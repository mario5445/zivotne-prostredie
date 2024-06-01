<?php 

session_start();

if(!isset($_SESSION['authenticated'])){
    header("Location: index.php");
}

require "connect.php";

if (isset($_GET['lokalita'])) {
    $lokalita = mysqli_real_escape_string($conn, trim($_GET['lokalita']));
}
else {
    $query = "SELECT lokalita FROM senzory GROUP BY lokalita LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $lokalita = $row['lokalita'];
}

if (isset($_GET['kategoria'])){
    $kategoria = mysqli_real_escape_string($conn, trim($_GET['kategoria']));
    $query = "SELECT jednotka FROM kategorie WHERE nazov=\"$kategoria\" LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $jednotka = $row['jednotka'];
}
else {
    $query = "SELECT * FROM kategorie LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $kategoria = $row['nazov'];
    $kategoria_id = $row['id'];
    $jednotka = $row['jednotka'];
}

if(isset($_GET['datum'])){
    switch (trim($_GET['datum'])) {
        case 'dni':
            $dateRange = "AND datum >= DATE_SUB(NOW(), INTERVAL 10 DAY)";
            break;
        case 'mesiac':
            $dateRange = "AND datum >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        case 'rok':
            $dateRange = "AND datum >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            break;
        case 'vsetko':
            $dateRange = "";
            break;
        default:
            $dateRange = "";
            break;
    }
}
else {
    $dateRange = "";
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lokalita; ?> | Detailné dáta</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/detail.css">
    <?php include 'font.php'; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {packages: ['corechart', 'line']});
        google.charts.setOnLoadCallback(drawBasic);

        function drawBasic() {

            var data = new google.visualization.DataTable();
            data.addColumn('datetime', 'X');
            data.addColumn('number', '<?php echo $jednotka; ?>');
            
            <?php 
                $query = "SELECT dt.id AS id, dt.hodnota AS hodnota, DATE_FORMAT(dt.datum, '%Y-%m-%dT%H:%i:%sZ') AS datum FROM data AS dt
                JOIN senzory as sz ON dt.senzor_id = sz.id
                JOIN kategorie as kt ON dt.kategoria_id = kt.id
                WHERE sz.lokalita = \"$lokalita\" AND kt.nazov = \"$kategoria\" $dateRange
                ORDER BY datum ASC;";
                $result = mysqli_query($conn, $query);
            ?>

            data.addRows([
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    [new Date('<?php echo $row['datum']; ?>'), <?php echo $row['hodnota']; ?>],
                <?php } ?>
            ]);

            var options = {
                hAxis: {
                    title: 'Dátum'
                },
                vAxis: {
                    title: '<?php echo ucfirst($kategoria); ?>'
                },
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
    <header>
        <?php require 'nav.php'; ?>
    </header>
    <main class="main">
        <div class="main-heading">
            <h1 class="heading-main">Lokalita <?php echo $lokalita; ?></h1>
        </div>
        <div class="chart-and-content">
            <div id="chart_div" class="main-left" style="width: 120rem; height: 40rem;"></div>
            <div class="main-right">
            <div class="heading-secondary settings-heading">Nastavenia:</div>
                <div class="form-container">
                    <form class="inline-form setting-form" action="" method="GET">
                        <select name="lokalita" class="select" onchange="document.querySelector('.setting-form').submit()">
                            <?php 
                                $query = "SELECT lokalita FROM senzory GROUP BY lokalita";
                                $result = mysqli_query($conn, $query);
                                while($row = mysqli_fetch_assoc($result)){
                                    ?>
                                    <option value="<?php echo $row['lokalita']; ?>" <?php echo $lokalita == $row['lokalita'] ? 'selected' : '' ?>><?php echo $row['lokalita']; ?></option>
                            <?php } ?>
                        </select>
                        <select name="kategoria" class="select" onchange="document.querySelector('.setting-form').submit()">
                        <?php 
                            $query = "SELECT nazov FROM kategorie";
                            $result = mysqli_query($conn, $query);
                            while($row = mysqli_fetch_assoc($result)){
                                ?>
                                <option value="<?php echo $row['nazov']; ?>" <?php echo $kategoria == $row['nazov'] ? 'selected' : '' ?>><?php echo ucfirst($row['nazov']); ?></option>
                        <?php } ?>
                        </select>
                        <select name="datum" class="select" onchange="document.querySelector('.setting-form').submit()"> 
                            <option value="vsetko" <?php echo $dateRange === "" ? "selected" : ""; ?>>Všetko</option>
                            <option value="dni" <?php echo isset($_GET['datum']) ? ($_GET['datum'] === "dni" ? "selected" : "") : "" ; ?>>Posledných 10 dní</option>
                            <option value="mesiac" <?php echo isset($_GET['datum']) ? ($_GET['datum'] === "mesiac" ? "selected" : "") : "" ; ?>>Posledný mesiac</option>
                            <option value="rok" <?php echo isset($_GET['datum']) ? ($_GET['datum'] === "rok" ? "selected" : "") : "" ; ?>>Posledný rok</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div>2024 Made by Mário Lastovica</div>
    </footer>
</body>
</html>