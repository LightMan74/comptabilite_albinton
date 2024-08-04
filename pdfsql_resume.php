<?php
  error_reporting(E_ERROR | E_PARSE);
  ini_set('display_errors', 1);
if(php_sapi_name() != 'cli') {
    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
        ?>
<script type="text/javascript">
window.location.href = "login.php";
</script>
<?php
    }
}
use Fpdf\Fpdf;
use setasign\Fpdi\Fpdi;

require_once "vendor/autoload.php";
// require('vendor/fpdf/fpdf.php');

include('config.php');
include('mysql_table.php');


$prop = array('HeaderColor' => array(255,255,210),
'color1' => array(210,245,255),
'color2' => array(255,255,255),
'padding' => 2);

ob_start();
$pdf = new PDF_MySQL_Table('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 0);

$result = mysqli_query(dbconnect, "SELECT DISTINCT `IDMOIS` FROM `comptabilite` where `id` <> 1 order by `IDMOIS` DESC;");
$annee = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) { 
$sql =
    "SELECT `IDMOIS` AS MorA, 'TOTAL' as TYPE,
    SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) AS CREDIT_TTC,
    SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DEBIT_TTC
    FROM `comptabilite` WHERE `IDMOIS` IS NOT NULL AND `IDMOIS` = '".$row["IDMOIS"]."' GROUP BY `IDMOIS`
    UNION ALL
    SELECT '' AS MorA, '' as TYPE,
    '' AS CREDIT_TTC,
    '' AS DEBIT_TTC
    UNION ALL
    SELECT `IDMOIS` AS MorA, `TYPE`,
    SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) AS CREDIT_TTC,
    SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DEBIT_TTC
    FROM `comptabilite` WHERE `IDMOIS` IS NOT NULL AND `IDMOIS` = '".$row["IDMOIS"]."' GROUP BY `TYPE` ORDER BY FIELD(`TYPE`,'','TOTAL') ASC, `TYPE` ASC;";

    $pdf->AddPage();

    $pdf->AddCol('MorA', 50, 'SAISON', 'C');
    $pdf->AddCol('TYPE', 50, 'CATEGORIE', 'C');
    $pdf->AddCol('CREDIT_TTC', 50, 'CREDIT_TTC', 'C');
    $pdf->AddCol('DEBIT_TTC', 50, 'DEBIT_TTC', 'C');

    $pdf->Table(dbconnect, $sql, $prop);

}
}



// $pdf->SetDisplayMode('fullwidth','one');

$pdf->Output('I', 'RESUMER_MOIS_ANNEE.pdf');


ob_end_flush();




?>