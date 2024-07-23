<?php
session_start();

require_once('./vendor-TOTP/autoload.php');
// in practice you would require the composer loader if it was not already part of your framework or project
spl_autoload_register(function ($className) {
    include_once str_replace(array('RobThree\\Auth', '\\'), array(__DIR__.'/../lib', '/'), $className) . '.php';
});

// substitute your company or app name here
$tfa = new RobThree\Auth\TwoFactorAuth('RobThree TwoFactorAuth');

if(!isset($_COOKIE['ALB_CONNECT_USERNAME'])) {
    $_COOKIE['ALB_CONNECT_USERNAME'] = "";
}
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    header('location: liste.php');
    exit;
} elseif($_COOKIE['ALB_CONNECT_USERNAME'] != "") {
    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $_COOKIE['ALB_CONNECT_USERNAME'];
    header('location: liste.php');
    exit;
}

// Include config file
// require_once "configlogin.php";

include "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
$TOTP_err = "";
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, TOTP FROM users_compta WHERE username = ?";

        if($stmt = mysqli_prepare(dbconnect, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $TOTP);
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            // echo $TOTP;
                            $secret = $TOTP;
                            //echo $tfa->getCode($secret);
                            if($tfa->verifyCode($secret, $_POST["totp"], 1) === true) {
                                // session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                setcookie('ALB_CONNECT_USERNAME', $username, time() + 1 * 12 * 3600, '/', '.albinton.fr', true, false);
                                header('location: liste.php');
                                exit;
                            } else {
                                // Display an error message if password is not valid
                                $TOTP_err = "The TOTP you entered was not valid.";

                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";

                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";

                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";

            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>ALB'INTON</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 500px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper" style="margin: 0 auto; text-align: center;">
        <h2><a style="color:inherit; text-decoration: inherit " href="https://albinton.fr" class="fullwidth">ALB'INTON</a></br>COMPTABILITE</br>
        </h2>
        <h2>Connexion</h2>
        <p>Veuillez remplir vos identifiants pour vous connecter.</p>
        <!-- <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> -->
        <form action="https://compta.albinton.fr/login.php" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Nom d'utillisateur</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($TOTP_err)) ? 'has-error' : ''; ?>">
                <label>2FA - TOTP</label>
                <input type="text" name="totp" class="form-control" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
                <span class="help-block"><?php echo $TOTP_err; ?></span>
            </div>
            <div class="form-group">
                <!-- <input name="id" type="text" maxlength="255" value="<?php echo $_GET["id"]; ?>" style="display:none" />
                <input type="hidden" name="token" id="token" value="<?php echo $token;?>" /> -->
                <!-- <input type="hidden" name="page" id="page" value="<?php echo $pagetoredirect;?>" /> -->
                <input type="submit" class="btn btn-primary" value="Connexion">
            </div>
        </form>
    </div>
    <?php
$ipaddress = $_SERVER['REMOTE_ADDR'];
echo "Your IP Address is " . $ipaddress;
?>

</body>

</html>