<?php

function modifconfig()
{

    $_SESSION["FILTRE-ID"] = "";

    $sqlconfig = "SELECT `TYPE`,`TYPE_CD` FROM `config_compta`";
    $resultconfig = mysqli_query(dbconnect, $sqlconfig);

    echo $sql;
    ?>
<?php
        $resultconfig = mysqli_query(dbconnect, $sqlconfig);
    ?>

<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">

    <table id="searchtable_config" class="blueTable tableFixHead">

        <tr>
            <th colspan="2">
                <font>CONFIGURATION</font>
            </th>
        </tr>
        <tr>
            <th>
                <font>TYPE</font>
            </th>
            <th>
                <font>1 - COMPTE COURANT / 2 - COMPTE EPARGNE</font>
            </th>
        </tr>

        <?php
    if (mysqli_num_rows($resultconfig) > 0) {
        while ($rowinter = mysqli_fetch_assoc($resultconfig)) {
            ?>
        <tr>
            <th>
                <input name="T[]" value="<?php echo $rowinter["TYPE"]; ?>">
            </th>
            <th>
                <input name="SOLDE[]" value="<?php echo $rowinter["TYPE_CD"]; ?>">
            </th>
        </tr>


        <?php
        } ?>

        <?php
    } ?>



    </Table>

    </br></br>
    <input type="button" class="btn btn-outline-primary" onclick="addRow();" value="ajouter un champ" id="xxx" />
    </br></br></br>

    <input class="btn btn-warning" name="configmodif" value="MODIFIER" type="submit">

</form>
<script>
function addRow() {
    // Get the table element in which you want to add row
    let table = document.getElementById("searchtable_config");

    // Create a row using the inserRow() method and
    // specify the index where you want to add the row
    let row = table.insertRow(-1); // We are adding at the end

    // Create table cells
    let c0 = row.insertCell(0);
    let c1 = row.insertCell(1);

    // Add data to c1 and c2
    c0.innerHTML = '<input name="T[]" value="">';
    c1.innerHTML = '<input name="SOLDE[]" value="">';

}
</script>



<?php
}


function modifconfigupdate()
{
    // print_r($_POST);

    // var_dump($_POST['T']);
    // var_dump($_POST['TCD']);
   
    $_POST['T'] = array_values(array_filter($_POST['T']));
    $_POST['SOLDE'] = array_values(array_filter($_POST['SOLDE']));
    // exit;
    $itemscount = 0;
    foreach($_POST['T'] as $k => $v) {
        echo '<script>console.log("Items:*' . $_POST['T'][$itemscount] . "* " . $itemscount . '");</script>';
        if ($_POST['T'][$itemscount] !== "") {
            ++$itemscount;
        }
    }

    $sqlitems = "";
    for ($i = 0; $i <= $itemscount - 1; $i++) {
        $sqlitems .= "('" . strtoupper($_POST['T'][$i])."','".strtoupper($_POST['SOLDE'][$i]). "'),";
    }

    $sqlitems = rtrim($sqlitems, ",");
    // echo '<script>console.log("PHP:' . $sqlconfig . '");</script>';
    $sqlconfig = "truncate `config_compta`;";
    mysqli_query(dbconnect, $sqlconfig);
    $sqlconfig = "INSERT INTO `config_compta`(`TYPE`,`TYPE_CD`) VALUES " . $sqlitems.";";
    // PopUpMsg($sqlconfig);
    //Logs
    $sqlogs = 'INSERT INTO `logs_compta` (`user`, `action`) VALUES ("' . $_SESSION["username"] . '","' . $sqlconfig . '")';

    $ww = mysqli_query(dbconnect, $sqlogs);

    //    PopUpMsg($sqlconfig);

    // }
    echo '<script>console.log("PHP:' . $sqlconfig . '");</script>';
    if (mysqli_query(dbconnect, $sqlconfig)) {
        PopUpMsg("MODIFICATION EFFECTUER.");
    } else {
        echo '<script>console.log("Error:' . mysqli_error(dbconnect) . '");</script>';
        // PopUpMsg("Error: " . mysqli_error(dbconnect));
    }

    //mysqli_close(dbconnect);
    loadpieces();
}


?>