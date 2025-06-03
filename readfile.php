<?php

function readfileform($idcall = 1)
{

    // include "config_upload.php";

    $id = $idcall;

    $images_sql = "SELECT * FROM compta_files where idclient = $id ORDER BY id asc";

    $result = mysqli_query(dbconnect, $images_sql);

    ?>
<br>
<table id="searchtable" class="blueTable blueTableIN tableFixHead">
    <thead>
        <tr>
            <th style="width:80%">
                <font>FICHIER</font>
            </th>
            <th>
                <font>OPTION</font>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
while ($row = mysqli_fetch_assoc($result)) {
    ?>
            <th>
                <input class="btn menu btn-warning" type="submit" onclick="submitfiles(<?php echo $row['id'];?>,0)" name="viewfilecompta" value="<?php echo $row['name']; ?>" />
            </th>
            <th>
                <input class="btn btn-danger" name="confitemfile" value="SUPRIMER" type="submit" onclick="submitfiles(<?php echo $row['id'].','.$id;?>)" />
            </th>
        </tr>
        <?php
}
    ?>
    </tbody>
</Table>

<?php
}
?>

<input name="btnsubmit0" id="btnsubmit0" type="text" maxlength="255" value="" style="display:none;" />
<input name="btnsubmit1" id="btnsubmit1" type="text" maxlength="255" value="" style="display:none;" />
<script>
function submitfiles(b0, b1) {
    document.getElementById('btnsubmit0').value = b0;
    document.getElementById('btnsubmit1').value = b1;
}
</script>