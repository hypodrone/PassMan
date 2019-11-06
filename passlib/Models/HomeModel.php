<?php

namespace PassMan\Models;

/**
 * Home Model Class.
 *
 * This is home model class extending Model.
 *
 */
class HomeModel extends \PassMan\Core\Model {

    /**
	 * Main application model - Home index.
	 * 
	 * Uses login form $_POST data to check if user exists and password correct.
     * 
     * @param int $param Used for pagination to determine which page to display.
	 *
	 * @uses Session:get("user_id") to establish logged in user for db queries.
	 * 
	 * @return array $data An array of arrays with data required by Home view.
	 */
    public function index($param) {
        // $param used for pagination
        $stats = array();
        $pass = array();
        $other = array();

        $rows_pp = 5; // used in pagination helper
        $user_id = \PassMan\Core\Session::get("user_id");

        $this->query('SELECT COUNT(*) AS users_qty FROM passmanusers');
        $stats = $this->single();     // fetches single row

        $this->query('SELECT COUNT(*) AS total FROM passmandata WHERE user_id = :user_id');
        $this->bind(':user_id', $user_id);
        $result = $this->single();

        $stats['total'] = $result['total'];

        $check = ceil($stats['total']/$rows_pp);
        if ( $param < 0 ) {
            $param = 1;
        }
        if ( $param > $check) {
            $param = $check;
        }

        $offset = ($param*$rows_pp)-($rows_pp);

        $this->query('SELECT * FROM passmandata WHERE user_id = :user_id LIMIT :limit OFFSET :offset');
        // the query above requires changes as per pagination!!! Return only $rows_per_page items starting from $current

        $this->bind(':user_id', (int)$user_id);
        $this->bind(':limit', (int)$rows_pp);
        $this->bind(':offset', (int)$offset);

        $pass = $this->resultSet();  //returns multiple rows...

        $other['current'] = $param;
        $other['rows_pp'] = $rows_pp;
        
        $data = array (
            "stats" => $stats,
            "pass" => $pass,
            "other" => $other
        );
        return $data;
    }

    /**
	 * Adds new service and password to the list.
     * 
     * @uses Session:get("user_id") to establish logged in user for db queries.
     * 
     * @return string Returns a result code to be processed by controller.
     */
    public function add() {
        // Sanitize POST
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $user_id = \PassMan\Core\Session::get("user_id");
        $service = $post['service'];
        $srvpsswd = $post['password'];

        if ( $service == "" ) {
            return "noservice";
        }

        $this->query('INSERT INTO passmandata ( user_id, service, srvpsswd) 
                        VALUES(:user_id, :service, :srvpsswd)');
        $this->bind(':user_id', $user_id);
        $this->bind(':service', $service);
        $this->bind(':srvpsswd', $srvpsswd);

        if ( $this->execute() ){
            return "success"; 
        }
        return "passexists"; 
    }

    /**
	 * Modifies password or service name.
     * 
     * @return string Returns a result code to be processed by controller.
     */
    public function modify() {
        //either update or delete password
        // Sanitize POST
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $action = $post['modify'];
        $id = $post['id'];
        $service = $post['service'];
        $srvpsswd = $post['password'];

        if ( $action=="update" && $id!="" ) {
            if ( $service=="" ) {
                return "noservice";
            }
            $this->query('UPDATE passmandata SET service = :service, srvpsswd = :srvpsswd WHERE id = :id');
            $this->bind(':service', $service);
            $this->bind(':srvpsswd', $srvpsswd);
            $this->bind(':id', $id);
            $this->execute();

            return "updated";
        }
        else if ( $action=="delete" && $id!="" ) {
            $this->query('DELETE FROM passmandata WHERE id = :id');
				$this->bind(':id', $id);
				$this->execute();
            return "deleted";
        }
        else {
            return "error";
        }
    }

}