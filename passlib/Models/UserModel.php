<?php

namespace PassMan\Models;

/**
 * User Model Class.
 *
 * This is user model class extending Model.
 *
 */
class UserModel extends \PassMan\Core\Model {

	/**
	 * User authorization.
	 * 
	 * Uses login form $_POST data to check if user exists and password correct.
	 *
	 * @uses Session:logIn($user) to set up session data
	 * 
	 * @return bool
	 */
	public function auth() {
		// Sanitize POST
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		if ($post['login']) {
			$email = trim($post['email']);
			$password = $post['password'];
		}
		$user = $this->checkPass($email,$password);
		if ( $user ) {
			\Passman\Core\Session::logIn($user);

			$this->query('UPDATE passmanusers SET last_login = :last_login WHERE id = :id');
			$this->bind(':last_login', date('Y-m-d H:i:s') );
			$this->bind(':id', $user['id']);
			$this->execute();
			return true; // all OK
		} 
		return false;
	}

	/**
	 * User registration.
	 * 
	 * Creates user record in the database using $_POST data,
	 *
	 * @return string function status used to set up success / error message in controller
	 */
	public function reguser () {
		// Sanitize POST
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		if ($post['regme']) {
			$email = trim($post['email']);
			$role = "user"; // may be implemented for future use - currently not relevant
			$firstname = trim($post['firstname']);
			$surname = trim($post['surname']);
			$password = $post['pass'];
			$passconf = $post['passconf'];
			$created = date('Y-m-d H:i:s');
			// validate if email, firstname and password are inputted in
			if( $email == '' || $firstname == '' || $password == '') {
				return "error"; // general error
			}
			if ( $password!=$passconf )
			{
				return "passmatch"; // password and confirmation do not match
			}
			if ( $this->checkUser($email) ) {
				return "userexists"; // error while trying to register user that already exists
			}
			$password = $this->hashPassword($password,$email);

			$this->query('INSERT INTO passmanusers ( email, role, password, firstname, surname, created) 
			              VALUES(:email, :role, :password, :firstname, :surname, :created)');
			$this->bind(':email', $email);
			$this->bind(':role', $role);
			$this->bind(':password', $password);
			$this->bind(':firstname', $firstname);
			$this->bind(':surname', $surname);
			$this->bind(':created', $created);
			// Verify
			if ( $this->execute() ){
				$this->notifyAdminEmail(ADMIN_EMAIL, $email);
				return "success"; //user created - ALL OK
			}
			return "dberror"; // database error	
		}
	}

	/**
	 * Generates password reset token and updated database.
	 *
	 * @return bool
	 */
	public function resend() {
		// Sanitize POST
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		if ( $post['resendpass'] && $this->checkUser($post['email']) ) {
				$token = bin2hex(openssl_random_pseudo_bytes(50));
				$this->query('UPDATE passmanusers SET resettoken = :resettoken WHERE email = :email');
				$this->bind(':resettoken', $token);
				$this->bind(':email', $post['email']);
				$this->execute();
				$this->emailToken($post['email'], $token, \PassMan\Core\Session::get("user_firstname"));
			return true;
		}
		return false;
	}

	/**
	 * Validates reset token link.
	 * 
	 * @uses Session::set to add session data - which user resets password.
	 *
	 * @return bool
	 */
	public function reset($resetString) {

		if ( !$resetString || $resetString=="" ) {
			return false;
		}
		$this->query('SELECT * FROM passmanusers WHERE resettoken = :resettoken LIMIT 1');
		$this->bind(':resettoken', $resetString);
		$row = $this->single();
		if ( $row ) {
			\PassMan\Core\Session::set("user_reset",$row["email"]);
			return true;
		}
		return false;
	}

	/**
	 * Password reset.
	 * 
	 * Resets user password in the database using $_POST data, clears reset token.
	 * 
	 * @uses Session::get to establish which user resets password
	 * @uses Session::unset to clear session's password reset mode
	 *
	 * @return mixed function status used to set up success / error message in controller
	 */
	public function resetpass() {
		// Sanitize POST
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		if ( $post['resetpass'] && \PassMan\Core\Session::get("user_reset") ) { 
			$password = $post['respassword'];
			$passconf = $post['respasswordconf'];
			$email = \PassMan\Core\Session::get("user_reset");

			if ( $password!=$passconf )
			{
				return "passmatch"; // password and confirmation do not match
			}
			//and then
			$password = $this->hashPassword($password,$email);
			// Insert into MySQL
			$this->query('UPDATE passmanusers SET password = :password, resettoken = :resettoken WHERE email = :email');
			$this->bind(':email', $email);
			$this->bind(':password', $password);
			$this->bind(':resettoken', '');

			$this->execute();

			\PassMan\Core\Session::unset("user_reset"); // unset the variable enforcing user to use the reset link again
			return "success"; // password reset - ALL OK
		}
		return "error";
	}

	/**
	 * Deletes user.
	 * 
	 * Removes the user and ALL their stored data.
	 * 
	 * @uses Session::get to bind user_id
	 *
	 * @return bool
	 */
	public function delete() {
		// Sanitize POST
		$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		 if ( $post['delete'] ) {			
				$this->query('DELETE FROM passmandata WHERE user_id = :user_id)');
				$this->bind(':user_id', \PassMan\Core\Session::get("user_id"));
				$this->execute();
				$this->query('DELETE FROM passmanusers WHERE id = :id');
				$this->bind(':id', \PassMan\Core\Session::get("user_id"));
				$this->execute();
				return true;
		}		
		return false; 
	}

	/**
	 * Hashing password with SHA_512.
	 * 
	 * Adds user email to the password inputted by user.
	 * 
	 * @param string $pass Password
	 * @param string $email User's email/login
	 *
	 * @return string encrypted password
	 */
	private function hashPassword($pass, $email) {
		$salt = uniqid('', true);
		$algo = '6'; // CRYPT_SHA512
		$rounds = '5048';
		$cryptSalt = '$'.$algo.'$rounds='.$rounds.'$'.$salt;
		$hashedPassword = crypt($pass.$email, $cryptSalt);
		return $hashedPassword;
	}

	/**
	 * Checks if user exists in database.
	 * 
	 * @param string $email User's email/login
	 *
	 * @return bool
	 */
	private function checkUser($email)
    {
        // check if user exists
		$email = strtolower($email);
		$this->query('SELECT * FROM passmanusers WHERE email = :email LIMIT 1');
		$this->bind(':email', $email);
		$row = $this->single();
		if ($row) return true;
		return false;
	}
	
	/**
	 * Checks if user's password is correct.
	 * 
	 * @param string $email User's email/login
	 * @param string $pass User's password
	 *
	 * @return mixed Database row with user's data or FALSE
	 */
	private function checkPass($email, $pass)
    {
	  $email = strtolower($email);
	  $this->query('SELECT * FROM passmanusers WHERE email = :email LIMIT 1');
	  $this->bind(':email', $email);
	  $row = $this->single();
	  $hashedPasswordInDb = $row['password'];
      if (crypt($pass.$email, $hashedPasswordInDb) == $hashedPasswordInDb) 
      {
        return $row;
      }
      return false;
	}
	
	/**
	 * Emails password reset token.
	 * 
	 * @param string $email User's email/login
	 * @param string $token Password reset token
	 * @param string $firstname User's firstname
	 */
	private function emailToken($email, $token, $firstname) {
		$to = $email;
        $subject = "PassMan - password reset link";
        $message = "
        <html>
          <head>
           <title>PassMan password reset</title>
          </head>
          <body>
            <p>Hi $firstname!</p>
            <p>You have recently asked for a password reset to our service.</p>
            <p>Please use the following link:</p>
            <p><strong><a href=\"https://".APP_DOMAIN."/user/reset/$token\">".APP_DOMAIN."/user/reset/$token</a></strong><p>
            <hr />
            <p><small>If it wasn't you or you've managed to retrieve your password please ignore this email.</small></p>
            <br /><br />
          </body>
        </html>
        ";
		$this->sendEmail($to,$subject,$message);
	}

	/**
	 * New user registration - admin notification.
	 * 
	 * @param string $email Admin's email
	 * @param string $user User email/login
	 */
	private function notifyAdminEmail($email, $user) {
		$to = $email;
		$subject = "PassMan - new user registration";
		$message = "
        <html>
          <head>
           <title>PassMan user rgistration</title>
          </head>
          <body>
            <p>Hi!</p>
            <p>New user - $user - has just registered with PassMan.</p>
          </body>
        </html>
        ";
		$this->sendEmail($to,$subject,$message);
	}

	/**
	 * Sending email function.
	 * 
	 * All headers provided. 
	 * 
	 * @param string $email Recipient's email
	 * @param string $subject Email subject
	 * @param string $message Email body
	 */
	private function sendEmail($email, $subject, $message) {
		$to = $email;
        $sub = $subject;
		$msg = $message;
		
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <'.ADMIN_EMAIL.'>' . "\r\n";

		mail($to,$sub,$msg,$headers);
	}
}

?>