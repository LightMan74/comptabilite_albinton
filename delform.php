<div id="form_container">
    <input class="button_close" type="button" onclick="closeForm('del-popup')" value="   [X]   "
        style="float: right;" />
    <form id="form_7328" class="appnitro" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <li id="li_7">
            <label class="description" for="element_7">CONFIRMER LA SUPPRESSION EN TAPANT : <?php echo $id; ?></label>
            <div>
                <input class="element text medium" id="element_7" name="confdel" value="" list="confdelitemlist">
                <!-- <datalist id="confdelitemlist">
                    <option value="non">NON</option>
                    <option value="oui">OUI</option>
                </datalist> -->
            </div>
        </li>
        <input name="ID" type="text" maxlength="255" value="<?php echo $id;?>" style="display:none" />
        <input class="btn btn-danger" type="submit"
            name="<?php if (isset($_POST['confitem'])){echo 'delitem';}else{echo 'delitemfile';} ?>"
            value="SUPPRIMER" />
    </form>
</div>