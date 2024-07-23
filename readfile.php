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
                <!-- <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post"> -->
                <input name="idfiles" type="text" maxlength="255" value="<?php echo $row["id"]; ?>" style="display:none" />
                <input class="btn menu btn-warning" type="submit" name="viewfilecompta" value="<?php echo $row['name']; ?>" formtarget="_viewcomptafile" />
                <!-- </form> -->
            </th>
            <th>
                <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
                    <input name="ID" type="text" maxlength="255" value="<?php echo $row["id"]; ?>" style="display:none" />
                    <input name="IDCLIENT" type="text" maxlength="255" value="<?php echo $id; ?>" style="display:none" />
                    <input class="btn btn-danger" type="submit" name="confitemfile" value="SUPRIMER" />
                </form>
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