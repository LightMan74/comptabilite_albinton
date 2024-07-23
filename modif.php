<?php

session_start();
// include "config.php";

// global dbconnect;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $itemscount = 0;
    foreach($_POST['HT'] as $k => $v) {
        if ($_POST['HT'][$itemscount] !== "") {
            ++$itemscount;
        }
    }
    $itemsinitcount = 0;
    foreach($_POST['IDINIT'] as $k => $v) {
        if ($_POST['IDINIT'][$itemsinitcount] !== "") {
            ++$itemsinitcount;
        }
    }

    if (isset($_POST['modifitem']) || isset($_POST['additem'])) {        
        if (isset($_POST['additem'])) {
            $sql .= "INSERT INTO `comptabilite` SET ";
        } else {
            $sql .= "UPDATE `comptabilite` SET ";           
        }
        if ($_POST["DATE_FACTURE"] != '') {
            $sql .=  "`DATE_FACTURE`='".$_POST["DATE_FACTURE"]."',";
        }
        if ($_POST["DEBIT"] != '') {
            $sql .=  "`DEBIT`='".strtolower($_POST["DEBIT"])."',";
        } else {
            $sql .=  "`DEBIT`=NULL,";
        }
        if ($_POST["CREDIT"] != '') {
            $sql .=  "`CREDIT`='".strtolower($_POST["CREDIT"])."',";
        } else {
            $sql .=  "`CREDIT`=NULL,";
        }
        $sql .=  "`TYPE`='".strtoupper($_POST["TYPE"])."',";        
        if ($_POST["TTC"] != '') {
            $sql .=  "`TTC`='".$_POST["TTC"]."',";
        }
        $sql .=  "`CLIENTS_FOURNISEUR`='".strtoupper(str_replace("'", "\'", $_POST["CLIENT"]))."',";
        if ($_POST["REMARQUE"] != '') {
            $sql .=  "`REMARQUE_DIVERSE`='".strtoupper(str_replace("'", "\'", $_POST["REMARQUE"]))."',";
        }
        if ($_POST["DATE_PAYEMENT"] != '') {
            $sql .=  "`DATE_PAYEMENT`='".str_replace("'", "\'", $_POST["DATE_PAYEMENT"])."',";
        }
        if ($_POST["CB"] != '') {
            $sql .=  "`CB`='".$_POST["CB"]."',";
        } else {
            $sql .=  "`CB`='0.00',";
        }
        if ($_POST["VIR"] != '') {
            $sql .=  "`VIR`='".$_POST["VIR"]."',";
        } else {
            $sql .=  "`VIR`='0.00',";
        }
        if ($_POST["ESP"] != '') {
            $sql .=  "`ESP`='".$_POST["ESP"]."',";
        } else {
            $sql .=  "`ESP`='0.00',";
        }
        if ($_POST["IDMOIS"] != '') {
            $sql .=  "`IDMOIS`='".$_POST["IDMOIS"]."',";
        }
        if ($_POST["CREATE_TIMESTAMP"] != '') {
            $sql .= "`CREATE_TIMESTAMP`='".$_POST["CREATE_TIMESTAMP"]."',";
        } else {
            $sql .=  "`CREATE_TIMESTAMP`='".date("Y-m-d H:i:s")."',";
        }
        $sql = rtrim($sql, ",");
        if (isset($_POST['modifitem'])) {
            if ($_POST["ID"] != '') {
                $sql .=  " WHERE `id`='".$_POST["ID"]."'";
            }
        }
        $sql .= "; ";

        echo "\n".'<script>console.log("SQL out: ' . $sql . '"); </script>';
        if (mysqli_query(dbconnect, $sql)) {
            PopUpMsg("AJOUT/MODIF EFFECTUER.");
            echo "\n".'<script>console.log("SQL ok: ' . $sql . '"); </script>';
        } else {
            echo "\n".'<script>console.log("SQL error: ' . $sql . '"); </script>';
            PopUpMsg("AJOUT ERROR: $sql " . mysqli_error(dbconnect));
            echo "\n".'<script>console.log("SQL error: ' . mysqli_error(dbconnect) . '"); </script>';
        }

        $sql = str_replace("'", "\'", $sql);
        $sqlogs = 'INSERT INTO `logs_compta` (`user`, `action`) VALUES (\'' . $_SESSION["username"] . '\',\'' . $sql . '\')';
        if (mysqli_query(dbconnect, $sqlogs)) {
            // echo "\n".'<script>console.log("SQL : ' . $sqlogs . '"); </script>';
        } else {

        }
        $sqlselectidfile = 'SELECT id FROM comptabilite WHERE `id` <> 1 ORDER BY CREATE_TIMESTAMP DESC LIMIT 1';

        $result = mysqli_query(dbconnect, $sqlselectidfile);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $_POST["ID"] = $row["id"];
            }
        }
        include 'uploadfile.php';
        ?>

<script type="text/javascript">
window.location.href = "liste.php";
</script>
<?php
        exit();
    }
    ?>
<?php
       if ((isset($_POST['additem'])) || (isset($_POST['modifitem']))) {
           $_SESSION['keepfiltre'] = true; ?>

<script type="text/javascript">
window.location.href = "liste.php";
</script>
<?php
    } else {
        $_SESSION['keepfiltre'] = false;
    } ?>

<?php
    if (isset($_POST['openmodifitem']) || (isset($_POST['openadditem']))) {
        
        if (isset($_POST['openmodifitem'])) {
            $id = $_POST["ID"];
            $sql = "SELECT * FROM `comptabilite` WHERE `id` = $id";
        }

        if (isset($_POST['openadditem'])) {
            $sql = "SELECT * FROM `comptabilite` WHERE `id` = '1'";
        }

        $result = mysqli_query(dbconnect, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($_POST['openadditem'])) {
                    $modifierORadd = "AJOUTER";
                    $namemodifierORadd = "additem";
                } else {
                    $modifierORadd = "MODIFIER";
                    $namemodifierORadd = "modifitem";
                }
                include "modifform.php";
            }
        } else {
            PopUpMsg("0 results");
        }
    }

    if ((isset($_POST['openadditem'])) || (isset($_POST['openmodifitem']))) {
        ?>

<script type="text/javascript">
toogleForm('modif-popup');
</script>

<?php
$_SESSION['keepfiltre'] = true;
    }
}
?>