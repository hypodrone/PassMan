<?php

// including configuration file
include(dirname(__FILE__)."/../../../passconfig.php");

// RUNNING THE TEST from PassMan directory: phpunit .\tests\passlib\Models\HomeModelTest.php

class UserModelTest extends PHPUnit_Framework_TestCase {

    public function testAuthReturnsTrue() {

        $model = new \PassMan\Models\UserModel();
        $_POST['login'] = true;
        $_POST['email'] = "legionix@gmail.com";
        $_POST['password'] =  "pass";

        $this->assertTrue($model->auth());
    }

}