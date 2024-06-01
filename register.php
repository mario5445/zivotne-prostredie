<?php 
require 'connect.php';

session_start();
if(isset($_SESSION['authenticated'])){
    header("Location: index.php");
}

function check_if_email_exists($email){
    global $conn;
    $query = "SELECT id FROM users WHERE email=\"$email\"";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result)) {
        return true;
    }
    return false;
}

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $meno = mysqli_real_escape_string($conn, trim($_POST['meno']));
    $heslo = mysqli_real_escape_string($conn, trim($_POST['password']));
    $query = "SELECT id FROM role WHERE nazov=\"občan\" LIMIT 1";
    $result = mysqli_query($conn, $query);
    $role = mysqli_fetch_assoc($result)['id'];
    if ($_POST['password'] !== $_POST['repeatedPassword']) {
        $error = "Heslo sa nezhoduje";
    }
    else {
        if (check_if_email_exists($email)){
            $error = "Zadaný email už existuje";
        }
        else {
            $query = "INSERT INTO users(meno, email, heslo, rola_id) VALUES (\"$meno\", \"$email\", \"$heslo\", $role);";
            mysqli_query($conn, $query);
            header("Location: login.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrácia | Životné prostredie</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/auth.css">
    <?php include 'font.php'; ?>
</head>
<body>
    <div class="auth-container">
        <h1 class="heading-main">Registrácia</h1>
        <form action="" method="POST" class="login-form">
            <div class="form-container">
                <input type="text" class="auth-form-input input" name="meno" placeholder="Titul, meno, priezvisko" value="<?php echo (isset($_POST['meno']) && isset($error)) ? $_POST['meno'] : "" ?>" required>
            </div>
            <div class="form-container">
                <input type="email" class="auth-form-input input" name="email" placeholder="Email" value="<?php echo (isset($_POST['email']) && isset($error)) ? $_POST['email'] : "" ?>" required>
            </div>
            <div class="form-container">
                <input type="password" class="auth-form-input input" name="password" placeholder="Heslo" required>
            </div>
            <div class="form-container">
                <input type="password" class="auth-form-input input" name="repeatedPassword" placeholder="Zopakujte heslo" required>
            </div>
            <div class="form-container">
                <?php if (isset($error)) {
                    echo '<div class="error-container">' . $error .'</div>';
                } ?>
            </div>
            <div class="form-container button-container">
                <input type="submit" value="Registrovať" name="submit" class="submit-button input">
            </div>
            <div class="back-btn-container">
                <a href="index.php" class="back-btn">&#127968;  Späť</a>
            </div>
        </form>
    </div>
</body>
</html>