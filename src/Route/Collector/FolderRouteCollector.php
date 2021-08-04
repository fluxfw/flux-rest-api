<?php

namespace Fluxlabs\FluxRestApi\Route\Collector;

use Fluxlabs\FluxRestApi\Route\Route;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FolderRouteCollector implements RouteCollector
{

    private string $folder;
    private array $route_classes = [];


    public static function new(string $folder) : /*static*/ self
    {
        $collector = new static();

        $collector->folder = $folder;

        return $collector;
    }


    public function collectRoutes() : array
    {
        $routes = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->folder, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFileName(), ".php")) {
                continue;
            }

            if (!isset($this->route_classes[$file->getPathName()])) {
                // TODO: Throwable not work

                /*$route_class = null;
                try {
                    require $file->getPathName();
                } catch (Throwable $ex) {
                    $matches = [];
                    preg_match("/^Cannot declare class (.+), because the name is already in use$/", $ex->getMessage(), $matches);
                    if (count($matches) > 1) {
                        $route_class = $matches[1];
                    } else {
                        throw $ex;
                    }
                }

                if ($route_class === null) {
                    $all_classes = get_declared_classes();
                    $route_class = end($all_classes);
                }

                $this->route_classes[$file->getPathName()] = $route_class;*/

                require_once $file->getPathName();
                $all_classes = get_declared_classes();
                $this->route_classes[$file->getPathName()] = $route_class = end($all_classes);
            } else {
                $route_class = $this->route_classes[$file->getPathName()];
            }

            if (!class_exists($route_class) || !is_a($route_class, Route::class, true) || !method_exists($route_class, "new")) {
                continue;
            }

            $routes[] = [$route_class, "new"]();
        }

        return $routes;
    }
}
