<body>
    <link rel="stylesheet" type="text/css" href="CSS_JS/popup.css" media="all">
    <link rel="stylesheet" type="text/css" href="CSS_JS/stockstyle.css" media="all">

    <link rel="stylesheet" type="text/css" href="CSS_JS/styleappel.css" media="all">

    <script type="text/javascript" src="CSS_JS/view.js"></script>

    <link rel="stylesheet" href="CSS_JS/menu.css" />
    <script type="text/javascript" src="CSS_JS/menu.js"></script>

    <?php
        if (!isset($_POST['modifitem']) || !isset($_POST['additem'])) {
            echo '<script src="CSS_JS/clipboard.min.js"></script>';
        }
    ?>
    <?php
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 1);?>

    <script type="text/javascript" src="CSS_JS/popup.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" type="image/png" sizes="32x32" href="logo.ico">
    <title>COMPTA</title>
    <?php
            session_start();

    include "config.php";

    if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true)) {
        ?>
    <script type="text/javascript">
    window.location.href = "login.php";
    </script>
    <?php
    }

    function getUpperPost($keepVar = true)
    {
        $return_array = array();
        /* Edited on 4/1/2015 */
        foreach ($_POST as $postKey => $postVar) {
            $return_array[$postKey] = strtoupper(ltrim(rtrim($postVar, " "), " "));
        }
        if ($keepVar) {
            $_POST = $return_array;
        } else {
            return $return_array;
        }
    }
    if (!isset($_POST['modifitem']) && !isset($_POST['additem'])) {
        getUpperPost();
    }
    function dtc($data)
    {
        $output = $data;
        if (is_array($output)) {
            $output = implode(',', $output);
        }
    }
    include "listefunc.php";
    ?>

    <table id="123" class="blueTable tablenoFixHead" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="8">
                    <div class="nav-fullscreen">
                        <ul class="nav-fullscreen__items">
                            <!-- <input class="btn btn-outline-danger btncat" value="VOIR EXPORT COMPTABLE" onclick="window.open('', '_blank');" />
                            <br>
                            <input class="btn btn-outline-danger btncat" value="GENERER EXPORT COMPTABLE" onclick="window.open('', '_blank');" /> -->
                            <br>
                            <br>
                            <?php echo 'Utillisateur : ' . htmlspecialchars($_SESSION["username"]); ?>
                            <a href="../logout.php"><input class="btn btn-outline-danger btncat" value="DECONNEXION"></a>
                        </ul>

                    </div>
                    <div class="hamburger">
                        <center>
                            <input style="width:75%; height: 100%;" class="btn" type="button" name="" value="MENU" />
                        </center>
                    </div>
                </th>
            </tr>
            <tr>
                <th colspan="11">

                    <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post" style="margin: 0;">
                        <input class="btn btn-outline-danger btnadd intable" id="addbutton" type="submit" name="openadditem" value="AJOUTER" />
                    </form>

                </th>
            </tr>
            <tr>
                <form id="formfiltre" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="get" style="margin: 0;">
                    <th>
                        <input class="btn btn-outline-danger btnadd intable" id="searchfilter" type="submit" name="addfilter" value="FILTRER" />

                    </th>

                    <th id="elem1">
                        <input style="width:75%; height: 3vh; text-align: center;" type="text" id="" placeholder="WHERE" title="WHERE" name="WHERE" value="<?php echo $_SESSION["WHERE"]; ?>">
                    </th>

                    <th>

                        <input class="btn btn-outline-danger btnadd intable" id="searchfilter" type="submit" name="removefilter" value="ANNULER" />
                    </th>

                </form>
            </tr>
    </table>


    <button onclick="topFunction()" id="myBtn" title="Go to top">↑ HAUT ↑</button>


    <?php
if (isset($_POST['viewfilecompta'])) {
    include "viewfilecompta.php";
}?>

    <div id="externalToolbar"></div>
    <table id="searchtable" class="blueTable tableFixHead">
        <?php

// echo "-->" . $_SESSION["comptacheckpoint"];
        if (!isset($_POST['openadditem']) && !isset($_POST['viewfilecompta']) && !isset($_POST['openmodifitem']) && !isset($_POST['modifitem']) && !isset($_POST['additem'])) {
            loadpieces();

            if (isset($_GET['removefilter']) || $_SESSION["comptacheckpoint"] == '1') {
                $_SESSION["comptacheckpoint"] = '2';
                // echo "-->" . $_SESSION["comptacheckpoint"];
                // echo "<script>var filteratstart = [0, 1, 6, 15, 17, 18, 19, 20, 21, 22, 23, 25];</script>";
                // echo "<script>var filteratstart = [0, 1, 3];</script>";

                echo "<script>var filteratstart = '';</script>";
            } else {
                echo "<script>var filteratstart = '';</script>";
            }
            // echo '<script src="CSS_JS/clipboard.min.js"></script>';
            echo '<script src="CSS_JS/tablefilter/tablefilter.js"></script>';
            echo '<script type="text/javascript" src="parametretableau.js"></script>';
        }
    ?>
    </table>
    <div class="modif-popup_close" id="modif-popup">

        <?php
            // echo "\n".'<script>console.log("avant modif.php"); </script>';
        include "modif.php";
    ?>
    </div>

    <div class="del-popup_close" id="del-popup">
        <?php
        include "del.php";
    ?>
    </div>



</body>
<?php
mysqli_close(dbconnect);
    ?>