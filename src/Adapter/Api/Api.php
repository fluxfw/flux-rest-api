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
use FluxRestApi\Log\Log;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\MatchedRouteDto;
use FluxRestApi\Route\Route;
use FluxRestApi\Server\Server;
use FluxRestBaseApi\Body\BodyType;
use FluxRestBaseApi\Header\Header;
use FluxRestBaseApi\Method\Method;
use FluxRestBaseApi\Status\Status;
use LogicException;
use Throwable;

class Api
{

    use Log;

    private ?Authorization $authorization;
    private ?array $docu_routes = null;
    private RouteCollector $route_collector;
    private ?array $routes = null;


    public static function new(RouteCollector $route_collector, ?Authorization $authorization = null) : /*static*/ self
    {
        $api = new static();

        $api->route_collector = CombinedRouteCollector::new(
            [
                GetRoutesRoute::new(
                    fn() : array => $api->getRoutesDocu()
                ),
                /*FolderRouteCollector::new(
                    __DIR__ . "/../../examples/routes"
                ),*/
                $route_collector
            ]
        );
        $api->authorization = $authorization;

        return $api;
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
                Status::_500
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
                        Status::_404
                    )
                );
            }

            $routes = array_filter($routes, fn(MatchedRouteDto $route) : bool => $this->normalizeMethod(
                    $route->getRoute()->getMethod()
                ) === $this->normalizeMethod(
                    $request->getMethod()
                ));

            if (empty($routes)) {
                return $this->toRawBody(
                    ResponseDto::new(
                        TextBodyDto::new(
                            "Invalid method"
                        ),
                        Status::_405
                    )
                );
            }

            if (count($routes) > 1) {
                throw new LogicException("Multiple routes found for route " . $request->getRoute() . " and method " . $request->getMethod());
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
                    Status::_400
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
                "method"       => $this->normalizeMethod(
                    $route->getMethod()
                ),
                "query_params" => $this->normalizeDocuArray(
                    $route->getDocuRequestQueryParams()
                ),
                "body_types"   => $this->normalizeDocuArray(
                    $route->getDocuRequestBodyTypes()
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
                    Status::_403
                )
            );
        }

        return null;
    }


    private function handleMethodOverride(RawRequestDto $request)/* : RawRequestDto|ResponseDto*/
    {
        $method_override = $request->getHeader(
            Header::X_HTTP_METHOD_OVERRIDE
        );

        if ($method_override === null) {
            return $request;
        }

        try {
            if ($request->getServer() !== Server::NGINX) {
                throw new Exception("Method overriding not enabled/needed for server " . $request->getServer());
            }

            $method_override = $this->normalizeMethod(
                $method_override
            );

            if ($this->normalizeMethod(
                    $request->getMethod()
                ) !== Method::POST
            ) {
                throw new Exception("Method overriding only for " . Method::POST);
            }

            if (!in_array($method_override, [Method::DELETE, Method::PATCH, Method::PUT])) {
                throw new Exception("Method overriding with " . $method_override . " not supported");
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
                    Status::_405
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
                        Header::CONTENT_TYPE
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
                Status::_400
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


    private function normalizeMethod(string $method) : string
    {
        return strtoupper($method);
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
            case str_contains($type, BodyType::FORM_DATA):
                return FormDataBodyDto::new(
                    $post,
                    $files
                );

            case str_contains($type, BodyType::HTML):
                return HtmlBodyDto::new(
                    $raw_body ?? ""
                );

            case str_contains($type, BodyType::JSON):
                $data = json_decode($raw_body);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }

                return JsonBodyDto::new(
                    $data
                );

            case str_contains($type, BodyType::TEXT):
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
                throw new Exception("Body type " . $body->getType() . " is not supported");
        }

        return ResponseDto::new(
            null,
            $response->getStatus(),
            $response->getHeaders() + [
                Header::CONTENT_TYPE => $body->getType()
            ],
            $response->getCookies(),
            $response->getSendfile(),
            $raw_body
        );
    }
}
