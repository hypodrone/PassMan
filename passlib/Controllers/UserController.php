<?php

namespace PassMan\Controllers;

/**
 * User Controller Class.
 *
 * This is user controller's class extending Controller. 
 * Each method represents action (/controller/action/params) and MUST result in DISPLAYING A VIEW ( e.g. $this->displayView('login'); )
 * or REDIRECT ( e.g. $this->redirect('user/login'); )
 *
 */
class UserController extends \PassMan\Core\Controller {

    /**
     * UserController's model.
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
        $this->model = new \PassMan\Models\UserModel(); 
    }

    // no index method as there is no such view required!

    /**
     * Executes "login" action.
     *
     * Displays login view including login form.
     *
     * @return void
     */
    protected function login() {
        $this->displayView('login');
    }

    /**
     * Executes "auth" action.
     *
     * Processes login form results. Calls UserModel's "auth" method to check credentials.
     * If login successful redirects to /home/index view.
     * Else - redirects to /user/login (login form).
     *
     * @return void
     */
    protected function auth() {

        if ( $this->model->auth() ) {
            $this->redirect('home');
        }
        else {
            \PassMan\Core\Session::setMessage("Incorrect login.<br />Please try again.", "error");
            $this->redirect('user/login');   
        }
    }

    /**
     * Executes "register" action.
     *
     * Displays register view including user registration form.
     *
     * @return void
     */
    protected function register() {
        $this->displayView('register');
    }

    /**
     * Executes "reguser" action.
     *
     * Processes user registration form results. Calls UserModel's "reguser" method to register.
     * Redirects to appropriate action based on it's result.
     * 
     * @return void
     */
    protected function reguser() {
        // result of register user form

        switch ($this->model->reguser()) {
            case "success":
                \PassMan\Core\Session::setMessage("User created.<br />You may now log in.", "success");
                $this->redirect('user/login');
                break;
            case "userexists":
                \PassMan\Core\Session::setMessage("User already exists.<br />Please try again.", "error");
                $this->redirect('user/register');
                break;
            case "passmatch":
                \PassMan\Core\Session::setMessage("Passwords don't match.<br />Please try again.", "error");
                $this->redirect('user/register');
                break;
            case "dberror":
                \PassMan\Core\Session::setMessage("Error connecting to database.<br />Please try again.", "error");
                $this->redirect('user/register');
                break;
            default:
                \PassMan\Core\Session::setMessage("Incorrect details.<br />Please try again.", "error");
                $this->redirect('user/register');
        }
    }

    /**
     * Executes "forgot" action.
     *
     * Displays forgot view including forgot my password input form.
     *
     * @return void
     */
    protected function forgot() {
        $this->displayView('resend');
    }

    /**
     * Executes "resend" action.
     *
     * Processes sending reset my password link to email inputted on "forgot" form. 
     * Calls UserModel's "resend" method to email user with new link.
     * Redirects to appropriate action based on it's result.
     * 
     * @return void
     */
    protected function resend() {

        if ( $this->model->resend() ) {
            \PassMan\Core\Session::setMessage("Password reset link emailed.<br />Please check your inbox.", "success");
            $this->redirect('user/login');
        }
        else {
            \PassMan\Core\Session::setMessage("Incorrect email.<br />Please try again.", "error");
            $this->redirect('user/forgot');
        }
    }

    /**
     * Executes "reset" action.
     *
     * Displays reset view including the password reset form if valid password reset link used.
     * Otherwiser returns error and redirects to /user/login
     *
     * @return void
     */
    protected function reset() {
        if ( $this->model->reset($this->param) ) {
            // show password reset form
            $this->displayView('reset');
        }
        else {
            \PassMan\Core\Session::setMessage("Error resetting password.<br />Please try again.", "error");
            $this->redirect('user/login');
        }
    }

    /**
     * Executes "resetpass" action.
     *
     * Processes resetting the password in database inputted on "forgot" form. 
     * Calls UserModel's "resetpass" method to reset password.
     * Redirects to /user/login with appropriate message based on it's result.
     * 
     * @return void
     */
    protected function resetpass() {
        // result of password reset form

        switch ($this->model->resetpass()) {
            case "success":
                \PassMan\Core\Session::setMessage("Password has been reset.<br />You may now log in.", "success");
                break;
            case "passmatch":
                \PassMan\Core\Session::setMessage("Passwords don't match.<br />Please try again.", "error");
                break;
            default:
                \PassMan\Core\Session::setMessage("Error connecting to database.<br />Please try again.", "error");
        }
        $this->redirect('user/login');
    }

    /**
     * Executes "delete" action.
     *
     * Enables user to delete their account.
     * Calls UserModel's "delete" method to delete user and all passwords.
     * Redirects to /user/login or /home/index if unsuccessful.
     * 
     * @return void
     */
    protected function delete() {
        // delete user 

        if ( $this->model->delete() ) {
            // show password reset form
            \PassMan\Core\Session::logOut();
            \PassMan\Core\Session::setMessage("Your account has been permanently deleted.<br />Good bye!", "success");
            $this->redirect('user/login');
        }
        else {
            \PassMan\Core\Session::setMessage("Error deleting user.<br />Please try again.", "error");
            $this->redirect('home/index');
        }
    }

    /**
     * Logs user out.
     *
     * Clears SESSION information logging user out and redirects to /user/login.
     * 
     * @return void
     */
    protected function logout() {
        // clear session data
        \PassMan\Core\Session::logOut();
        \PassMan\Core\Session::setMessage("You have been successfully logged out.", "success");
        // redirect to:
        $this->redirect("user/login");
    }
        
}