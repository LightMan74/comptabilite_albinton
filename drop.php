<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>

<body>
    <?php echo $uploaded_filenames; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
        <div class="box">
            <label>
                <strong>Choose files</strong>
                <span>or drag them here.</span>
                <input class="box__file" type="file" name="files[]" multiple />
            </label>
            <div class="file-list"></div>
        </div>
        <button>Submit</button>
    </form>
</body>

</html>
<link href="CSS_JS/dragdrop.css" rel="stylesheet" />
<script src="CSS_JS/dragdrop.js"></script>