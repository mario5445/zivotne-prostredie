<?php 

session_start();
require "connect.php";

if (isset($_GET['lokalita'])) {
    $lokalita = $_GET['lokalita'];
}
else {
    $query = "SELECT lokalita FROM senzory GROUP BY lokalita LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $lokalita = $row['lokalita'];
}

?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lokalita; ?> | Životné prostredie</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/index.css">
    <?php include 'font.php'; ?>
</head>
<body>
    <header>
        <?php include 'nav.php'; ?>
    </header>
    <main class="main">
        <div class="main-heading">
            <h1 class="heading-main">Lokalita <?php echo $lokalita; ?></h1>
            <div class="form-container">
                <form action="" method="GET" class="select-form">
                <select name="lokalita" class="select" onchange="document.querySelector('.select-form').submit()">
                    <?php 
                        $query = "SELECT lokalita FROM senzory GROUP BY lokalita";
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_assoc($result)){
                            ?>
                            <option value="<?php echo $row['lokalita']; ?>" <?php echo $lokalita == $row['lokalita'] ? 'selected' : '' ?>><?php echo $row['lokalita']; ?></option>
                         <?php } ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="main-content">
            <?php 
            $query = "SELECT COUNT(*) AS count FROM data
            JOIN senzory ON data.senzor_id = senzory.id
            WHERE lokalita=\"$lokalita\"";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $count = $row["count"];
            ?>
            <div class="main-content--left">
                <div class="info">Dáta za posledných 10 dní</div>
                    <?php 
                    $query = "SELECT dt.id as id, ROUND(dt.hodnota, 2) as hodnota, dt.datum as datum, kt.nazov as kategoria, kt.jednotka AS jednotka, sz.lokalita as lokalita FROM data as dt
                        JOIN senzory as sz ON  dt.senzor_id = sz.id
                        JOIN kategorie as kt ON dt.kategoria_id = kt.id
                        WHERE sz.lokalita = \"$lokalita\" AND datum >= DATE_SUB(NOW(), INTERVAL 10 DAY)
                        ORDER BY datum desc;
                    ";
                    $result = mysqli_query($conn, $query); ?>
                <div class="overview <?php echo !mysqli_num_rows($result) ? "overview-no-data" : ""; ?>" <?php echo (mysqli_num_rows($result) > 6 ? 'style="overflow-y: scroll;"' : "") ?>>
                    <?php if(!mysqli_num_rows($result)){
                        echo '<div class="no-data-item">Žiadne dáta</div>';
                    }
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="overview-item">
                            <div class="overview-item--kategoria"><?php echo $row['kategoria']; ?></div>
                            <div class="overview-item--hodnota"><?php echo $row['hodnota'] . $row['jednotka']; ?></div>
                            <div class="overview-item--datum"><?php echo date_format(date_create($row['datum']), 'd.m.Y H:i:s'); ?></div>
                        </div>
                  <?php } ?>
                </div>
            </div>
            <div class="main-content--right">
                <?php 
                $query = "SELECT kt.nazov as kategoria, jednotka, ROUND(AVG(hodnota), 2) AS avg FROM data
                    JOIN kategorie as kt ON data.kategoria_id = kt.id
                    JOIN senzory as sz on data.senzor_id = sz.id
                    WHERE sz.lokalita = \"$lokalita\" AND datum >= DATE_SUB(NOW(), INTERVAL 10 DAY)
                    GROUP BY kt.nazov;
                ";
                $result = mysqli_query($conn, $query);
                // if(!mysqli_num_rows($result)){
                //    echo '<div class="heading-secondary">No data yet</div>';
                // }
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="average-container">
                        <div class="heading-secondary">Priemerná <?php echo $row['kategoria']; ?>: <?php echo $row['avg'] . $row['jednotka']; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>   
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>