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

require_once('config.php');
require_once('mysql_table.php');

if (!file_exists('../../../EXPORT_COMPTABLE/files_sql/pdffiles')) {
    mkdir('../../../EXPORT_COMPTABLE/files_sql/pdffiles', 0777, true);
}
if (!file_exists('files_sql/pdffiles')) {
    mkdir('files_sql/pdffiles', 0777, true);
}
if (!file_exists('files_sql/pdffiles/RESUMER')) {
    mkdir('files_sql/pdffiles/RESUMER', 0777, true);
}

$prop = array('HeaderColor' => array(255,255,210),
'color1' => array(210,245,255),
'color2' => array(255,255,255),
'padding' => 2);

// $arr_annee = array('2024','2023','2023','2022','2022','2021');
// $arr_mois = array('06','07','06','07','06','03');
require_once('variableannee.php');
ob_start();
$pdf = new PDF_MySQL_Table('P', 'mm', 'A4');
// $pdf->SetTopMargin(5);
$pdf->SetAutoPageBreak(true, 0);
for ($i = 0;$i < count($arr_annee);$i += 2) {

    $anneePLUS = $arr_annee[$i];
    $moisPLUS = $arr_mois[$i];
    $anneeMOINS = $arr_annee[$i + 1];
    $moisMOINS = $arr_mois[$i + 1];

    // $sql = "SELECT `IDMOIS`,sum(`HT`) as HT,sum(`TVA_MONTANT`) as TVA,sum(`TTC`) as TTC FROM `comptabilite` where `CREDIT` is null group by `IDMOIS`;";

    $sql =
    "SELECT 
`IDMOIS` AS MorA,  
SUM(case when `CREDIT` IS NOT NULL then `HT` else 0 end) AS CREDIT_HT,
SUM(case when `CREDIT` IS NOT NULL then `TVA_MONTANT` else 0 end) AS CREDIT_TVA,
SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) AS CREDIT_TTC,
SUM(case when `CREDIT` IS NOT NULL AND `COMPTE` LIKE '706%' then `HT` else 0 end) AS CREDIT_MO_HT,
SUM(case when `CREDIT` IS NOT NULL AND `COMPTE` LIKE '707%' then `HT` else 0 end) AS CREDIT_P_HT,  
SUM(case when `CREDIT` IS NULL then `HT` else 0 end) AS DEBIT_HT,
SUM(case when `CREDIT` IS NULL then `TVA_MONTANT` else 0 end) AS DEBIT_TVA,
SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DEBIT_TTC,
SUM(case when `CREDIT` IS NULL AND `COMPTE` LIKE '601%' then `HT` else 0 end) AS DEBIT_P_HT,  
SUM(case when `CREDIT` IS NOT NULL then `HT` else 0 end) - SUM(case when `CREDIT` IS NULL then `HT` else 0 end) AS DIFF_HT,
SUM(case when `CREDIT` IS NOT NULL then `TVA_MONTANT` else 0 end) - SUM(case when `CREDIT` IS NULL then `TVA_MONTANT` else 0 end) AS DIFF_TVA,
SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) - SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DIFF_TTC  
FROM `comptabilite`  WHERE `IDMOIS` IS NOT NULL AND `IDMOIS` BETWEEN '".$anneeMOINS."-".$moisMOINS."' AND '".$anneePLUS."-".$moisPLUS."'
GROUP BY `IDMOIS`  
UNION ALL  
SELECT 
CASE
WHEN `IDMOIS` BETWEEN '".$anneeMOINS."-".$moisMOINS."' AND '".$anneePLUS."-".$moisPLUS."' THEN '".$anneeMOINS."-".$anneePLUS."'
END AS MorA,
SUM(case when `CREDIT` IS NOT NULL then `HT` else 0 end) AS CREDIT_HT,
SUM(case when `CREDIT` IS NOT NULL then `TVA_MONTANT` else 0 end) AS CREDIT_TVA,
SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) AS CREDIT_TTC,
SUM(case when `CREDIT` IS NOT NULL AND `COMPTE` LIKE '706%' then `HT` else 0 end) AS CREDIT_MO_HT,
SUM(case when `CREDIT` IS NOT NULL AND `COMPTE` LIKE '707%' then `HT` else 0 end) AS CREDIT_P_HT,
SUM(case when `CREDIT` IS NULL then `HT` else 0 end) AS DEBIT_HT,
SUM(case when `CREDIT` IS NULL then `TVA_MONTANT` else 0 end) AS DEBIT_TVA,
SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DEBIT_TTC,
SUM(case when `CREDIT` IS NULL AND `COMPTE` LIKE '601%' then `HT` else 0 end) AS DEBIT_P_HT,
SUM(case when `CREDIT` IS NOT NULL then `HT` else 0 end) - SUM(case when `CREDIT` IS NULL then `HT` else 0 end) AS DIFF_HT,
SUM(case when `CREDIT` IS NOT NULL then `TVA_MONTANT` else 0 end) - SUM(case when `CREDIT` IS NULL then `TVA_MONTANT` else 0 end) AS DIFF_TVA,
SUM(case when `CREDIT` IS NOT NULL then `TTC` else 0 end) - SUM(case when `CREDIT` IS NULL then `TTC` else 0 end) AS DIFF_TTC
FROM `comptabilite` WHERE `IDMOIS` IS NOT NULL AND `IDMOIS` BETWEEN '".$anneeMOINS."-".$moisMOINS."' AND '".$anneePLUS."-".$moisPLUS."'
GROUP BY MorA
ORDER BY CHAR_LENGTH(MorA) DESC, MorA DESC;";

    // echo $sql.' $newlinedynamique $newlinedynamique $newlinedynamique';

    // $pdf = new PDF();
    // $pdf = new PDF('L','mm','A3');
    $pdf->AddPage();
    $pdf->AddCol('MorA', 50, 'MorA', 'C');
    $pdf->AddCol('CREDIT_HT', 50, 'CREDIT_HT', 'C');
    $pdf->AddCol('CREDIT_TVA', 50, 'CREDIT_TVA', 'C');
    $pdf->AddCol('CREDIT_TTC', 50, 'CREDIT_TTC', 'C');
    $pdf->Table($dbconnect, $sql, $prop);

    $pdf->AddCol('MorA', 50, 'MorA', 'C');
    $pdf->AddCol('DEBIT_HT', 50, 'DEBIT_HT', 'C');
    $pdf->AddCol('DEBIT_TVA', 50, 'DEBIT_TVA', 'C');
    $pdf->AddCol('DEBIT_TTC', 50, 'DEBIT_TTC', 'C');
    $pdf->Table($dbconnect, $sql, $prop);

    $pdf->AddCol('MorA', 50, 'MorA', 'C');
    $pdf->AddCol('DIFF_HT', 50, 'DIFF_HT', 'C');
    $pdf->AddCol('DIFF_TVA', 50, 'DIFF_TVA', 'C');
    $pdf->AddCol('DIFF_TTC', 50, 'DIFF_TTC', 'C');
    $pdf->Table($dbconnect, $sql, $prop);

    $pdf->AddPage();
    $pdf->AddCol('MorA', 50, 'MorA', 'C');
    $pdf->AddCol('CREDIT_MO_HT', 50, 'CREDIT_MO_HT', 'C');
    $pdf->AddCol('CREDIT_P_HT', 50, 'CREDIT_P_HT', 'C');
    $pdf->AddCol('DEBIT_P_HT', 50, 'DEBIT_P_HT', 'C');
    $pdf->Table($dbconnect, $sql, $prop);


}



// $pdf->SetDisplayMode('fullwidth','one');

$pdf->Output('F', 'files_sql/pdffiles/RESUMER/RESUMER_MOIS_ANNEE.pdf');


ob_end_flush();
$pdf = new Fpdi('L', 'mm', "A4");
$pageCount = $pdf->setSourceFile('files_sql/pdffiles/RESUMER/RESUMER_MOIS_ANNEE.pdf');
echo 'PDF RESUMER_MOIS_ANNEE GENERER ' .$pageCount ." Pages  $newlinedynamique ";


include_once('function_sql.php');
folderrecurseCopy('files_sql/pdffiles/RESUMER', '../../../EXPORT_COMPTABLE/files_sql/pdffiles', 'RESUMER');



?>