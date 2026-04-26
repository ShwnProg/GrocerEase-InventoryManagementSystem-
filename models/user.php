<?php
require_once(__DIR__ . '/../config/db.php');

class User
{
    private $conn;

    public function __construct($db)
    {
        // $database = new DB();
        $this->conn = $db;
    }
    public function InsertUser($username, $password, $email, $contact)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (username, password, email, contact_number)
             VALUES (:username, :password, :email, :contact)"
        );
        $stmt->execute([
            ':username' => $username,
            ':password' => $password,
            ':email' => $email,
            ':contact' => $contact,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function AuthenticateUser($username, $password)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id, password FROM users WHERE username = :username"
        );
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user['user_id'];
        }
        return false;
    }

    public function GetUserById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT user_id, username, email, contact_number, role
             FROM users WHERE user_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function UpdateUser($user_id, $username, $email, $contact)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users
             SET username = :username, email = :email, contact_number = :contact
             WHERE user_id = :id"
        );
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':contact' => $contact,
            ':id' => $user_id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function UpdatePassword($user_id, $hashed_password)
    {
        $stmt = $this->conn->prepare(
            "UPDATE users SET password = :password WHERE user_id = :id"
        );
        $stmt->execute([
            ':password' => $hashed_password,
            ':id' => $user_id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function UsernameExists($username, $exclude_user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM users
             WHERE username = :username AND user_id != :id"
        );
        $stmt->execute([':username' => $username, ':id' => $exclude_user_id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function EmailExists($email, $exclude_user_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM users
             WHERE email = :email AND user_id != :id"
        );
        $stmt->execute([':email' => $email, ':id' => $exclude_user_id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
?>