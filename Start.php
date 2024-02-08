<?php

use Libraries\Response;


class Start
{
    
    /**
     * init
     *
     * @throws \Exception
     * @return void
     */
    public function init()
    {
        
        try {
            if($_SERVER['REQUEST_METHOD']!="GET"){
                throw new \Exception("Method Not Found");
            }
            if (!isset($_GET['param'])) {
                throw new \Exception("Unauthorized");
            }
            $controller = explode("/", $_GET['param']);
            $class_name = "\\Controllers\\AuthController";

            if (!class_exists($class_name)) {
                throw new \Exception("Controller Not Found");
            }
            $class = new $class_name();
            if (!method_exists($class, "index")) {
                throw new \Exception("Method Not Found");
            }
            $params_url = $controller;
            unset($params_url[0], $params_url[1]);
            $class->index(array_values($params_url));
        } catch (\Exception $e) {
            Response::sendResponse(404, ["msg" => $e->getMessage()]);
        }
    }
    
}
