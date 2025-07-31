<?php
if(!defined('sql202.infinityfree.com')){
    require_once("../initialize.php");
}
class DBConnection{

    private $host = 'sql202.infinityfree.com';
    private $username = 'if0_39463843';
    private $password = 'bedcom2019';
    private $database = 'if0_39463843_elearning';
    
    public $conn;
    
    public function __construct(){

        if (!isset($this->conn)) {
            
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn) {
                echo 'Cannot connect to database server';
                exit;
            }            
        }    
        
    }
    public function __destruct(){
        $this->conn->close();
    }
}
?>