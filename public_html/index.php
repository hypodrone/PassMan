<?php
/**
 * PassMan - EDUCATIONAL PROJECT
 *
 * This is an educational project to explore PHP OOP and MVC.
 * All passwords are stored as PLAIN TEXT as this was not part of the exercise.
 * This application should not be used 'as is' to store any sensitive data without further improvements.
 *
 * @author Przemyslaw Mikolajczak <przemek@mylittlepla.net>
 * @license MIT
 * @package PassMan
 */

$loader = require __DIR__ . '/../vendor/autoload.php'; // loads the Composer autoloader

require_once("../passconfig.php"); // loads configuration file

// Initiating session
Passlib\Core\Session::start();

// Setting publicly accessible routes - all others are RESTRICTED and require logging in
Passlib\Core\Router::allowPublicAccess("user/login", "user/auth", "user/register", "user/reguser", "user/forgot", "user/resend", "user/reset", "user/resetpass");

$controller = Passlib\Core\Router::getController($_SERVER['REQUEST_URI']);

// Router - execution of chosen controller.

$controller->execute();
