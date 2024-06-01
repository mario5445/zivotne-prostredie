<?php require 'connect.php';
session_start();

if (isset($_SESSION['authenticated'])) {
    if($_SESSION['role'] !== 'admin'){
        header('Location: index.php');
    }
    else {
        $user = $_SESSION['authenticated'];
    }
}
else {
    header('Location: index.php');
}


if (isset($_POST['role'])) {
    $role_id = mysqli_real_escape_string($conn, trim($_POST['role']));
    $user_id = mysqli_real_escape_string($conn, trim($_POST['userId']));
    $query = "UPDATE users SET rola_id=$role_id WHERE id=$user_id";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Užívatelia | Životné prostredie</title>
    <?php include 'font.php'; ?>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/users.css">
</head>
<body>
    <header>
        <?php require 'nav.php'; ?>
    </header>
    <main class="main">
        <div class="main-heading">
            <h1 class="heading-main">Správa užívateľov</h1>
        </div>
        <div class="main-content">
            <?php 
            $query = "SELECT users.id AS id, users.meno AS meno, role.id AS rola_id, role.nazov AS nazov FROM users
            JOIN role ON users.rola_id = role.id
            WHERE users.id != $user";
            $result = mysqli_query($conn, $query);
            ?>
            <div class="overview <?php echo !mysqli_num_rows($result) ? "overview-no-data" : ""; ?>" <?php echo (mysqli_num_rows($result) > 6 ? 'style="overflow-y: scroll;"' : "") ?>>
                <?php 
                if (!mysqli_num_rows($result)) {
                    echo '<div class="no-data-item">Žiadne dáta</div>';
                }
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="overview-item">
                        <div class="name-box">
                            <?php echo $row['meno']; ?>
                        </div>
                        <div class="role-box">
                            <form class="inline-form role-form" action="" method="POST">
                                <input type="hidden" name="userId" value="<?php echo $row['id'] ?>">
                                <?php 
                                $q = "SELECT * FROM role";
                                $r = mysqli_query($conn, $q);
                                while ($ro = mysqli_fetch_assoc($r)) { ?>
                                    <div class="radio-group">
                                        <input <?php echo $row['rola_id'] === $ro['id'] ? "checked" : "" ?> type="radio" class="input" name="role" id="<?php echo $ro['nazov'] . $row['id']; ?>" value="<?php echo $ro['id']; ?>">
                                        <label class="role-label" for="<?php echo $ro['nazov'] . $row['id']; ?>"><?php echo ucfirst($ro['nazov']); ?></label>
                                    </div>    
                                <?php } ?>           
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
    <footer>
        <div>2024 Made by Mário Lastovica</div>
    </footer>
    <script>
        document.querySelector('.overview').addEventListener('click', function(e){
            console.log(e.target);
            if(e.target.classList.contains('input') || e.target.classList.contains('role-label')){
                e.target.closest('.role-form').submit();
            }
        });
    </script>
</body>
</html>