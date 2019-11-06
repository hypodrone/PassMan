<?php

namespace PassMan\Core;

/**
 * Session and messages handler - STATIC
 */
final class Session {

    /**
     * Session's own instance
     * @var object
     */
    private static $instance = null;

    /**
     * Session timeout - 10 minutes by default
     * @var int
     */
    private static $session_timeout = 600;

    /**
     * PRIVATE Session start; default user_role and timeout setup
     */
    private function __construct() {
        session_start();
        if (!isset($_SESSION["uid"])) $_SESSION["uid"]=session_id();
        if (!isset($_SESSION["user_role"])) $_SESSION["user_role"]="none";
        if ( defined("SESSION_TIME") ) self::$session_timeout = SESSION_TIME;
    }

    /**
     * PRIVATE _clone to block cloning
     */
    private function __clone() {
    }

    /**
     * Starting static session
     * 
     * @return object
     */
    public static function start() {
        if ( self::$instance == false ) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    // Magic methods do not work in static context!!
    // Need own implementation

    public static function set($name, $value) {
        $_SESSION[$name]=$value;
    }

    public static function get($name)
    {
        if (isset($_SESSION[$name])) return $_SESSION[$name];
    }

    public static function isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public static function unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Sets session message
     * 
     * @param string $text message body
     * @param string $type message type - success or error
     */
    public static function setMessage($text, $type) {
        if($type == 'error') {
            self::set("errorMsg", $text);
        } 
        else {
            self::set("successMsg", $text);
        }
    }

    /**
     * Shows (echo) html block - div - with  session message
     */
    public static function showMessage() {
        if( self::isset("errorMsg") ) {
            echo '<div class="alert alert-danger">'.
            self::get("errorMsg").'</div>';
            self::unset("errorMsg");
        }
        if( self::isset("successMsg") ) {
            echo '<div class="alert alert-success">'.
            self::get("successMsg").'</div>';
            self::unset("successMsg");
        }
    }

    /**
     * Sets session data for user logging in based on time and database
     * 
     * @param array $user Array of user's data from database (id, email, firstname, role, last_login)
     */
    public static function logIn($user) {
        self::set("user_id",$user['id']);
        self::set("user_email", $user['email']);
        self::set("user_firstname", $user['firstname']);
        self::set("user_role", $user['role']);
        self::set("user_last_login_date", date('Y-m-d', strtotime($user['last_login'])));
        self::set("user_last_login_time", date('H:i:s', strtotime($user['last_login'])));

        self::set("is_logged_in",true);
        self::set("last_active", time());
    }

    /**
     * Destroys user's information when logging off but not the session
     */
    public static function logOut() {
        session_unset();
    }

    /**
     * Checks if session not expired
     * 
     * If expired - logs user out otherwise resets last_active timestamp in session
     * 
     * @return bool 
     */
    public static function isValid() {
        // check if session not timed out
        if ( self::isset("last_active") && (  (time()-self::get("last_active"))<self::$session_timeout) ) {
            self::set("last_active", time());
            return true;
        }   
        //otherwise
        self::logOut(); // destroy session data in storage but keep the session
        return false;
    }

    /**
     * Checks if any user is logged in
     * 
     * @return bool 
     */
    public static function isLogged() {
        // checking if user is logged in
        if ( self::get("is_logged_in") ) {
            return true;
        }
        return false;
    }

    /**
     * Returns logged user's role
     */
    public static function getRole() {
        return self::get("user_role");
    }
}