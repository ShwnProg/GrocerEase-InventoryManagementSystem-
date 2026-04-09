<?php
require_once '../config/db.php';
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function InsertUser($username, $password, $email, $contact)
    {
        $stmt = $this->db->conn->prepare("INSERT INTO users(username,password,email,contact_number) 
                                              VALUES(:username,:password,:email,:contact)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':email' => $email,
            ':contact' => $contact
        ]);

        return $stmt->rowCount() > 0;
    }
    public function AuthenicateUser($username, $password)
    {
        $stmt = $this->db->conn->prepare("SELECT user_id, password FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user['user_id'];
        }
        return false;
    }

    public function GetUserById($id)
    {
        $stmt = $this->db->conn->prepare("SELECT user_id, username, email, contact_number,role,profile_picture FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>