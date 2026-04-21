<?php
class DB
{
    private $host = "localhost";
    private $user = "root";
<<<<<<< HEAD
    private $pass = "anna_luna1223";
=======

    private $pass = "shawnmarlogaldo@1122";
>>>>>>> e616dbe15b9c4674cee136df67a77384bc83e6d1
    private $dbname = "grocer_easedb";

    public $conn = null;
    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            die("Connection Failed: ". $e->getMessage());
        }
    }
}

?>