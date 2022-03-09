<?php

namespace FluxRestApi\Adapter\Api;

use Exception;
use FluxRestApi\Adapter\Collector\CombinedRouteCollector;
use FluxRestApi\Adapter\Route\GetRoutesRoute;
use FluxRestApi\Authorization\Authorization;
use FluxRestApi\Body\BodyDto;
use FluxRestApi\Body\FormDataBodyDto;
use FluxRestApi\Body\HtmlBodyDto;
use FluxRestApi\Body\JsonBodyDto;
use FluxRestApi\Body\TextBodyDto;
use FluxRestApi\Collector\RouteCollector;
use FluxRestApi\Libs\FluxRestBaseApi\Body\BodyType;
use FluxRestApi\Libs\FluxRestBaseApi\Body\LegacyDefaultBodyType;
use FluxRestApi\Libs\FluxRestBaseApi\Header\LegacyDefaultHeader;
use FluxRestApi\Libs\FluxRestBaseApi\Method\CustomMethod;
use FluxRestApi\Libs\FluxRestBaseApi\Method\LegacyDefaultMethod;
use FluxRestApi\Libs\FluxRestBaseApi\Status\LegacyDefaultStatus;
use FluxRestApi\Log\Log;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\MatchedRouteDto;
use FluxRestApi\Route\Route;
use FluxRestApi\Server\LegacyDefaultServer;
use LogicException;
use Throwable;

class RestApi
{

    use Log;

    private ?Authorization $authorization;
    private array $docu_routes;
    private RouteCollector $route_collector;
    private array $routes;


    private function __construct(
        /*private readonly*/ RouteCollector $route_collector,
        /*private readonly*/ ?Authorization $authorization
    ) {
        $this->route_collector = CombinedRouteCollector::new(
            [
                GetRoutesRoute::new(
                    fn() : array => $this->getRoutesDocu()
                ),
                $route_collector
            ]
        );
        $this->authorization = $authorization;
    }


    public static function new(
        RouteCollector $route_collector,
        ?Authorization $authorization = null
    ) : /*static*/ self
    {
        return new static(
        /*CombinedRouteCollector::new(
            [
                FolderRouteCollector::new(
                    __DIR__ . "/../../examples/routes"
                ),*/
            $route_collector
            /*]
        )*/,
            $authorization
        );
    }


    public function handleRequest(RawRequestDto $request) : ResponseDto
    {
        try {
            $request = $this->handleMethodOverride(
                $request
            );
            if ($request instanceof ResponseDto) {
                return $request;
            }

            $response = $this->handleAuthorization(
                $request
            );
            if ($response !== null) {
                return $response;
            }

            $route = $this->getMatchedRoute(
                $request,
                $this->collectRoutes()
            );
            if ($route instanceof ResponseDto) {
                return $route;
            }

            return $this->toRawBody(
                $this->handleRoute(
                    $route,
                    $request
                )
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return ResponseDto::new(
                null,
                LegacyDefaultStatus::_500()
            );
        }
    }


    private function collectRoutes() : array
    {
        $this->routes ??= (function () : array {
            $routes = $this->route_collector->collectRoutes();

            usort($routes, fn(Route $route1, Route $route2) : int => strnatcasecmp($route2->getRoute(), $route1->getRoute()));

            return $routes;
        })();

        return $this->routes;
    }


    private function getMatchedRoute(RawRequestDto $request, array $routes)/* : MatchedRouteDto|ResponseDto*/
    {
        try {
            if (($request->getRoute()[0] ?? null) !== "/") {
                throw new LogicException("Invalid route format " . $request->getRoute());
            }

            $routes = array_filter(array_map(fn(Route $route) : ?MatchedRouteDto => $this->matchRoute(
                $route,
                $request
            ), $routes), fn(?MatchedRouteDto $route) : bool => $route !== null);

            if (empty($routes)) {
                return $this->toRawBody(
                    ResponseDto::new(
                        TextBodyDto::new(
                            "Route not found"
                        ),
                        LegacyDefaultStatus::_404()
                    )
                );
            }

            $routes = array_filter($routes,
                fn(MatchedRouteDto $route) : bool => $route->getRoute()->getMethod()->value === $request->getMethod()->value);

            if (empty($routes)) {
                return $this->toRawBody(
                    ResponseDto::new(
                        TextBodyDto::new(
                            "Invalid method"
                        ),
                        LegacyDefaultStatus::_405()
                    )
                );
            }

            if (count($routes) > 1) {
                throw new LogicException("Multiple routes found for route " . $request->getRoute() . " and method " . $request->getMethod()->value);
            }

            return current($routes);
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->toRawBody(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Invalid route"
                    ),
                    LegacyDefaultStatus::_400()
                )
            );
        }
    }


    private function getRoutesDocu() : array
    {
        $this->docu_routes ??= (function () : array {
            $routes = array_map(fn(Route $route) : array => [
                "route"        => $this->normalizeRoute(
                    $route->getRoute()
                ),
                "method"       => $route->getMethod()->value,
                "query_params" => $this->normalizeDocuArray(
                    $route->getDocuRequestQueryParams()
                ),
                "body_types"   => $this->normalizeDocuArray(
                    array_map(fn(BodyType $body_type) : string => $body_type->value, $route->getDocuRequestBodyTypes() ?? [])
                )
            ], $this->collectRoutes());

            usort($routes, function (array $route1, array $route2) : int {
                $sort = strnatcasecmp($route1["route"], $route2["route"]);
                if ($sort !== 0) {
                    return $sort;
                }

                return strnatcasecmp($route1["method"], $route2["method"]);
            });

            return $routes;
        })();

        return $this->docu_routes;
    }


    private function handleAuthorization(RawRequestDto $request) : ?ResponseDto
    {
        if ($this->authorization === null) {
            return null;
        }

        try {
            $response = $this->authorization->authorize(
                $request
            );
            if ($response !== null) {
                return $this->toRawBody(
                    $response
                );
            }
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->toRawBody(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Invalid authorization"
                    ),
                    LegacyDefaultStatus::_403()
                )
            );
        }

        return null;
    }


    private function handleMethodOverride(RawRequestDto $request)/* : RawRequestDto|ResponseDto*/
    {
        $method_override = $request->getHeader(
            LegacyDefaultHeader::X_HTTP_METHOD_OVERRIDE()->value
        );

        if ($method_override === null) {
            return $request;
        }

        try {
            if ($request->getServer()->value !== LegacyDefaultServer::NGINX()->value) {
                throw new Exception("Method overriding not enabled/needed for server " . $request->getServer()->value);
            }

            $method_override = CustomMethod::factory($method_override);

            if ($request->getMethod()->value !== LegacyDefaultMethod::POST()->value) {
                throw new Exception("Method overriding only for " . LegacyDefaultMethod::POST()->value);
            }

            if (!in_array($method_override->value, [LegacyDefaultMethod::DELETE()->value, LegacyDefaultMethod::PATCH()->value, LegacyDefaultMethod::PUT()->value])) {
                throw new Exception("Method overriding with " . $method_override->value . " not supported");
            }

            return RawRequestDto::new(
                $request->getRoute(),
                $method_override,
                $request->getServer(),
                $request->getQueryParams(),
                $request->getBody(),
                $request->getPost(),
                $request->getFiles(),
                $request->getHeaders(),
                $request->getCookies()
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->toRawBody(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Invalid method"
                    ),
                    LegacyDefaultStatus::_405()
                )
            );
        }
    }


    private function handleRoute(MatchedRouteDto $route, RawRequestDto $request) : ResponseDto
    {
        try {
            $request = RequestDto::new(
                $request->getRoute(),
                $request->getMethod(),
                $request->getServer(),
                $request->getQueryParams(),
                $request->getBody(),
                $request->getHeaders(),
                $request->getCookies(),
                $route->getParams(),
                $this->parseBody(
                    $request->getHeader(
                        LegacyDefaultHeader::CONTENT_TYPE()->value
                    ),
                    $request->getBody(),
                    $request->getPost(),
                    $request->getFiles()
                )
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return ResponseDto::new(
                TextBodyDto::new(
                    "Invalid body"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        return $route->getRoute()->handle(
                $request
            ) ?? ResponseDto::new();
    }


    private function matchRoute(Route $route, RawRequestDto $request) : ?MatchedRouteDto
    {
        $param_keys = [];
        $param_values = [];
        preg_match("/^" . preg_replace_callback("/\\\{([A-Za-z0-9-_]+)(\\\\\.)?\\\}/", function (array $matches) use (&$param_keys) {
                $param_keys[] = $matches[1];

                if (isset($matches[2]) && $matches[2] === "\\.") {
                    return "([A-Za-z0-9-_.\/]+)";
                } else {
                    return "([A-Za-z0-9-_.]+)";
                }
            }, preg_quote($this->normalizeRoute(
                $route->getRoute()
            ), "/")) . "$/", $this->normalizeRoute(
            $request->getRoute()
        ), $param_values);

        if (empty($param_values) || count($param_values) < 1) {
            return null;
        }

        array_shift($param_values);

        if (count($param_keys) !== count($param_values)) {
            throw new LogicException("Count of param keys and values are not the same");
        }

        return MatchedRouteDto::new(
            $route,
            array_combine($param_keys, array_map([$this, "removeNormalizeRoute"], $param_values))
        );
    }


    private function normalizeDocuArray(?array $array) : ?array
    {
        if (empty($array)) {
            return null;
        }

        $array = array_filter(array_values(array_map("trim", $array)));
        if (empty($array)) {
            return null;
        }

        natcasesort($array);

        return $array;
    }


    private function normalizeRoute(string $route) : string
    {
        return "/" . $this->removeNormalizeRoute(
                $route
            );
    }


    private function parseBody(?string $type, ?string $raw_body, array $post, array $files) : ?BodyDto
    {
        if (empty($type)) {
            return null;
        }

        switch (true) {
            case str_contains($type, LegacyDefaultBodyType::FORM_DATA()->value):
                return FormDataBodyDto::new(
                    $post,
                    $files
                );

            case str_contains($type, LegacyDefaultBodyType::HTML()->value):
                return HtmlBodyDto::new(
                    $raw_body ?? ""
                );

            case str_contains($type, LegacyDefaultBodyType::JSON()->value):
                $data = json_decode($raw_body);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }

                return JsonBodyDto::new(
                    $data
                );

            case str_contains($type, LegacyDefaultBodyType::TEXT()->value):
                return TextBodyDto::new(
                    $raw_body ?? ""
                );

            default:
                return null;
        }
    }


    private function removeNormalizeRoute(string $route) : string
    {
        return trim(preg_replace("/\.+/", ".", preg_replace("/\/+/", "/", $route)), "/");
    }


    private function toRawBody(ResponseDto $response) : ResponseDto
    {
        $body = $response->getBody();
        $raw_body = $response->getRawBody();

        if ($response->getSendfile() !== null && ($body !== null || $raw_body !== null)) {
            throw new LogicException("Can't set both body and sendfile");
        }

        if ($body === null) {
            return $response;
        }

        if ($raw_body !== null) {
            throw new LogicException("Can't set both body and raw body");
        }

        switch (true) {
            case $body instanceof HtmlBodyDto:
                $raw_body = $body->getHtml();
                break;

            case $body instanceof JsonBodyDto:
                $raw_body = json_encode($body->getData(), JSON_UNESCAPED_SLASHES);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }
                break;

            case $body instanceof TextBodyDto:
                $raw_body = $body->getText();
                break;

            default:
                throw new Exception("Body type " . $body->getType()->value . " is not supported");
        }

        return ResponseDto::new(
            null,
            $response->getStatus(),
            $response->getHeaders() + [
                LegacyDefaultHeader::CONTENT_TYPE()->value => $body->getType()->value
            ],
            $response->getCookies(),
            $response->getSendfile(),
            $raw_body
        );
    }
}
