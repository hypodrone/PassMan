<?php

	namespace Passlib\Core;

    /**
     * Base Model Class.
     *
     * This is model's base abstract class providing standard functionalities.
     *
     */
    abstract class Model {
        /**
         * Database handle.
         *
         * @var resource $dbh Database handle
         */
        protected $dbh;

          /**
           * Database statement.
          *
          * @var object $stmt Database statement
          */
          protected $stmt;

          /**
           * Class constructor.
          *
          * Sets up database handle based on config.php constants
          *
          * @return void
          */
          public function __construct() {
               $this->dbh = new \PDO("mysql:host=".\DB_HOST.";dbname=".\DB_NAME, \DB_USER, \DB_PASS);
          }

          /**
           * Prepares the MySQL query.
          *
          * @param string $query MySQL query 
          * 
          * @return void
          */
          public function query($query) {
               $this->stmt = $this->dbh->prepare($query);
          }

          /**
           * Binds value to a parameter.
          *
          * One parameter at a time. All parameters MUST be bibd separately.
          * Checks the parameter type automatically.
          *
          * @param string $param Parameter name e.g. :id
          * @param mixed $value Parameter value
          * @param mixed $type (optional=null) May be provided explicitly e.g. \PDO::PARAM_INT if null will be auto determined
          * 
          * @return void
          */
          public function bind($param, $value, $type = null) { // each variable needs to be bound separately
               if (is_null($type)) {
                    switch (true) {
                         case is_int($value):
                              $type = \PDO::PARAM_INT;
                              break;
                         case is_bool($value):
                              $type = \PDO::PARAM_BOOL;
                              break;
                         case is_null($value):
                              $type = \PDO::PARAM_NULL;
                              break;
                         default:
                              $type = \PDO::PARAM_STR;
                              break;
                    }
               }
               $this->stmt->bindValue($param, $value, $type);
          }

          /**
           * Executes the MySQL query.
          *
          * Used when no information needs to be retrieved from db, e.g. INSERT or UPDATE
          *
          * 
          * @return bool
          */
          public function execute() {
               return $this->stmt->execute();
          }

          /**
           * Executes the MySQL query and returns rows assoc array.
          *
          * @return array
          */
          public function resultSet() {
               $this->execute();
               return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
          }

          /**
           * Returns an ID of last inserted element.
          *
          * WARNING: Bear in mind may result DIFFERENT with different DB drivers!
          *
          * @return string
          */
          public function lastInsertId() {
               return $this->dbh->lastInsertId();
          }

          /**
           * Executes the MySQL query and returns single row as an assoc array.
          *
          * @return array
          */
          public function single() {
               $this->execute();
               return $this->stmt->fetch(\PDO::FETCH_ASSOC);
          }
          
    }