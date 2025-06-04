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



//php-cgi -f csvsql.php  intervaldate='2021-04-01' interval='3' compilation=true creditdebit=true
// $_GET['intervaldate']; // date yyyy-mm-01 >= 2021-04-01
// $_GET['interval']; // int > 0 et <= diff mois 2021-04-01 et NOW
// $_GET['compilation']; // TRUE or FALSE --- GENERER COMPILATION AVEC CREDIT ET DEBIT
// $_GET['creditdebit']; // TRUE or FALSE --- GENERER SEPAREMENT CREDIT ET DEBIT


// $host = '192.168.3.70';
// $username = 'siteconnect';
// $password = 'Azertyuiop!1';
// $database = 'recappi';
// Connexion � la base
// dbconnect = mysqli_connect($host, $username, $password, $database);

if (htmlspecialchars($_SESSION["username"]) == "debug") {
    $userrecappi = "%";
} else {
    $userrecappi = htmlspecialchars($_SESSION["username"]);
}
$filename = 'comptabilite_albinton.csv';

$resultcol = mysqli_query(dbconnect, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'albin549889' AND TABLE_NAME = 'comptabilite' ORDER BY `ORDINAL_POSITION`; ") or die("Selection Error " . mysqli_error(dbconnect));
// $resultcol = mysqli_query(dbconnect, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'albinton' AND TABLE_NAME = 'comptabilite' ORDER BY `ORDINAL_POSITION`; ") or die("Selection Error " . mysqli_error(dbconnect));
while($row = mysqli_fetch_assoc($resultcol)) {
    $header[] = $row['COLUMN_NAME'];
}

$sql = "SELECT * FROM `comptabilite` WHERE `id` <> '1' ORDER BY `id` ASC;";
// SELECT * FROM `clients` WHERE `user` like "william"AND `etatvisite` <> 'NON' ORDER BY nom ASC

// fetch mysql table rows
// $sql = "select * from tbl_books";
$result = mysqli_query(dbconnect, $sql) or die("Selection Error " . mysqli_error(dbconnect));

$fp = fopen('./'.$filename, 'w');

fputcsv($fp, $header, ';');
while($row = mysqli_fetch_assoc($result)) {
    fputcsv($fp, $row, $delimiter = ';');
}
fclose($fp);


//mysqli_close(dbconnect);
// header('Location: generation.php');
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

//Check the file exists or not
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