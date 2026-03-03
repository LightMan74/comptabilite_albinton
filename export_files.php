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


require_once 'vendor/autoload.php';
// require_once 'dbconnect.php'; // Votre connexion existante
include "config.php";

use setasign\Fpdi\Fpdi;

// ============================================================
// CONFIGURATION
// ============================================================
$output_dir = './exports/';

// ============================================================
// CRÉATION DU DOSSIER DE SORTIE
// ============================================================
if (!is_dir($output_dir)) {
    mkdir($output_dir, 0755, true);
}

// ============================================================
// RÉCUPÉRATION DES DONNÉES
// ============================================================
// $stmt = $pdo->query("SELECT name, extension, file FROM compta_files");
// $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
$result = mysqli_query(dbconnect, "SELECT name, extension, file FROM compta_files");
$files  = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (empty($files)) {
    die("Aucun fichier trouvé dans compta_files.");
}

// ============================================================
// TRAITEMENT ET EXPORT
// ============================================================
$success = 0;
$errors  = [];

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

    $filename = $output_dir . sanitize_filename($name) . '.pdf';

    // ============================================================
    // CAS 1 : PDF → Import via FPDI
    // ============================================================
    if ($extension === 'pdf') {
        try {
            $tmp_pdf = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.pdf';
            file_put_contents($tmp_pdf, $decoded);

            $fpdi = new Fpdi();
            $pageCount = $fpdi->setSourceFile($tmp_pdf);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $fpdi->importPage($i);
                $size  = $fpdi->getTemplateSize($tplId);

                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $fpdi->AddPage($orientation, [$size['width'], $size['height']]);
                $fpdi->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            }

            $fpdi->Output('F', $filename);
            unlink($tmp_pdf);

            $success++;
            echo "✅ PDF exporté ({$pageCount} page(s)) : {$filename}<br>";

        } catch (Exception $e) {
            $errors[] = "❌ Erreur PDF [{$name}] : " . $e->getMessage();
        }
    }

    // ============================================================
    // CAS 2 : IMAGE → Conversion en PDF via FPDI
    // ============================================================
    elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        try {
            $tmp_image = sys_get_temp_dir() . '/' . uniqid('img_') . '.' . $extension;
            file_put_contents($tmp_image, $decoded);

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

            $fpdi = new Fpdi();
            $fpdi->AddPage('P', 'A4');
            $fpdi->Image($tmp_image, $x, $y, $final_width, $final_height, $img_type);
            $fpdi->Output('F', $filename);

            unlink($tmp_image);

            $success++;
            echo "✅ Image → PDF : {$filename}<br>";

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
// RÉSUMÉ
// ============================================================
echo "<hr>";
echo "✅ Succès : {$success} fichier(s) exporté(s)<br>";
echo "❌ Erreurs : " . count($errors) . "<br>";
foreach ($errors as $error) {
    echo "&nbsp;&nbsp;→ {$error}<br>";
}

// ============================================================
// FONCTION UTILITAIRE
// ============================================================
function sanitize_filename(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
    return trim($name, '_');
}