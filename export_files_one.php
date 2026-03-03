<?php

require_once 'vendor/autoload.php';
// require_once 'dbconnect.php';

include "config.php";

use setasign\Fpdi\Fpdi;

// ============================================================
// CONFIGURATION
// ============================================================
// $output_dir  = './exports/';
// $output_file = $output_dir . 'export_compta_' . date('Y-m-d_His') . '.pdf';
// $output_file = 'export_compta_files_albinton_' . date('Y-m-d_His') . '.pdf';

// ============================================================
// CRÉATION DU DOSSIER DE SORTIE
// ============================================================
// if (!is_dir($output_dir)) {
//     mkdir($output_dir, 0755, true);
// }

// ============================================================
// RÉCUPÉRATION DES DONNÉES
// ============================================================
if (isset($_GET["SAISON"])){
    $wheresql = ' WHERE `idclient` IN (SELECT `id` FROM `comptabilite` where `SAISON` = "'.$_GET["SAISON"].'")';
    $output_file = 'export_compta_files_albinton_'.$_GET["SAISON"].'_'.date('Y-m-d_His') . '.pdf';
}else{    
    $output_file = 'export_compta_files_albinton_' . date('Y-m-d_His') . '.pdf';
}

$result = mysqli_query(dbconnect, "SELECT name, extension, file FROM compta_files".$wheresql);
$files  = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (empty($files)) {
    die("Aucun fichier trouvé dans compta_files.");
}

// ============================================================
// INITIALISATION FPDI (document unique)
// ============================================================
$fpdi    = new Fpdi();
$success = 0;
$errors  = [];
$tmp_files = []; // Pour nettoyage final

foreach ($files as $row) {
    $name      = $row['name'];
    $extension = strtolower(trim($row['extension'], '. '));
    $base64    = $row['file'];

    // --- Nettoyage du préfixe data:...;base64, si présent ---
    if (strpos($base64, ';base64,') !== false) {
        $base64 = explode(';base64,', $base64)[1];
    }

    // --- Correction des espaces ---
    $base64 = str_replace(' ', '+', trim($base64));

    // --- Décodage ---
    $decoded = base64_decode($base64, true);

    if ($decoded === false) {
        $errors[] = "❌ Décodage échoué pour : {$name}.{$extension}";
        continue;
    }

    // ============================================================
    // CAS 1 : PDF → Import page par page dans le document unique
    // ============================================================
    if ($extension === 'pdf') {
        try {
            $tmp_pdf = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.pdf';
            file_put_contents($tmp_pdf, $decoded);
            $tmp_files[] = $tmp_pdf;

            $pageCount = $fpdi->setSourceFile($tmp_pdf);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $fpdi->importPage($i);
                $size  = $fpdi->getTemplateSize($tplId);

                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                $fpdi->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            }

            $success++;
            // echo "✅ PDF ajouté ({$pageCount} page(s)) : {$name}<br>";

        } catch (Exception $e) {
            $errors[] = "❌ Erreur PDF [{$name}] : " . $e->getMessage();
        }
    }

    // ============================================================
    // CAS 2 : IMAGE → Ajout dans le document unique
    // ============================================================
    elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        try {
            $tmp_image = sys_get_temp_dir() . '/' . uniqid('img_') . '.' . $extension;
            file_put_contents($tmp_image, $decoded);
            $tmp_files[] = $tmp_image;

            $imgInfo = getimagesize($tmp_image);
            if (!$imgInfo) {
                throw new Exception("Impossible de lire les dimensions de l'image.");
            }

            // Conversion pixels → mm (96 DPI)
            $img_width_mm  = ($imgInfo[0] / 96) * 25.4;
            $img_height_mm = ($imgInfo[1] / 96) * 25.4;

            // Dimensions A4
            $a4_width  = 210;
            $a4_height = 297;

            // Redimensionnement proportionnel
            $ratio        = min($a4_width / $img_width_mm, $a4_height / $img_height_mm);
            $final_width  = $img_width_mm  * $ratio;
            $final_height = $img_height_mm * $ratio;

            // Centrage
            $x = ($a4_width  - $final_width)  / 2;
            $y = ($a4_height - $final_height) / 2;

            $img_type = strtoupper($extension === 'jpg' ? 'jpeg' : $extension);

            $fpdi->AddPage('P', 'A4');
            $fpdi->Image($tmp_image, $x, $y, $final_width, $final_height, $img_type);

            $success++;
            // echo "✅ Image ajoutée : {$name}<br>";

        } catch (Exception $e) {
            $errors[] = "❌ Erreur Image [{$name}] : " . $e->getMessage();
        }
    }

    // ============================================================
    // CAS 3 : Extension non supportée
    // ============================================================
    else {
        $errors[] = "⚠️ Extension non supportée : {$name}.{$extension}";
    }
}

// ============================================================
// SAUVEGARDE DU DOCUMENT UNIQUE
// ============================================================
if ($success > 0) {
    // $fpdi->Output('F', $output_file);
    $fpdi->Output('I', $output_file);
    // echo "<hr>📄 Fichier PDF généré : <strong>{$output_file}</strong><br>";
} else {
    // echo "<hr>⚠️ Aucun fichier valide traité, PDF non généré.<br>";
}

// ============================================================
// NETTOYAGE DES FICHIERS TEMPORAIRES
// ============================================================
foreach ($tmp_files as $tmp) {
    if (file_exists($tmp)) {
        unlink($tmp);
    }
}

// ============================================================
// RÉSUMÉ
// ============================================================
// echo "<hr>";
// echo "✅ Succès : {$success} fichier(s) ajouté(s)<br>";
// echo "❌ Erreurs : " . count($errors) . "<br>";
foreach ($errors as $error) {
    // echo "&nbsp;&nbsp;→ {$error}<br>";
}

// ============================================================
// FONCTION UTILITAIRE
// ============================================================
function sanitize_filename(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
    return trim($name, '_');
}