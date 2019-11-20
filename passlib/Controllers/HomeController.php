<?php

namespace Passlib\Controllers;

/**
 * Home Controller Class.
 *
 * This is home controller's class extending Controller.
 * Each method represents action (/controller/action/params) and MUST result in DISPLAYING A VIEW ( e.g. $this->displayView('login'); )
 * or REDIRECT ( e.g. $this->redirect('user/login'); )
 *
 */
class HomeController extends \Passlib\Core\Controller {

    /**
     * HomeController's model.
     *     
     * @param object $model Model used by controller's methods
     */
    private $model;

    /**
     * Class constructor
     *
     * Assigning appropriate Model to be used by class' methods
     * 
     * * @param string $action Request's action to be called
     * * @param string $param Request's parameter to be used
     * * @param string $flags CURRENTLY NOT IN USE
     *
     * @return void
     */
    public function __construct($action, $param = null, $flags = null) {
        parent::__construct($action, $param, $flags);
        $this->model = new \Passlib\Models\HomeModel(); 
    }

    /**
     * Executes "index" action.
     *
     * Calls HomeModel's "index" method and displays "index" view - the main view for the application for logged in users.
     *
     * @return void
     */
    protected function index() {   
        $data = $this->model->index($this->param);
        $this->displayView('index', $data);
    }

    /**
     * Executes "add" action.
     *
     * The function handles adding new passwords. Calls HomeModel's "add" method and depending on the stage sets appropriate message and then redirects to "/home".
     *
     * @uses $this->model->add() to process $_POST results
     *
     * @return void
     */
    protected function add() {
        // add password to database
        if ($this->model->add()=="success" ) {
            \Passlib\Core\Session::setMessage("Password added.", "success");
        }
        else if ($this->model->add()=="passexists" ) {
            \Passlib\Core\Session::setMessage("Password for this service already exists.", "error");
        }
        else if ($this->model->add()=="noservice" ) {
            \Passlib\Core\Session::setMessage("Service name is required.", "error");
        }
        $this->redirect('home');
    }

    /**
     * Executes "modify" action.
     *
     * The function handles services / passwords modifications. Calls HomeModel's "modify" method and depending on the stage sets appropriate message and then redirects to "/home".
     *
     * @uses $this->model->modify() to process $_POST results
     *
     * @return void
     */
    protected function modify() {
        if ($this->model->modify()=="updated" ) {
            \Passlib\Core\Session::setMessage("Password updated.", "success");
        }
        else if ($this->model->modify()=="noservice" ) {
            \Passlib\Core\Session::setMessage("Service name required.", "error");
        }
        else if ($this->model->modify()=="deleted" ) {
            \Passlib\Core\Session::setMessage("Password deleted.", "success");
        }
        else {
            \Passlib\Core\Session::setMessage("General error.", "error");
        }
        $this->redirect('home');
    }
}