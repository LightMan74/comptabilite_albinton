<?php


if (isset($_POST['viewfilecompta'])) {
    // include "config_upload.php";

    $id = $_POST['idfiles'];

    $images_sql = "SELECT * FROM compta_files where id = $id";
    echo $images_sql;
    $result = mysqli_query(dbconnect, $images_sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $extensions_img_arr = array("jpg","jpeg","png","gif");
        $extensions_data = "";
        if(in_array($row['extension'], $extensions_img_arr)) {
            // echo '<th><img src=data:image/'.$row['extension'].';base64,'. $row['file'].' width="33%" height="auto"><br>'.$row['name'].'</th>';
            $extensions_data = 'data:image/'.$row['extension'];
        } else {
            // echo '<th><a download="'.$row['name'].'" href="data:application/'.$row['extension'].';base64,'. $row['file'].'">TELECHARGER '.$row['name'].'</a></th>';
            $extensions_data = 'data:application/'.$row['extension'];
        }

        echo '<script>';
        echo 'function myRedirectFunction() {';
        echo 'window.location.replace("'.$extensions_data.';base64,'. $row['file'].'");';
        echo '}';
        echo '</script>';
        echo'<body onload="myRedirectFunction()">';
        echo '</body>';
    }

}
