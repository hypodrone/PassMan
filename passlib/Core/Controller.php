<?php

	namespace PassMan\Core;

    /**
     * Base Controller Class.
     *
     * This is controller's base abstract class providing standard functionalities.
     */
    abstract class Controller {

        /**
         * Controller's action for the route.
         *
         * @var string $action Part of URL request: /controller/action/param
         */
        protected $action;

        /**
         * Controller's parameter for the route.
         *
         * @var string $param Part of URL request: /controller/action/param
         */
        protected $param;

        /**
         * Class constructor
         *
         * @param string $action Controller's action
         * @param string $param (optional=null) Controller's parameter
         * @param string $flags (optional=null) Controller's flags - currently not in use FUTURE DEVELOPMENT
         *
         * @return void
         */
        public function __construct($action, $param = null, $flags = null) {
            $this->action = $action;
            $this->param = $param;
        }

        /**
         * Used to establish view in displayView function.
         *
         * Quicker than preg_match("/\\\PassMan\\\Controller\\\(.*)Controller/", $a, $match); - tested...
         * 
         * @return string Controller's name
         */
        private function getName() {
            $name = static::class;
            $name = str_replace("PassMan\\", "", $name);
            $name = str_replace("Controllers\\", "", $name);
            $name = str_replace("Controller", "", $name);
            return $name;
        }

        /**
         * Executes chosen action with paremeter.
         *
         * Based on URL: /controller/action/parameter
         * 
         * @return mixed Returns function action with parameter(optional) for the controller
         */
        public function execute() {
            return $this->param == null ? 
                $this->{$this->action}() : 
                $this->{$this->action}($this->param);
        }

        /**
         * Displays (require file) view
         * 
         * @uses $this->getName() to display appropriate view
         *
         * @param string $view View to display
         * @param array $model (optional=null) - array returned by the model
         * @param bool $isParent (optional=fales) - determines if view is Parent (not wrapped in any ) or child view (that will be wrapped in lib/Views/main.php)
         *
         * @return void
         */
        protected function displayView($view, $model = null, $isParent = false) {
            $view = "../passlib/Views/" . $this->getName() . "/" . $view . ".php"; // this will be used in the VIEW in the REQUIRE!!!
            if ($isParent) {
                require_once($view);    // meaning there is a stand alone view that does not need showing $view in it
            }
            else {
                require_once("../passlib/Views/main.php"); // and inside main.php it uses $view to require another!
            }
        }

        /**
         * Redirects to /controller/action/param address - header("Location:$url");.
         * 
         * @param string $controller Controller to redirect to
         * @param array $action (optional=null) action
         * @param bool $param (optional=null) param
         *
         * @return void
         */
        protected function redirect($controller, $action = null, $param = null) {
            $controller = trim($controller, "/");
            $action = trim($action, "/");
            $param = trim($param, "/");
            
            $url = "/$controller";
            if (!empty($action)) {
                $url .= "/$action";
            }
            if (!empty($param)) {
                $url .= "/$param";
            }
            header("Location:$url");
        }
    }