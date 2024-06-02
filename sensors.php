<?php 
require 'connect.php'; 
session_start();
if (isset($_SESSION['authenticated'])) {
    if($_SESSION['role'] !== 'admin'){
        header("Location: index.php");
    }
}
else {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senzory | Životné prostredie</title>
    <?php include 'font.php'; ?>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/sensors.css">
</head>
<body>
    <header>
        <?php require 'nav.php'; ?>
    </header>
    <main class="main">
        <div class="main-heading">
            <h1 class="heading-main">Senzory</h1>
        </div>
        <div class="main-content">
            <?php 
            $query = "SELECT * FROM senzory;";
            $result = mysqli_query($conn, $query);
            ?>
            <div class="sensors">
                <div class="overview">
                    <?php
                    if (!mysqli_num_rows($result)) {
                        echo '<div class="no-data-item">Žiadne dáta</div>';
                    }
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="overview-item">
                            <div class="id"><?php echo $row['id'] . ' - ' . $row['lokalita']; ?></div>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-location="<?php echo $row['lokalita']; ?>" data-equipment="<?php echo $row['vybavenie']; ?>" data-date="<?php echo date_format(date_create($row['posledny_update']), 'd.m.Y H:i:s'); ?>">Informácie</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="sensor-info">
                <h2 class="heading-secondary">Údaje o senzore</h2>
                <div class="info-item">
                    <h3 class="heading-tertiary">ID:</h3>
                    <div class="info-data" id="sensor-id"></div>
                </div>
                <div class="info-item">
                    <h3 class="heading-tertiary">Lokalita:</h3>
                    <div class="info-data" id="sensor-location"></div>
                </div>
                <div class="info-item">
                    <h3 class="heading-tertiary">Vybavenie:</h3>
                    <div class="info-data" id="sensor-equipment"></div>
                </div>
                <div class="info-item">
                    <h3 class="heading-tertiary">Posledný update:</h3>
                    <div class="info-data" id="sensor-date"></div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        const idContainer = document.querySelector('#sensor-id');
        const locationContainer = document.querySelector('#sensor-location');
        const equipmentContainer = document.querySelector('#sensor-equipment');
        const dateContainer = document.querySelector('#sensor-date');
        
        document.querySelector('.overview').addEventListener('click', function(e){
            if(e.target.classList.contains('btn')){
                const id = e.target.dataset.id;
                const location = e.target.dataset.location;
                const equipment = e.target.dataset.equipment;
                const date = e.target.dataset.date;
                idContainer.textContent = "";
                idContainer.textContent = id;
                locationContainer.textContent = location;
                equipmentContainer.textContent = equipment;
                dateContainer.textContent = date;
            }
        });
    </script>
</body>
</html>