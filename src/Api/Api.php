<?php

namespace Fluxlabs\FluxRestApi\Api;

use Fluxlabs\FluxRestApi\Body\BodyHelper;
use Fluxlabs\FluxRestApi\Body\Text\TextBodyDto;
use Fluxlabs\FluxRestApi\Config\Config;
use Fluxlabs\FluxRestApi\Log\LogHelper;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\RawResponseDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;
use Fluxlabs\FluxRestApi\Route\Fetcher\CombinedRoutesFetcher;
use Fluxlabs\FluxRestApi\Route\Fetcher\StaticRoutesFetcher;
use Fluxlabs\FluxRestApi\Route\GetRoutes\GetRoutesRoute;
use Fluxlabs\FluxRestApi\Route\Route;
use Fluxlabs\FluxRestApi\Route\RouteHelper;
use LogicException;
use Throwable;

class Api
{

    use BodyHelper;
    use LogHelper;
    use RouteHelper;

    private ?array $body_classes_of_routes = null;
    private Config $config;
    private ?array $docu_routes = null;
    private ?array $routes = null;


    public static function new(Config $config) : /*static*/ self
    {
        $api = new static();

        $api->config = $config;

        return $api;
    }


    public function handleRequest(RawRequestDto $request) : RawResponseDto
    {
        $response = $this->handleAuthorization(
            $request
        );
        if ($response !== null) {
            return $this->mapResponse(
                $response
            );
        }

        try {
            $request = $this->parseRequest(
                $request
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return RawResponseDto::new(
                400
            );
        }

        try {
            $route = $this->getMatchedRoute(
                $request,
                $this->fetchRoutes()
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return $this->mapResponse(
                ResponseDto::new(
                    TextBodyDto::new(
                        "Route not found"
                    ),
                    404
                )
            );
        }

        try {
            $response = $this->handleRoute(
                $route,
                $request
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return RawResponseDto::new(
                500
            );
        }

        return $this->mapResponse(
            $response
        );
    }


    private function fetchRoutes() : array
    {
        $this->routes ??= (function () : array {
            $routes = CombinedRoutesFetcher::new([
                StaticRoutesFetcher::new([
                    GetRoutesRoute::new(
                        fn() : array => $this->getRoutesDocu()
                    )
                ]),
                /*FolderRoutesFetcher::new(
                    __DIR__ . "/../../examples/routes"
                ),*/
                $this->config->getRoutesFetcher()
            ])
                ->fetchRoutes();

            usort($routes, fn(Route $route1, Route $route2) : int => strnatcasecmp($route2->getRoute(), $route1->getRoute()));

            return $routes;
        })();

        return $this->routes;
    }


    private function getBodyClassesOfRoutes() : array
    {
        $this->body_classes_of_routes ??= array_reduce($this->fetchRoutes(), function (array $body_classes, Route $route) : array {
            $body_class = $route->getBodyClass();

            if ($body_class !== null && !in_array($body_class, $body_classes)) {
                $body_classes[] = $body_class;
            }

            return $body_classes;
        }, []);

        return $this->body_classes_of_routes;
    }


    private function getRoutesDocu() : array
    {
        $this->docu_routes ??= (function () : array {
            $routes = array_map([$this, "getRouteDocu"], $this->fetchRoutes());

            usort($routes, fn(array $route1, array $route2) : int => strnatcasecmp($route1["route"], $route2["route"]));

            return $routes;
        })();

        return $this->docu_routes;
    }


    private function handleAuthorization(RawRequestDto $request) : ?ResponseDto
    {
        if ($this->config->getAuthorization() === null) {
            return null;
        }

        try {
            $this->config->getAuthorization()->authorize(
                $request
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return ResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                401,
                $this->config->getAuthorization()->get401Headers()
            );
        }

        return null;
    }


    private function mapResponse(ResponseDto $response) : RawResponseDto
    {
        try {
            if ($response->getBody() !== null && $response->getSendfile() !== null) {
                throw new LogicException("Can't set both body and sendfile");
            }

            $headers = $response->getHeaders();

            $body = $this->toRawBody(
                $response->getBody()
            );
            if ($body !== null) {
                $headers["Content-Type"] = $body->getType();
            }

            return RawResponseDto::new(
                $response->getStatus(),
                $body,
                $this->mapHeaders(
                    $headers
                ),
                $response->getCookies(),
                $response->getSendfile()
            );
        } catch (Throwable $ex) {
            $this->log(
                $ex
            );

            return RawResponseDto::new(
                500
            );
        }
    }


    private function parseRequest(RawRequestDto $request) : RequestDto
    {
        return RequestDto::new(
            $request->getRoute(),
            $request->getMethod(),
            null,
            $request->getQuery(),
            $this->parseBody(
                $request->getHeader("Content-Type"),
                $request->getBody(),
                $this->getBodyClassesOfRoutes()
            ),
            $this->mapHeaders(
                $request->getHeaders()
            ),
            $request->getCookies()
        );
    }
}
