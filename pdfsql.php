<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
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


if (isset($_GET['intervaldate'])) {
    $datetime1 = date_create($_GET['intervaldate']);
} else {
    $datetime1 = date_create('2021-04-01');
}
$datetime2 = date_create(date('Y-m-01'));
$interval = date_diff($datetime1, $datetime2);
if (isset($_GET['interval'])) {
    $intervalmois = $_GET['interval'] - 1;
} else {
    $intervalmois = ($interval->format('%m') + ($interval->format('%y') * 12));
}

$prop = array('HeaderColor' => array(255,255,210),
'color1' => array(210,245,255),
'color2' => array(255,255,255),
'padding' => 2);


for ($j = 0; $j <= 5; $j++) {
    if(isset($_GET['compilation'])) {
        if($_GET['compilation'] == 'false') {
            if ($j == 0 || $j == 5) {
                continue;
            }
        }
    }
    if(isset($_GET['creditdebit'])) {
        if($_GET['creditdebit'] == 'false') {
            if ($j == 1 || $j == 2 || $j == 3 || $j == 4) {
                continue;
            }
        }
    }

    for ($i = 0; $i <= $intervalmois; $i++) {
        ob_start();

        if ($j == '0') {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND  `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND  `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = '';
        } elseif ($j == '1') {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND `CREDIT` IS NOT NULL AND `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND `CREDIT` IS NOT NULL AND `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = '';
        } elseif ($j == '2') {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND `CREDIT` IS NULL AND `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`,`ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull,  IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND `CREDIT` IS NULL AND `IDMOIS` = '" . date('Y-m', strtotime(date('Y-m-01')." -$i MONTH"))."'ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = '';
        } elseif ($j == '3') {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`CREDIT` IS NOT NULL AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0')) OR (`CREDIT` IS NOT NULL AND `ISERROR` = '2') OR (`CREDIT` IS NOT NULL AND (`COMPTE` IS NULL OR `COMPTE` = '')) OR (`CREDIT` IS NOT NULL AND (`COMPTE_E` IS NULL OR `COMPTE_E` = '')) ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`CREDIT` IS NOT NULL AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0')) OR (`CREDIT` IS NOT NULL AND `ISERROR` = '2') OR (`CREDIT` IS NOT NULL AND (`COMPTE` IS NULL OR `COMPTE` = '')) OR (`CREDIT` IS NOT NULL AND (`COMPTE_E` IS NULL OR `COMPTE_E` = '')) ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = 'ERROR';
            $i = $intervalmois;
        } elseif ($j == '4') {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`CREDIT` IS NULL AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0')) OR (`CREDIT` IS NULL AND `ISERROR` = '2') OR (`UPLOAD_COMPTA` <> '1'and `CREDIT` IS NULL and cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) >= '20230701') ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`CREDIT` IS NULL AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0')) OR (`CREDIT` IS NULL AND `ISERROR` = '2') OR (`UPLOAD_COMPTA` <> '1'and `CREDIT` IS NULL and cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) >= '20230701') ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = 'ERROR';
            $i = $intervalmois;
        } else {
            $sql = "SELECT `id`,`N_FACTURE`, `DATE_FACTURE`, `DEBIT`, `CREDIT`, `TYPE`, `TVA_TAUX`, `HT`, `TVA_MONTANT`, `TTC`, `T_HT`, `T_TVA`, `T_TTC`, `CLIENTS_FOURNISEUR`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0') OR `ISERROR` = '2' OR (`UPLOAD_COMPTA` <> '1' and `CREDIT` IS NULL and cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) >= '20230701') OR (`CREDIT` IS NOT NULL AND (`COMPTE` IS NULL OR `COMPTE` = '')) OR (`CREDIT` IS NOT NULL AND (`COMPTE_E` IS NULL OR `COMPTE_E` = '')) ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $sql2 = "SELECT `REMARQUE_DIVERSE`, `DATE_PAYEMENT`, `CB`, `VIR`, `ESP`, `CHQ`, `BANQUE`, `N_CHEQUE`, `TITULAIRE_CHEQUE`, `TOTAL_PAYEMENT`, `RBS`, `IDMOIS`, `COMPTE`, `COMPTE_E`, `timestamp`, `id`, `ISERROR`,`UPLOAD_COMPTA`, IF((`COMPTE` IS NULL OR `COMPTE` =''),1,0) AS comptenull, IF((`COMPTE_E` IS NULL OR `COMPTE_E` =''),1,0) AS compte_enull, IF(`TOTAL_PAYEMENT` > `T_TTC`,1,0) AS totalpayementup, IF(`TOTAL_PAYEMENT` < `T_TTC`,1,0) AS totalpayementdown, IF((`IDMOIS` IS NULL OR `IDMOIS` = ''),1,0) AS idmoiserror FROM `comptabilite` WHERE `id` <> '1' AND (`TOTAL_PAYEMENT` <> `T_TTC` AND `ISERROR` = '0') OR `ISERROR` = '2' OR (`UPLOAD_COMPTA` <> '1'and `CREDIT` IS NULL and cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) >= '20230701') OR (`CREDIT` IS NOT NULL AND (`COMPTE` IS NULL OR `COMPTE` = '')) OR (`CREDIT` IS NOT NULL AND (`COMPTE_E` IS NULL OR `COMPTE_E` = '')) ORDER BY `DEBIT` ASC, cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC, `N_FACTURE` DESC";
            $iserrorstr = 'ERROR';
            $i = $intervalmois;
        }

        // $pdf = new PDF();
        // $pdf = new PDF('L','mm','A3');
        $pdf = new PDF_MySQL_Table('L', 'mm', array(420,600));
        $pdf->AddPage();

        $pdf->AddCol('id', 15, 'id', 'C');
        $pdf->AddCol('N_FACTURE', 120, 'N_FACTURE', 'C');
        $pdf->AddCol('DATE_FACTURE', 30, 'D_FACTURE', 'C');
        $pdf->AddCol('DEBIT', 10, 'D', 'C');
        $pdf->AddCol('CREDIT', 10, 'C', 'C');
        $pdf->AddCol('TYPE', 80, 'TYPE', 'C');
        $pdf->AddCol('TVA_TAUX', 30, 'TVA_TAUX', 'C');
        $pdf->AddCol('HT', 30, 'HT', 'C');
        $pdf->AddCol('TVA_MONTANT', 30, 'TVA_MONT', 'C');
        $pdf->AddCol('TTC', 30, 'TTC', 'C');
        $pdf->AddCol('T_HT', 30, 'T_HT', 'C');
        $pdf->AddCol('T_TVA', 30, 'T_TVA', 'C');
        $pdf->AddCol('T_TTC', 30, 'T_TTC', 'C');
        $pdf->AddCol('CLIENTS_FOURNISEUR', 120, 'CLIENTS_FOURNISEUR', 'C');
        // $pdf->AddCol('comptenull',0,'comptenull','C');

        $pdf->Table($dbconnect, $sql, $prop);
        $pdf->SetDisplayMode('fullwidth', 'two');
        if ($j == '0') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_01.pdf');
        } elseif ($j == '1') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_01.pdf');
        } elseif ($j == '2') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_01.pdf');
        } elseif ($j == '3') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_01.pdf');
        } elseif ($j == '4') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_01.pdf');
        } else {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_01.pdf');
        }

        $pdf = new PDF_MySQL_Table('L', 'mm', array(420,600));
        $pdf->AddPage();
        $pdf->AddCol('REMARQUE_DIVERSE', 100, 'REMARQUE_DIVERSE', 'C');
        $pdf->AddCol('DATE_PAYEMENT', 30, 'D_PAYEMENT', 'C');
        $pdf->AddCol('CB', 30, 'CB', 'C');
        $pdf->AddCol('VIR', 30, 'VIR', 'C');
        $pdf->AddCol('ESP', 30, 'ESP', 'C');
        $pdf->AddCol('CHQ', 30, 'CHQ', 'C');
        $pdf->AddCol('BANQUE', 40, 'BANQUE', 'C');
        $pdf->AddCol('N_CHEQUE', 40, 'N_CHEQUE', 'C');
        $pdf->AddCol('TITULAIRE_CHEQUE', 60, 'TITULAIRE_CHEQUE', 'C');
        $pdf->AddCol('TOTAL_PAYEMENT', 30, 'T_PAYEMENT', 'C');
        $pdf->AddCol('RBS', 40, 'RBS', 'C');
        $pdf->AddCol('IDMOIS', 20, 'IDMOIS', 'C');
        $pdf->AddCol('COMPTE', 30, 'COMPTE', 'C');
        $pdf->AddCol('COMPTE_E', 30, 'COMPTE_E', 'C');
        $pdf->AddCol('timestamp', 40, 'timestamp', 'C');
        $pdf->AddCol('id', 15, 'id', 'C');
        // $pdf->AddCol('comptenull',0,'comptenull','C');

        $pdf->Table($dbconnect, $sql2, $prop);
        $pdf->SetDisplayMode('fullwidth', 'two');
        if ($j == '0') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_02.pdf');
        } elseif ($j == '1') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_02.pdf');
        } elseif ($j == '2') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_02.pdf');
        } elseif ($j == '3') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_02.pdf');
        } elseif ($j == '4') {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_02.pdf');
        } else {
            $pdf->Output('F', 'files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_02.pdf');
        }

        ob_end_flush();
        if ($j == '0') {
            echo 'PDF PART GENERER '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).' / ';
        } elseif ($j == '1') {
            echo 'PDF PART GENERER CREDIT '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).' / ';
        } elseif ($j == '2') {
            echo 'PDF PART GENERER DEBIT '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).' / ';
        } elseif ($j == '3') {
            echo 'PDF PART GENERER CREDIT '.$iserrorstr.' / ';
        } elseif ($j == '4') {
            echo 'PDF PART GENERER DEBIT '.$iserrorstr.' / ';
        } else {
            echo 'PDF PART GENERER '.$iserrorstr.' / ';
        }
        $pdf = new Fpdi('L', 'mm', "A4");
        if ($j == '0') {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_01.pdf');
        } elseif ($j == '1') {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_01.pdf');
        } elseif ($j == '2') {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_01.pdf');
        } elseif ($j == '3') {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_01.pdf');
        } elseif ($j == '4') {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_01.pdf');
        } else {
            $pageCount = $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_01.pdf');
        }
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $pdf->AddPage();
            if ($j == '0') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_01.pdf');
            } elseif ($j == '1') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_01.pdf');
            } elseif ($j == '2') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_01.pdf');
            } elseif ($j == '3') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_01.pdf');
            } elseif ($j == '4') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_01.pdf');
            } else {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_01.pdf');
            }
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, null, null, true);

            $pdf->AddPage();
            if ($j == '0') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_02.pdf');
            } elseif ($j == '1') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_02.pdf');
            } elseif ($j == '2') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_02.pdf');
            } elseif ($j == '3') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_02.pdf');
            } elseif ($j == '4') {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_02.pdf');
            } else {
                $pdf->setSourceFile('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_02.pdf');
            }
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, null, null, true);
        }

        $pdf->SetDisplayMode('fullwidth', 'two');
        if ($j == '0') {
            $pdf->Output('F', 'files_sql/pdffiles/COMPILATION/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")). $newlinedynamique;
        } elseif ($j == '1') {
            $pdf->Output('F', 'files_sql/pdffiles/CREDIT/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_CREDIT_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")). $newlinedynamique;
        } elseif ($j == '2') {
            $pdf->Output('F', 'files_sql/pdffiles/DEBIT/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")).'_DEBIT_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.date('Y-m', strtotime(date('Y-m-01')." -$i MONTH")). $newlinedynamique;
        } elseif ($j == '3') {
            $pdf->Output('F', 'files_sql/pdffiles/ERREUR/SQL-COMPTA_'.$iserrorstr.'_CREDIT.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_CREDIT_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.$iserrorstr. $newlinedynamique;
        } elseif ($j == '4') {
            $pdf->Output('F', 'files_sql/pdffiles/ERREUR/SQL-COMPTA_'.$iserrorstr.'_DEBIT.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_DEBIT_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.$iserrorstr. $newlinedynamique;
        } else {
            $pdf->Output('F', 'files_sql/pdffiles/COMPILATION/SQL-COMPTA_'.$iserrorstr.'.pdf');
            copy('files_sql/pdffiles/COMPILATION/SQL-COMPTA_'.$iserrorstr.'.pdf', 'files_sql/pdffiles/ERREUR/SQL-COMPTA_'.$iserrorstr.'.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_01.pdf');
            unlink('files_sql/pdffiles/SQL-COMPTA_'.$iserrorstr.'_02.pdf');
            echo $pageCount .' Pages / PDF COMPI GENERER '.$iserrorstr. $newlinedynamique;
        }
    }
}
include_once('function_sql.php');
folderrecurseCopy('files_sql/pdffiles/COMPILATION', '../../../EXPORT_COMPTABLE/files_sql/pdffiles', 'COMPILATION');
folderrecurseCopy('files_sql/pdffiles/CREDIT', '../../../EXPORT_COMPTABLE/files_sql/pdffiles', 'CREDIT');
folderrecurseCopy('files_sql/pdffiles/DEBIT', '../../../EXPORT_COMPTABLE/files_sql/pdffiles', 'DEBIT');
folderrecurseCopy('files_sql/pdffiles/ERREUR', '../../../EXPORT_COMPTABLE/files_sql/pdffiles', 'ERREUR');


?>