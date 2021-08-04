<?php

namespace Fluxlabs\FluxRestApi\Route;

use Exception;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;
use LogicException;

trait RouteHelper
{

    private function getMatchedRoute(RequestDto $request, array $routes) : MatchedRouteDto
    {
        $routes = array_filter(array_map(fn(Route $route) : ?MatchedRouteDto => $this->matchRoute($route, $request), $routes), fn(?MatchedRouteDto $route) : bool => $route !== null);

        if (empty($routes)) {
            throw new Exception("No route found for route=" . $request->getRoute() . ", method=" . $request->getMethod() . ", body=" . $request->getBody());
        }

        if (count($routes) > 1) {
            throw new Exception("Multiple routes found for route=" . $request->getRoute() . ", method=" . $request->getMethod() . ", body=" . $request->getBody());
        }

        return current($routes);
    }


    private function getRouteDocu(Route $route) : array
    {
        $body_class = $route->getBodyClass();

        return [
            "route"     => "/" . trim($route->getRoute(), "/"),
            "method"    => $route->getMethod(),
            "body_type" => $body_class !== null && !is_a($body_class, RawBodyDto::class, true) ? $this->getBodyParser(
                $body_class
            )::getType() : null
        ];
    }


    private function handleRoute(MatchedRouteDto $route, RequestDto $request) : ResponseDto
    {
        return $route->getRoute()->handle(
            RequestDto::new(
                $request->getRoute(),
                $request->getMethod(),
                $route->getParams(),
                $request->getQuery(),
                $request->getBody(),
                $request->getHeaders(),
                $request->getCookies()
            )
        );
    }


    private function matchRoute(Route $route, RequestDto $request) : ?MatchedRouteDto
    {
        $method = $route->getMethod();
        $body_class = $route->getBodyClass();

        if (!(
            (
                $method === null
                || strtoupper($request->getMethod() === strtoupper($method))
            )
            && (
                $body_class === null
                || $request->getBody() instanceof $body_class
            )
        )
        ) {
            return null;
        }

        $param_keys = [];
        $param_values = [];
        preg_match("/^\/" . preg_replace_callback("/\\\{([A-Za-z0-9-_]+)\\\}/", function (array $matches) use (&$param_keys) {
                $param_keys[] = $matches[1];

                return "([A-Za-z0-9-_]+)";
            }, preg_quote(trim($route->getRoute(), "/"), "/")) . "\/?$/", $request->getRoute(),
            $param_values);

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
                $param_values
            )
        );
    }
}
