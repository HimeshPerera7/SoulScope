<?php
session_start();
require_once "config.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);  

    if (empty($email) || empty($password)) {
        $_SESSION["login_error"] = "Please enter both email and password.";
        header("Location: ../login.php");
        exit();
    }

    // Ensure the user is not deleted (deleted = 0)
    $query = "SELECT userId, password, role, deleted FROM users WHERE email = :email AND deleted = 0";
    $stmt = $pdo->prepare($query); 
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);  
    $stmt->execute();

    if ($stmt->rowCount() > 0) {  
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user['userId'];  
        $hashed_password = $user['password'];
        $db_role = $user['role']; 

        if (password_verify($password, $hashed_password)) {
            if ($role === $db_role) {  
                $_SESSION["user_id"] = $user_id;
                $_SESSION["email"] = $email;
                $_SESSION["role"] = $db_role; 

                if ($db_role == "user") {
                    header("Location: ../user_dashboard.php"); 
                } else {
                    header("Location: ../admin/admin_dashboard.php"); 
                }
                exit();
            } else {
                $_SESSION["login_error"] = "You do not have permission to access this role.";
            }
        } else {
            $_SESSION["login_error"] = "Incorrect password. Please try again.";
        }
    } else {
        $_SESSION["login_error"] = "Invalid credentials or account deleted.";
    }

    $pdo = null;
    header("Location: ../login.php");
    exit();
}
