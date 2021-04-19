<?php

/* LOGOUT */

include('setup/config.php');
session_start();
$_SESSION['error'] = '';
$sessionname = md5($SALT);
$_SESSION[$sessionname] = 'LOGOUT';
session_unset();
session_destroy();
die(header('Location: index.php'));
