    <?php
    if(isset($_POST['modifitem']) || isset($_POST['additem'])) {
        $id = $_POST["ID"];
        $total_count = count($_FILES['file']['name']);
        if (!empty($_FILES['file']['name'][0])) {
            for ($i = 0 ; $i < $total_count ; $i++) {

                echo "\n".'<script>console.log("$_FILES[\'file\'][\'name\'][$i] : ' . $_FILES['file']['name'][$i] . '"); </script>';
                $name = $_FILES['file']['name'][$i];
                $target_dir = "temp_file/";
                $target_file = $target_dir . basename($_FILES["file"]["name"][$i]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $image_base64 = file_get_contents($_FILES['file']['tmp_name'][$i]);
                $extensions_img_arr = array("jpg","jpeg","png","gif");
                if (in_array($imageFileType, $extensions_img_arr)) {
                    $imageBlob = $image_base64;
                    ${'imagick'.$x} = new Imagick();
                    ${'imagick'.$x}->readImageBlob($imageBlob);
                    ${'imagick'.$x}->setImageFormat('jpg');
                    $imageFileType = "jpg";
                    ${'imagick'.$x}->setImageCompressionQuality(75);
                    ${'imagick'.$x}->adaptiveResizeImage(1920, 1080, true);
                    $imageBlob = ${'imagick'.$x}->getImageBlob();
                    $image_base64 = base64_encode($imageBlob);
                } else {
                    $image_base64 = base64_encode($image_base64);
                }
                $query .= "('".$id."','".$name."','".$imageFileType."','".$image_base64."'),";
            }
            $query = "INSERT INTO compta_files(idclient,name,extension,file) values " . $query;
            $query = rtrim($query, ",");
            echo "\n".'<script>console.log("SQL : ' . $query . '"); </script>';
            mysqli_query(dbconnect, $query);
            $sqlogs = "INSERT INTO `logs_compta` (`user`, `action`) VALUES ('" . $_SESSION["username"] . "','Files ".$name."')";
            echo "\n".'<script>console.log("SQL : ' . $sqlogs . '"); </script>';
            mysqli_query(dbconnect, $sqlogs);
        }
    }
?>
    <br>