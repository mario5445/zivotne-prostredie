<nav class="nav">
<div class="logo nav-item"><a href="index.php">Životné prostredie</a></div>
<?php if(isset($_SESSION['authenticated'])){ ?>
    <div class="login-group">
        <?php 
            $query = "SELECT meno FROM users WHERE id = \"" . $_SESSION['authenticated'] . "\" LIMIT 1";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
        ?>
        <div class="name"><?php echo $row['meno']; ?></div>
        <div class="nav-item"><a href="detail.php">Podrobné dáta</a></div>
        <?php if ($_SESSION['role'] === 'admin') { ?>
        <div class="nav-item"><a href="users.php">Užívatelia</a></div>
        <?php } ?>
        <form action="logout.php" class="logout-form" method="POST">
            <div class="logout nav-item">
                <input type="hidden" name="logout">
                <a href="#" onclick="document.querySelector('.logout-form').submit()">Logout</a></div>
        </form>
    </div>
<?php } else {  ?>
<div class="login-group">
    <div class="login nav-item"><a href="login.php">Login</a></div>
    <div class="register nav-item"><a href="register.php">Registrácia</a></div>
</div>
<?php } ?>
</nav>