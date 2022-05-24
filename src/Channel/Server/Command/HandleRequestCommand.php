<?php

namespace FluxRestApi\Channel\Server\Command;

use FluxRestApi\Adapter\Authorization\Authorization;
use FluxRestApi\Adapter\Body\RawBodyDto;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Header\LegacyDefaultHeaderKey;
use FluxRestApi\Adapter\Route\Collector\CombinedRouteCollector;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Channel\Body\Port\BodyService;
use FluxRestApi\Channel\Server\Route\GetRoutesRoute;
use FluxRestApi\Channel\Server\Route\MatchedRouteDto;
use LogicException;
use Throwable;

class HandleRequestCommand
{

    private ?Authorization $authorization;
    private BodyService $body_service;
    private array $docu_routes;
    private RouteCollector $route_collector;
    /**
     * @var Route[]
     */
    private array $routes;


    private function __construct(
        /*private readonly*/ BodyService $body_service,
        /*private readonly*/ RouteCollector $route_collector,
        /*private readonly*/ ?Authorization $authorization
    ) {
        $this->body_service = $body_service;
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
        BodyService $body_service,
        RouteCollector $route_collector,
        ?Authorization $authorization = null
    ) : /*static*/ self
    {
        return new static(
            $body_service,
            /*CombinedRouteCollector::new(
                [
                    FolderRouteCollector::new(
                        __DIR__ . "/../../../../examples/routes"
                    ),
                    */ $route_collector/*
                ]
            )*/,
            $authorization
        );
    }


    public function handleRequest(ServerRawRequestDto $request) : ServerRawResponseDto
    {
        try {
            $request = $this->body_service->handleMethodOverride(
                    $request
                ) ?? $request;
            if ($request instanceof ServerResponseDto) {
                return $this->toRawResponse(
                    $request
                );
            }

            $response = $this->handleAuthorization(
                $request
            );
            if ($response !== null) {
                return $this->toRawResponse(
                    $response
                );
            }

            $route = $this->getMatchedRoute(
                $request,
                $this->collectRoutes()
            );
            if ($route instanceof ServerResponseDto) {
                return $this->toRawResponse(
                    $route
                );
            }

            return $this->toRawResponse(
                $this->handleRoute(
                    $route,
                    $request
                )
            );
        } catch (Throwable $ex) {
            file_put_contents("php://stdout", $ex);

            return $this->toRawResponse(
                ServerResponseDto::new(
                    null,
                    LegacyDefaultStatus::_500()
                )
            );
        }
    }


    /**
     * @return Route[]
     */
    private function collectRoutes() : array
    {
        $this->routes ??= (function () : array {
            $routes = $this->route_collector->collectRoutes();

            usort($routes, fn(Route $route1, Route $route2) : int => strnatcasecmp($route2->getRoute(), $route1->getRoute()));

            return $routes;
        })();

        return $this->routes;
    }


    /**
     * @param Route[] $routes
     */
    private function getMatchedRoute(ServerRawRequestDto $request, array $routes)/* : MatchedRouteDto|ServerResponseDto*/
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
                return ServerResponseDto::new(
                    TextBodyDto::new(
                        "Route not found"
                    ),
                    LegacyDefaultStatus::_404()
                );
            }

            $routes = array_filter($routes,
                fn(MatchedRouteDto $route) : bool => $route->getRoute()->getMethod()->value === $request->getMethod()->value);

            if (empty($routes)) {
                return ServerResponseDto::new(
                    TextBodyDto::new(
                        "Invalid method"
                    ),
                    LegacyDefaultStatus::_405()
                );
            }

            if (count($routes) > 1) {
                throw new LogicException("Multiple routes found for route " . $request->getRoute() . " and method " . $request->getMethod()->value);
            }

            return current($routes);
        } catch (Throwable $ex) {
            file_put_contents("php://stdout", $ex);

            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid route"
                ),
                LegacyDefaultStatus::_400()
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


    private function handleAuthorization(ServerRawRequestDto $request) : ?ServerResponseDto
    {
        if ($this->authorization === null) {
            return null;
        }

        return $this->authorization->authorize(
            $request
        );
    }


    private function handleRoute(MatchedRouteDto $route, ServerRawRequestDto $request) : ServerResponseDto
    {
        try {
            $request = ServerRequestDto::new(
                $request->getRoute(),
                $request->getMethod(),
                $request->getServerType(),
                $request->getQueryParams(),
                $request->getBody(),
                $request->getHeaders(),
                $request->getCookies(),
                $route->getParams(),
                $this->body_service->parseBody(
                    RawBodyDto::new(
                        $request->getHeader(
                            LegacyDefaultHeaderKey::CONTENT_TYPE()
                        ),
                        $request->getBody()
                    ),
                    $request->getPost(),
                    $request->getFiles()
                )
            );
        } catch (Throwable $ex) {
            file_put_contents("php://stdout", $ex);

            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid body"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        return $route->getRoute()->handle(
                $request
            ) ?? ServerResponseDto::new();
    }


    private function matchRoute(Route $route, ServerRawRequestDto $request) : ?MatchedRouteDto
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


    private function removeNormalizeRoute(string $route) : string
    {
        return trim(preg_replace("/\.+/", ".", preg_replace("/\/+/", "/", $route)), "/");
    }


    private function toRawResponse(ServerResponseDto $response) : ServerRawResponseDto
    {
        if ($response->getSendfile() !== null && ($response->getBody() !== null || $response->getRawBody() !== null)) {
            throw new LogicException("Can't set both body and sendfile");
        }

        if ($response->getBody() === null) {
            return ServerRawResponseDto::new(
                $response->getRawBody(),
                $response->getStatus(),
                $response->getHeaders(),
                $response->getCookies(),
                $response->getSendfile()
            );
        }

        if ($response->getRawBody() !== null) {
            throw new LogicException("Can't set both body and raw body");
        }

        $raw_body = $this->body_service->toRawBody(
            $response->getBody()
        );

        return ServerRawResponseDto::new(
            $raw_body->getBody(),
            $response->getStatus(),
            $response->getHeaders() + [
                LegacyDefaultHeaderKey::CONTENT_TYPE()->value => $raw_body->getType()
            ],
            $response->getCookies(),
            $response->getSendfile()
        );
    }
}
