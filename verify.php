<?php

/* CHECK LOGIN */

session_start();
if ( isset($_POST['u']) AND isset($_POST['p']) ) {
    require('setup/config.php');
    $USER = $_POST['u'];
    if ( $DATA[$USER] == $_POST['p'] ){
        $sessionname = md5($SALT);
        $_SESSION[$sessionname] = $USER;
        $_SESSION['error'] = FALSE;
        die(header('location: index.php'));
    }
}
$_SESSION['error'] = TRUE;
die(header('location: login.php'));
