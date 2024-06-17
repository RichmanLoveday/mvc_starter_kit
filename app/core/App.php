<?php

declare(strict_types=1);

namespace App\core;

use app\core\Controller;

class App
{
    protected $controller = 'HomeController';  // Default controller
    protected $method = 'index';  // Default method
    private $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // show($url);
        // die;

        if (isset($url[0])) {
            $firstPath = ucwords($url[0]);
            $secondPath = isset($url[1]) ? ucwords($url[1]) : ucwords($url[0]);
            $controllerPathWithFolder = "../app/Controllers/{$firstPath}/{$secondPath}.php";
            $controllerPathWithoutFolder = "../app/Controllers/{$firstPath}.php";

            if (file_exists($controllerPathWithFolder)) {
                require $controllerPathWithFolder;
                $controllerClass = "App\\Controllers\\{$firstPath}\\" . $secondPath;
                $this->controller = new $controllerClass();

                // Handle method
                if (isset($url[2])) {
                    if (method_exists($this->controller, $url[2])) {

                        $this->method = strtolower($url[2]);
                        unset($url[2]);
                    } else {
                        $this->show404();
                        return;
                    }
                }

                unset($url[0], $url[1]);
            } elseif (file_exists($controllerPathWithoutFolder)) {
                require $controllerPathWithoutFolder;
                $controllerClass = "App\\Controllers\\" . $firstPath;
                $this->controller = new $controllerClass();

                // Handle method
                if (isset($url[1])) {
                    if (method_exists($this->controller, $url[1])) {

                        $this->method = strtolower($url[1]);
                        unset($url[1]);
                    } else {
                        $this->show404();
                        return;
                    }
                }

                unset($url[0]);
            } else {
                $this->show404();
                return;
            }
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function getUrl()
    {
        $url = isset($_GET['url']) ? filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL) : null;
        return $url ? explode('/', $url) : [];
    }

    private function show404()
    {
        $controller = new Controller();
        $controller->view('404', ['pageTitle' => null]);
        exit;
    }
}
