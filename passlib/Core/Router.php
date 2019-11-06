<?php

    namespace PassMan\Core;

    /**
     * Router Class.
     *
     * This is the main Router aka Bootstrap class.
     * 
     */
    class Router {
        /**
         * The controller to be returned as the routing result.
         *
         * @static object
         */
        private static $controller;

        /**
         * The array of publicly available routes in "controller/action" format.
         * 
         * All actions NOT INCLUDED IN ARRAY require logging in.
         *
         * @static array
         */
        private static $publicAccess = array();

        private function __construct() {
        }

        /**
          * Returns controller for URL request.
          *
          * @param string $request a current application URL e.g. /home/index/3
          * 
          * @return object
          */
        public static function getController($request) {

            // INITIAL $request CLEAN-UP
            
            $req = explode("?", $request);                  // get rid of potential GET parametres
            $request = strtolower(trim($req[0], "/"));      // removing frond and end / from the request
                    
            // VALIDATOR / AUTHENTICATOR 

            $validRequest = self::validateRequest($request);
            // function returns an array of valid ["contr", "act", "param"] to be used
            
            //let's FINALLY try to create controller to be returned
            $controllerClass = "\\PassMan\\Controllers\\" . ucfirst($validRequest["contr"]) . "Controller";
            self::$controller = new $controllerClass($validRequest["act"], $validRequest["param"]);

            return self::$controller;
        }

        /**
          * Sets publicly available routes - all other routes require logging in.
          *
          * Recommended: Core\Router::allowPublicAccess("user/login", "user/auth", "user/register", "user/reguser", "user/forgot", "user/resend", "user/reset", "user/resetpass");
          *
          * @param string
          * 
          * @return void
          */
        public static function allowPublicAccess(...$routes) {
            foreach ( $routes as $route ) {
                if ( !in_array($route, self::$publicAccess) ) {
                    self::$publicAccess[] = $route;
                }
            }
        }

         /**
          * Validates request.
          *
          * Function checks if user logged in, SESSION time-out and if publicAccess granted.
          * Results in default /user/login enforcing visitor to log in OR
          * /home/index/1 for logged in users if controller and action for URL request do not exist
          *
          * @param string $request a current application URL e.g. /home/index/3
          * 
          * @return array Controller, Action and Params
          */
        private function validateRequest($request) {

            $reqExpl = explode("/", $request);
            $contr = $reqExpl[0];
            empty($reqExpl[1]) ?  $act = "index" : $act = $reqExpl[1];
            empty($reqExpl[2]) ?  $param = "1" : $param = $reqExpl[2]; 

            $action = $contr."/".$act;
            
            if ( Session::isLogged() ) {
                if ( !Session::isValid() ) {
                    Session::setMessage("Your session has expired.", "error");
                    $contr = "user";
                    $act = "login";
                    $param = "";
                }
                else {
                    if ( empty($request) || in_array($action, self::$publicAccess) ) { //disabling empty request or listed in $this->publicAccess
                        $contr = "home";
                        $act = "index";
                        $param = "1";
                    }                                                                                              

                    if ( !class_exists("\\PassMan\\Controllers\\" . ucfirst($contr) . "Controller") || !method_exists("\\PassMan\\Controllers\\" . ucfirst($contr) . "Controller", $act) ) {
                        $contr = "home";
                        $act = "index";
                        $param = "1";
                    }
                }
            }
            else if ( !in_array($action, self::$publicAccess) ) { // allowing publicly opened request listed in $this->publicAccess array
                $contr = "user";
                $act = "login";
                $param = "";
            }

            $details = array (
                "contr" => $contr,
                "act" => $act,
                "param" => $param
            );     

            //details - an array of contr, act, param
            return $details;
        }
    }