<?php
class DB
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "shawnmarlogaldo@1122";
    private $dbname = "grocer_easedb";

    public $conn = null;
    public function GetConnection()
    {
        $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", $this->user, $this->pass);

        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $this->conn;
    }
}

?>
