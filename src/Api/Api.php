<?php

namespace Fluxlabs\FluxRestApi\Api;

use Exception;
use Fluxlabs\FluxRestApi\Authorization\Authorization;
use Fluxlabs\FluxRestApi\Body\BodyDto;
use Fluxlabs\FluxRestApi\Body\BodyType;
use Fluxlabs\FluxRestApi\Body\FormDataBodyDto;
use Fluxlabs\FluxRestApi\Body\HtmlBodyDto;
use Fluxlabs\FluxRestApi\Body\JsonBodyDto;
use Fluxlabs\FluxRestApi\Body\TextBodyDto;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;
use Fluxlabs\FluxRestApi\Route\Collector\CombinedRouteCollector;
use Fluxlabs\FluxRestApi\Route\Collector\RouteCollector;
use Fluxlabs\FluxRestApi\Route\Collector\StaticRouteCollector;
use Fluxlabs\FluxRestApi\Route\GetRoutesRoute;
use Fluxlabs\FluxRestApi\Route\MatchedRouteDto;
use Fluxlabs\FluxRestApi\Route\Route;
use LogicException;
use Throwable;

class Api
{

    private ?Authorization $authorization;
    private ?array $docu_routes = null;
    private RouteCollector $route_collector;
    private ?array $routes = null;


    public static function new(RouteCollector $route_collector, ?Authorization $authorization = null) : /*static*/ self
    {
        $api = new static();

        $api->route_collector = CombinedRouteCollector::new([
            StaticRouteCollector::new([
                GetRoutesRoute::new(
                    fn() : array => $this->getRoutesDocu()
                )
            ]),
            /*FolderRouteCollector::new(
                __DIR__ . "/../../examples/routes"
            ),*/
            $route_collector
        ]);
        $api->authorization = $authorization;

        return $api;
    }


    public function handleRequest(RawRequestDto $request) : ResponseDto
    {
        $response = $this->handleAuthorization(
            $request
        );
        if ($response !== null) {
            return $response;
        }

        try {
            $route = $this->getMatchedRoute(
                $request,
                $this->collectRoutes()
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->toRawBody(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Route not found"
                    ),
                    404
                )
            );
        }

        try {
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
                500
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


    private function getMatchedRoute(RawRequestDto $request, array $routes) : MatchedRouteDto
    {
        $routes = array_filter(array_map(fn(Route $route) : ?MatchedRouteDto => $this->matchRoute($route, $request), $routes), fn(?MatchedRouteDto $route) : bool => $route !== null);

        if (empty($routes)) {
            throw new Exception("No route found for route " . $request->getRoute() . " and method " . $request->getMethod());
        }

        if (count($routes) > 1) {
            throw new LogicException("Multiple routes found for route " . $request->getRoute() . "and method " . $request->getMethod());
        }

        return current($routes);
    }


    private function getRoutesDocu() : array
    {
        $this->docu_routes ??= (function () : array {
            $routes = array_map(fn(Route $route) : array => [
                "route"     => $this->normalizeRoute($route->getRoute()),
                "method"    => $this->normalizeMethod($route->getMethod()),
                "body_type" => $route->getBodyType()
            ], $this->collectRoutes());

            usort($routes, fn(array $route1, array $route2) : int => strnatcasecmp($route1["route"], $route2["route"]));

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
            $this->authorization->authorize(
                $request
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->toRawBody(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Authorization needed"
                    ),
                    404,
                    $this->authorization->get401Headers()
                )
            );
        }

        return null;
    }


    private function handleRoute(MatchedRouteDto $route, RawRequestDto $request) : ResponseDto
    {
        try {
            $request = RequestDto::new(
                $request->getRoute(),
                $request->getMethod(),
                $request->getQuery(),
                $request->getBody(),
                $request->getHeaders(),
                $request->getCookies(),
                $route->getParams(),
                $this->parseBody(
                    $request->getHeader(
                        "Content-Type"
                    ),
                    $request->getBody(),
                    $request->getPost(),
                    $request->getFiles(),
                    $route->getRoute()->getBodyType()
                )
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return ResponseDto::new(
                null,
                400
            );
        }

        return $route->getRoute()->handle(
            $request
        );
    }


    private function log(Throwable $ex) : void
    {
        file_put_contents("php://stdout", $ex);
    }


    private function matchRoute(Route $route, RawRequestDto $request) : ?MatchedRouteDto
    {
        if ($this->normalizeMethod($route->getMethod()) !== $this->normalizeMethod($request->getMethod())) {
            return null;
        }

        $param_keys = [];
        $param_values = [];
        preg_match("/^" . preg_replace_callback("/\\\{([A-Za-z0-9-_]+)(\\\.)?\\\}/", function (array $matches) use (&$param_keys) {
                $param_keys[] = $matches[1];

                if (isset($matches[2]) && $matches[2] === "\\.") {
                    return "([A-Za-z0-9-_.\/]+)";
                } else {
                    return "([A-Za-z0-9-_.]+)";
                }
            }, preg_quote($this->normalizeRoute($route->getRoute()), "/")) . "$/", $this->normalizeRoute($request->getRoute()), $param_values);

        if (empty($param_values) || count($param_values) < 1) {
            return null;
        }

        array_shift($param_values);

        if (count($param_keys) !== count($param_values)) {
            throw new LogicException("Count of param keys and values are not the same");
        }

        return MatchedRouteDto::new(
            $route,
            array_combine(
                $param_keys,
                array_map([$this, "removeNormalizeRoute"], $param_values)
            )
        );
    }


    private function normalizeMethod(string $method) : string
    {
        return strtoupper($method);
    }


    private function normalizeRoute(string $route) : string
    {
        return "/" . $this->removeNormalizeRoute($route);
    }


    private function parseBody(?string $type, ?string $raw_body, array $post, array $files, ?string $route_body_type) : ?BodyDto
    {
        if ($route_body_type === null) {
            if (!empty($type) || !empty($raw_body)) {
                throw new Exception("Supports no body");
            }

            return null;
        }

        if (empty($type) || !str_contains($type, $route_body_type)) {
            throw new Exception("Body type is not " . $route_body_type);
        }

        switch ($route_body_type) {
            case BodyType::FORM_DATA:
                return FormDataBodyDto::new(
                    $post,
                    $files
                );

            case BodyType::JSON:
                $data = json_decode($raw_body);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg());
                }

                return JsonBodyDto::new(
                    $data
                );

            default:
                throw new Exception("Body type " . $route_body_type . " is not supported");
        }
    }


    private function removeNormalizeRoute(string $route) : string
    {
        return trim(preg_replace("/\.+/", ".", preg_replace("/\/+/", "/", $route)), "/");
    }


    private function toRawBody(ResponseDto $response) : ResponseDto
    {
        $body = $response->getBody();

        if ($response->getSendfile() !== null && ($body !== null || $response->getRawBody() !== null)) {
            throw new LogicException("Can't set both body and sendfile");
        }

        if ($body === null) {
            return $response;
        }

        if ($response->getRawBody() !== null) {
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
                "Content-Type" => $body->getType()
            ],
            $response->getCookies(),
            $response->getSendfile(),
            $raw_body
        );
    }
}
