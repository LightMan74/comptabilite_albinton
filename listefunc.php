<script>
const isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
    },
    any: function() {
        if (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows()) {
            return '_blank';
        } else {
            return '_compta';
        }
    }
};
</script>

<?php
    session_start();
ini_set('precision', 2);
function PopUpMsg($message)
{
    echo "<script>alert('$message');</script>";
}

if (isset($_POST['addfilter']) || $filtrekeep) {
    $filtrekeep = false;
    $_SESSION["WHERE"] = htmlspecialchars($_POST["WHERE"]);

}

if (htmlspecialchars($_GET["WHERE"]) != "") {
    $_SESSION["WHERE"] = htmlspecialchars($_GET["WHERE"]);
} elseif (htmlspecialchars($_GET["where"]) != "") {
    $_SESSION["WHERE"] = htmlspecialchars($_GET["where"]);
}
if (isset($_GET['removefilter']) || empty($_SESSION["comptacheckpoint"])) {
    $_SESSION["comptacheckpoint"] = '1';
    echo "<script>var filteratstart2 = '';</script>";
    $_SESSION["WHERE"] = '1';
}

function loadpieces()
{

    $countfiltre = false;

    if ($_SESSION["WHERE"] != "") {
        if ($countfiltre) {
            $wherecondition = $wherecondition . " AND" ;
        }
        $wherecondition = $_SESSION["WHERE"];
        $countfiltre = true;
    }


    if ($_SESSION["WHERE"] == "") {
        $wherecondition = "1";
    }

    $_SESSION["FILTRE-ID"] = "";
    $wherecondition = str_replace("==", "LIKE", $wherecondition);
    $wherecondition = str_replace("**", "_", $wherecondition);
    $wherecondition = str_replace("*", "%", $wherecondition);
    $wherecondition = str_replace("&gt;=", ">=", $wherecondition);
    $wherecondition = str_replace("&lt;=", "<=", $wherecondition);
    $wherecondition = str_replace("\"", "'", $wherecondition);
    $wherecondition = str_replace("&lt;&gt;", "<>", $wherecondition);
    $wherecondition = str_replace("!=", "is null", $wherecondition);
    $wherecondition =
    $wherecondition . " " . "ORDER BY create_timestamp DESC"
     . ", cast(concat(SUBSTR(`DATE_FACTURE`, 7, 4), SUBSTR(`DATE_FACTURE`, 4, 2), SUBSTR(`DATE_FACTURE`, 1, 2)) as unsigned) DESC";
    $sql = "SELECT * FROM `comptabilite` WHERE `id` <> '1' AND  " . $wherecondition;
    $sql2 = "SELECT 
    1500.00-
    IFNULL((SELECT sum(`TTC`) FROM `comptabilite` WHERE `CREDIT` is not null and `DATE_PAYEMENT` is not null),0)-
    IFNULL((SELECT sum(`TTC`) FROM `comptabilite` WHERE `DEBIT` is not null and `DATE_PAYEMENT` is not null),0) as compte_reel,
    IFNULL((SELECT sum(`TTC`) FROM `comptabilite` WHERE `CREDIT` is not null and `DATE_PAYEMENT` is null),0) as credit, 
    IFNULL((SELECT sum(`TTC`) FROM `comptabilite` WHERE `DEBIT` is not null and `DATE_PAYEMENT` is null),0) as debit;";
    // SELECT * FROM `comptabilite` WHERE `DEBIT` is not null and TTC <> (`CB`+`VIR`+`ESP`) and `ISERROR` <> 1;
    // $sql2 = "SELECT ''";
    $result2 = mysqli_query(dbconnect, $sql2);
    // echo $sql . " " . "<br>". 'PAGE: <span id="T-T_HT-PAGE"></span> / ALL: <span id="T-T_HT-ALL"></span>';
    echo '<div style="font-size:75%">'.$sql . "</div>" . '<br>';
    // echo 'GENERAL -> TOTAL HT: <span id="T-T_HT-ALL"></span> / TOTAL TTC: <span id="T-T_TTC-ALL"></span> // MO -> TOTAL HT: <span id="T-MO_HT-ALL"></span> / TOTAL TTC: <span id="T-MO_TTC-ALL"></span> // PIECE -> TOTAL HT: <span id="T-P_HT-ALL"></span> / TOTAL TTC: <span id="T-P_TTC-ALL"></span>';
    // echo 'GENERAL --> TTC: <span id="TTC-ALL"></span> // CLARA: <span id="CLARA-ALL"></span> (<span id="P-CLARA-ALL"></span>%) // COMMUN: <span id="COMMUN-ALL"></span> // WILLIAM: <span id="WILLIAM-ALL"></span> (<span id="P-WILLIAM-ALL"></span>%)';

    if (mysqli_num_rows($result2) > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            echo '<br>COMPTE COURANT --> <span id="COMPTE_COURANT">'.$row["compte_reel"].'</span> €';
            echo ' --- A PAYER --> <span id="COMPTE_COURANT_APAYER">'.$row["debit"].'</span> €';
            echo ' --- A RENTRER --> <span id="COMPTE_COURANT_APAYER">'.$row["credit"].'</span> €';
            echo ' --- COMPTE COURANT APRES RBS ET PAYEMENT --> <span id="COMPTE_COURANT_REEL">'.sprintf('%0.2f', (($row["compte_reel"] - $row["debit"]) + $row["credit"])).'</span> €';
        }
    }
    $sqlogs = 'INSERT INTO `logs_compta` (`user`, `action`) VALUES ("' . $_SESSION["username"] . '","' . $sql . '")';

    // $ww = mysqli_query(dbconnect, $sqlogs);
    if (1 != 1) {
        echo "</br>";
        echo "MODE MOBILE";
        echo "</br>";
    } else {
        $result = mysqli_query(dbconnect, $sql);

        ?>

<table id="searchtable" class="blueTable tableFixHead">
    <thead>
        <tr>
            <th style="width:6%">id</th>
            <th style="width:6%">timestamp</th>
            <th style="width:6%">DATE_FACTURE</th>
            <th style="width:6%;font-size:85%">IDMOIS</th>
            <!-- <th style="width:6%;font-size:75%">DEBIT</th> -->
            <!-- <th style="width:6%;font-size:75%">CREDIT</th> -->
            <th style="width:6%">CorD</th>
            <th style="width:6%">TYPE</th>
            <th style="width:6%">TTC</th>
            <th style="width:15%;font-size:85%">CLIENTS_FOURNISEUR</th>
            <th style="width:15%">REMARQUES_DIVERSES</th>
            <th style="width:3%">DATE_PAYEMENT</th>
            <th style="width:6%">MONTANT</th>
            <th style="width:12%">OPTIONS</th>
            <th style="width:12%">ERREUR</th>
        </tr>
    </thead>
    <tbody>
        <script>
        function sendform(sendform) {
            document.getElementById(sendform).submit();
        }
        </script>
        <?php

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // $tvareel = number_format(str_replace(",", ".", $row["TVA_TAUX"]) * 100, 2) ;
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["timestamp"] . "</td>";

            echo "<td>" . $row["DATE_FACTURE"] . "</td>";
            echo "<td style='font-size:85%'>" . $row["IDMOIS"] . "</td>";
            // echo "<td style='font-size:75%'>" . $row["DEBIT"] . "</td>";
            // echo "<td style='font-size:75%'>" . $row["CREDIT"] . "</td>";
            echo "<td style='font-size:75%'>";
            if ($row["CREDIT"] <> "") {
                echo "CREDIT";
            }
            if ($row["DEBIT"] <> "") {
                echo "DEBIT";
            }
            echo "</td>";
            echo "<td>" . $row["TYPE"] . "</td>";
            echo "<td>" . number_format($row["TTC"], 2, ".", "") . "</td>";
            echo "<td style='font-size:85%'>" . $row["CLIENTS_FOURNISEUR"] . "</td>";
            echo "<td>" . $row["REMARQUE_DIVERSE"] . "</td>";
            echo "<td>" . $row["DATE_PAYEMENT"] . "</td>";
            echo "<td>" ;
            if($row["VIR"] != "") {
                echo number_format($row["VIR"], 2, ".", "");
            }
            echo "</td>";
            ?>
        <script>
        $(function() {
            $("#<?php echo $row["N_FACTURE"].$row["id"] ?>").attr('target', isMobile.any());
        });
        </script>

        <?php

                       echo "<td>"; ?>
        <div style="height: 75%;">
            <form id="searchclienttab" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
                <input name="ID" type="text" maxlength="255" value="<?php echo $row["id"]; ?>" style="display:none" />
                <input class="btn menu btn-warning" type="submit" name="openmodifitem" value="MODIFER" style="height: 90%;" />
            </form>
        </div>
        <div style="height: 25%;">
            <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
                <input name="ID" type="text" maxlength="255" value="<?php echo $row["id"]; ?>" style="display:none" />
                <input class="btn menu btn-danger" type="submit" name="confitem" value="SUPPRIMER" style="height: 90%;" />
            </form>
        </div>
        <?php
               echo "</td>";
            echo "<td>" . $row["ISERROR"] . "</td>";
            ?>

        </tr>
        <?php

        } ?>

    </tbody>
    <tfoot id="footer">
        <tr>
            <th>id</th>
            <th>timestamp</th>
            <th>DATE_FACTURE</th>
            <th>IDMOIS</th>
            <!-- <th>DEBIT</th>
        <th>CREDIT</th> -->
            <th>CorD</th>
            <th>TYPE</th>
            <th>TTC</th>
            <th>CLIENTS_FOURNISEUR</th>
            <th>REMARQUES_DIVERSES</th>
            <th>DATE_PAYEMENT</th>
            <th>MONTANT</th>
            <th>OPTIONS</th>
            <th>ERREUR</th>
        </tr>
    </tfoot>

</table>
<?php
    } else {
        echo "</br>";
        echo "0 results";
    }
    }
}

?>

<script>
document.addEventListener('click', function(event) {
    var name = event.key;
    var code = event.code;
    var clickctrl = event.ctrlKey
    // Alert the key name and key code on keydown
    if (clickctrl) {
        // console.log(`Key DOWN ${name} \r\n Key code value: ${code}`);
        var anchors = document.querySelectorAll("#searchclienttab");
        for (var i = 0; i < anchors.length; i++) {
            anchors[i].setAttribute('target', '_newone');
        }
    }
}, false);

const handleVisibilityChange = function() {
    if (document.visibilityState != 'visible') {
        var anchors = document.querySelectorAll("#searchclienttab");
        for (var i = 0; i < anchors.length; i++) {
            anchors[i].setAttribute('target', '_self');
        }
    }
}
</script>