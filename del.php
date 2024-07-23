<?php
session_start();
require_once "config.php";
$id = $_POST["ID"];

if ((isset($_POST['delitem']) || isset($_POST['delitemfile'])) && $_POST["confdel"] == $id) {
    // $id = $_POST["ID"];
    if (isset($_POST['delitemfile'])) {
        $sql = "DELETE FROM `compta_files` WHERE `id` = '$id'";
    } else {
        $sql = "DELETE FROM `comptabilite` WHERE `id` = '$id'";
        $sqlfile = "DELETE FROM `compta_files` WHERE `idclient` = '$id'";
    }

    if(mysqli_query(dbconnect, $sql)) {
        if (isset($_POST['delitemfile'])) {
            PopUpMsg("SUPPRESSION EFFECTUER.");
        } else {
            PopUpMsg("SUPPRESSION EFFECTUER 1/2");
        }
    } else {
        PopUpMsg("SUPPRESSION Error :" . mysqli_error(dbconnect));
    }

    if (isset($_POST['delitem'])) {
        dbconnect->next_result();
        if(mysqli_query(dbconnect, $sqlfile)) {
            PopUpMsg("SUPPRESSION EFFECTUER 2/2");
        } else {
            PopUpMsg("SUPPRESSION Error :" . mysqli_error(dbconnect));
        }
    }
    // mysqli_close(dbconnect);
    ?>
<script type="text/javascript">
	window.location.href = "liste.php";
</script>
<?php
    exit();

}
if (isset($_POST['confdel']) && $_POST["confdel"] != $id) {
    ?>
<script type="text/javascript">
	window.location.href = "liste.php";
</script>
<?php
    exit();
}


if (isset($_POST['confitem']) || isset($_POST['confitemfile'])) {
    // $id = $_POST["ID"];
    include "delform.php";
    ?>

<script type="text/javascript">
	toogleForm('del-popup');
</script>

<?php
    $_SESSION['keepfiltre'] = true;
}
?>