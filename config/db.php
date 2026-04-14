<?php
class DB
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "";

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