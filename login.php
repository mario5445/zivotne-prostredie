<?php 
require 'connect.php';
session_start();

if(isset($_SESSION['authenticated'])){
    header("Location: index.php");
}

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $query = "SELECT users.id AS id, heslo, nazov FROM users
    JOIN role ON users.rola_id = role.id
    WHERE email=\"$email\" 
    LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
        if(mysqli_real_escape_string($conn, trim($_POST['password'])) == $row['heslo']){
            $_SESSION['authenticated'] = $row['id'];
            $_SESSION['role'] = $row['nazov'];
            header("Location: index.php");
        }
        else {
            $error = "Nesprávny email alebo heslo";
        }
    }
    else {
        $error = "Nesprávny email alebo heslo";
    }
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Životné prostredie</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/auth.css">
    <?php include 'font.php'; ?>
</head>
<body>
    <div class="auth-container">
        <h1 class="heading-main">Login</h1>
        <form action="" method="POST" class="auth-form">
            <div class="form-container">
                <input type="email" class="auth-form-input input" name="email" placeholder="Email" required>
            </div>
            <div class="form-container">
                <input type="password" class="auth-form-input input" name="password" placeholder="Heslo" required>
            </div>
            <div class="form-container">
                <?php if (isset($error)) {
                    echo '<div class="error-container">' . $error .'</div>';
                } ?>
            </div>
            <div class="form-container button-container">
                <input type="submit" value="Login" name="submit" class="submit-button input">
            </div>
            <div class="back-btn-container">
                <a href="index.php" class="back-btn">&#127968;  Späť</a>
            </div>
        </form>
    </div>
</body>
</html>