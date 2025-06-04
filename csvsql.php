<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if(php_sapi_name() != 'cli') {
    // session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
        ?>
<script type="text/javascript">
window.location.href = "login.php";
</script>
<?php
    }
}

include 'config.php';

$resultcol = mysqli_query(dbconnect, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'albin549889' AND TABLE_NAME = 'comptabilite' ORDER BY `ORDINAL_POSITION`; ") or die("Selection Error " . mysqli_error(dbconnect));
// $resultcol = mysqli_query(dbconnect, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'albinton' AND TABLE_NAME = 'comptabilite' ORDER BY `ORDINAL_POSITION`; ") or die("Selection Error " . mysqli_error(dbconnect));
while($row = mysqli_fetch_assoc($resultcol)) {
    $header[] = $row['COLUMN_NAME'];
}

$sql = "SELECT * FROM `comptabilite` WHERE `id` <> '1' ORDER BY `id` ASC;";

// fetch mysql table rows
// $sql = "select * from tbl_books";
$result = mysqli_query(dbconnect, $sql) or die("Selection Error " . mysqli_error(dbconnect));

$fp = fopen('./comptabilite_albinton.csv', 'w');

fputcsv($fp, $header, ';');
while($row = mysqli_fetch_assoc($result)) {
    fputcsv($fp, $row, $delimiter = ';');
}
fclose($fp);
?>
<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
    ?>
<script type="text/javascript">
window.location.href = "login.php";
</script>
<?php
}

//Read the filename

$filename = 'comptabilite_albinton.csv';
// Check the file exists or not
if(file_exists($filename)) {

    //Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: 0");
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Content-Length: ' . filesize($filename));
    header('Pragma: public');

    //Clear system output buffer
    flush();

    //Read the size of the file
    readfile($filename);
    echo "FICHIER TELECHARGER";
    //Terminate from the script
    die();
} else {
    echo "File does not exist.";
}



// echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>