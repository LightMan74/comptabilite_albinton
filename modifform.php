<?php
include "settings.php"
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- <link href="CSS_JS/select2.css" rel="stylesheet" /> -->
<!-- <script src="CSS_JS/select2.js"></script> -->
<link href="CSS_JS/dragdrop.css" rel="stylesheet" />
<script>
    function energiechange(cb, el) {
        var combo = document.getElementById(cb);
        var element = document.getElementById(el);
        if (combo.value == "AUTRE") {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }
        element.value = combo.value;
    }
</script>
<script type="text/javascript">
    function setRequired(element) {
        if (element == "DEBIT") {
            document.getElementById("DEBIT").required = true;
            document.getElementById("CREDIT").required = false;
            document.getElementById("DEBIT").checked = true;
            document.getElementById("CREDIT").checked = false;
        }
        if (element == "CREDIT") {
            document.getElementById("DEBIT").required = false;
            document.getElementById("CREDIT").required = true;
            document.getElementById("DEBIT").checked = false;
            document.getElementById("CREDIT").checked = true;
        }
    }
</script>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<body id="main_body">
    <div id="form_container">
        <input class="button_close" type="button" onclick="closeForm('form_container')" value="   [X]   " style="float: right;" />
        <form id="formAorM" class="appnitro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype='multipart/form-data' id="file-upload-form">

            <div class="form_description">
                <h2><?php echo $modifierORadd; ?> DONNEE COMPTA</h2>
            </div>
            <table id="aze" class="blueTable tablenoFixHead">
                <tr>
                    <th>
                        DATE FACTURE<br><input type="date" style="width:100%;height: 3vh;text-align: center;" type="text" class="form-control date" name="DATE_FACTURE" value="<?php $splitdate = explode("/", $row['DATE_FACTURE']);
echo $splitdate[2]."-".$splitdate[1]."-".$splitdate[0];?>" placeholder="Date de facture" required="required">
                    </th>
                </tr>
            </table>
            <table id="aze" class="blueTable tablenoFixHead">
                <tr style="width:50%">
                    <th><input type="radio" id="DEBIT" name="DEBIT" placeholder=" DEBIT" value="x" <?php if($row['DEBIT'] != "") {
                        echo "checked";
                    } if ($row['DEBIT'] == "" && $row['CREDIT'] == "") {
                        echo ' required="required" ';
                    }?> onclick="setRequired('DEBIT');">
                        DEBIT
                    </th>
                    <th style="width:50%"><input type="radio" id="CREDIT" name="CREDIT" placeholder="CREDIT" value="x" <?php if($row['CREDIT'] != "") {
                        echo "checked";
                    }?> onclick="setRequired('CREDIT');">
                        CREDIT
                    </th>
                </tr>
            </table>
            <table id="aze" class="blueTable tablenoFixHead">
                <tr>
                    <th>TYPE<br>
                        <!-- <input style="width:100%;height: 3vh;text-align: center;" class="form-control" type="text" name="TYPE" placeholder="TYPE" value="<?php echo $row['TYPE'];?>" autocomplete="off"> -->
                        <select name="type0" id="cbtype" onchange="energiechange('cbtype','autretype')">
                            <option value="<?php echo $row["TYPE"] ?>" selected><?php echo $row["TYPE"] ?></option>
                            <?php
                        foreach ($setting_type as $it) {
                            echo '<option value="'.$it.'">'.$it.'</option>';
                        } ?>
                            <option value=""></option>
                            <option value="AUTRE">AUTRE</option>
                        </select>
                        <input name="type" id="autretype" value="<?php echo $row["TYPE"] ?>" style="display:none;" />
                    </th>
                    </th>
                </tr>
            </table>

            <br>
            <script>
                function validateNumber(event) {
                    var key = window.event ? event.keyCode : event.which;
                    if (event.keyCode === 8 || event.keyCode === 46 || event.key === '-') {
                        return true;
                    } else if (key < 48 || key > 57) {
                        return false;
                    } else {
                        return true;
                    }
                };

                function energiechange(cb, el) {
                    var combo = document.getElementById(cb);
                    var element = document.getElementById(el);
                    if (combo.value == "AUTRE") {
                        element.style.display = "block";
                    } else {
                        element.style.display = "none";
                    }
                    element.value = combo.value;
                }
            </script>


            <table id="tableautva" class="blueTable tablenoFixHead">
                <tr>
                    <th>TTC<br><input style="width:100%;height: 3vh;text-align: center;" class="form-control" type="text" id="TTC" name="TTC" placeholder="TTC" value="<?php echo $row['TTC'];?>" onkeypress="return validateNumber(event)">
                    </th>
                </tr>
                <tr>
                    <th>
                        <br>
                        <div class="box">
                            <label>
                                <strong>Choose files</strong>
                                <span>or drag them here.</span>
                                <input class="box__file" type="file" name="file[]" multiple />
                            </label>
                            <div class="file-list"></div>
                        </div>


                        <input style="display:none" id="element_6" name="IDCOMPTA" class="element text medium" type="text" maxlength="255" value="<?php echo $id; ?>" />
                        <!-- </form> -->
                        <?php
                    require 'readfile.php';
readfileform($row["id"]);
?>
                    </th>
                </tr>
            </table>

            <br>
            <table id="aze" class="blueTable tablenoFixHead">
                <tr>
                    <th>CLIENT/FORUNISSEUR<br><input style="width:100%;height: 3vh;text-align: center;" class="form-control" type="text" name="CLIENT" placeholder="CLIENT/FOURNISSEUR" value="<?php echo $row['CLIENTS_FOURNISEUR'];?>" autocomplete="off">
                    </th>
                </tr>
                <tr>
                    <th>REMARQUE<br><input style="width:100%;height: 3vh;text-align: center;" class="form-control" type="text" name="REMARQUE" placeholder="REMARQUES" value="<?php echo $row['REMARQUE_DIVERSE'];?>" autocomplete="off">
                    </th>
                </tr>
                <tr>
                </tr>
            </table>
            <br>
            <table id="aze" class="blueTable tablenoFixHead">
                <tr>
                    <th>
                        DATE PAYEMENT<br><input type="date" style="width:100%;height: 3vh;text-align: center;" type="text" class="form-control date" name="DATE_PAYEMENT" value="<?php $splitdate = explode("/", $row['DATE_FACTURE']);
echo $splitdate[2]."-".$splitdate[1]."-".$splitdate[0];?>" placeholder="Date de payement">
                    </th>
                </tr>
                <br>
                <table id="aze" class="blueTable tablenoFixHead">
                    <tr>
                        <th>PAYEMENT<br><input style="width:100%;height: 3vh;text-align: center;" class="form-control" type="text" onkeypress="return validateNumber(event)" name="VIR" placeholder="VIR" value="<?php echo $row['VIR'];?>" autocomplete="off">
                        </th>
                </table>
                <br>

                <table id=" aze" class="blueTable tablenoFixHead">
                    <tr>
                        <th>
                            <br>COLORER SI ERREUR ?<br>
                            <select id="selectiserror" style="width:100%;height: 3vh;text-align: center;" name="ISERROR">
                                <option value="0" <?php if($row['ISERROR'] == "0") {
                                    echo "selected";
                                } ?>>OUI</option>
                                <option value="1" <?php if($row['ISERROR'] == "1") {
                                    echo "selected";
                                } ?>>NON</option>
                                <option value="2" <?php if($row['ISERROR'] == "2") {
                                    echo "selected";
                                } ?>>FORCED</option>
                            </select>
                        </th>
                    </tr>
                </table>


                <br>
                <input style="display:none" id="element_6" name="CREATE_TIMESTAMP" class="element text medium" type="text" maxlength="255" value="<?php echo $row["CREATE_TIMESTAMP"]; ?>" />
                <input style="display:none" id="element_6" name="ID" class="element text medium" type="text" maxlength="255" value="<?php echo $row["id"]; ?>" />
                <input id="saveForm" class="btn btn-outline-primary" type="submit" name="<?php echo $namemodifierORadd; ?>" value="<?php echo $modifierORadd; ?>" style="width:100%" ; />
                <br>


        </form>
    </div>
</body>



<script src="CSS_JS/dragdrop.js"></script>