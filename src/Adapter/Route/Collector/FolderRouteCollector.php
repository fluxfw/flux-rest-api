<?php

namespace FluxRestApi\Adapter\Route\Collector;

use FluxRestApi\Adapter\Route\Route;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FolderRouteCollector implements RouteCollector
{

    private array $arguments;
    private string $folder;
    /**
     * @var string[]
     */
    private array $route_classes;


    private function __construct(
        /*private readonly*/ string $folder,
        /*private readonly*/ array $arguments
    ) {
        $this->folder = $folder;
        $this->arguments = $arguments;
    }


    public static function new(
        string $folder,
        ?array $arguments = null
    ) : /*static*/ self
    {
        return new static(
            $folder,
            $arguments ?? []
        );
    }


    public function collectRoutes() : array
    {
        $this->route_classes ??= [];
        $routes = [];

        if (!file_exists($this->folder)) {
            return $routes;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->folder, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if (str_ends_with($file->getPathName(), "/autoload.php")) {
                require_once $file->getPathName();

                continue;
            }

            if (!str_ends_with($file->getFileName(), "Route.php")) {
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

            $routes[] = [$route_class, "new"](...$this->arguments);
        }

        return $routes;
    }
}
