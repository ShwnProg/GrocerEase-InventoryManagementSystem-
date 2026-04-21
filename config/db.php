<?php
class DB
{
    private $host = "localhost";
    private $user = "root";
<<<<<<< HEAD
    private $pass = "shawnmarlogaldo@1122";
=======
    private $pass = "1234";
>>>>>>> 432e3b669f695ecd8f8261e2bc435222f435e641
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