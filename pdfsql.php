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
$result = mysqli_query(dbconnect, "SELECT DISTINCT `IDMOIS` FROM `comptabilite` where `id` <> 1 order by `IDMOIS` DESC;");

if (mysqli_num_rows($result) > 0) {
    $pdf = new PDF_MySQL_Table('L', 'mm', 'A3');
    while ($row = mysqli_fetch_assoc($result)) {
        ob_start();
        // echo $row["IDMOIS"] . "<br>";
            $sql = "SELECT `id`, `DATE_FACTURE`, `IDMOIS`, `DEBIT`, `CREDIT`, `TYPE`, `TTC`, `CLIENTS_FOURNISEUR`, `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `VIR`, `ISERROR`, IF(`VIR` > `TTC`,1,0) AS totalpayementup, IF(`VIR` < `TTC`,1,0) AS totalpayementdown FROM `comptabilite` WHERE `id` <> '1' AND  `IDMOIS` = '" . $row["IDMOIS"]."' ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC";
            // echo $sql . "<br>";
            
        $pdf->AddPage();
        $pdf->AddCol('id', 10, 'id', 'C');
        $pdf->AddCol('DATE_FACTURE', 30, 'D_FACTURE', 'C');
        $pdf->AddCol('IDMOIS', 30, 'SAISON', 'C');
        $pdf->AddCol('DEBIT', 5, 'D', 'C');
        $pdf->AddCol('CREDIT', 5, 'C', 'C');
        $pdf->AddCol('TYPE', 50, 'TYPE', 'C');
        $pdf->AddCol('TTC', 30, 'TTC', 'C');
        $pdf->AddCol('CLIENTS_FOURNISEUR', 50, 'FOURNISEUR', 'C');
        $pdf->AddCol('REMARQUE_DIVERSE', 100, 'REMARQUE', 'C');
        $pdf->AddCol('DATE_PAYEMENT', 30, 'D_PAYEMENT', 'C');
        $pdf->AddCol('VIR', 30, 'TTC PAYER', 'C');
        $pdf->AddCol('ISERROR', 30, 'ISERROR', 'C');

        $pdf->Table(dbconnect, $sql, $prop);

    }
    
    $pdf->Output('I', 'COMPTA_ALB.pdf');  
    ob_end_flush();
}

?>