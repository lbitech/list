<?php

/* BACKUP (DOWNLOAD DATA FILE) */

// READ SETTINGS
require 'setup/config.php';

// CHECK IF USER IS ADMIN
session_start();
$sessionname = md5($SALT);

// IF USER IS ADMIN
if (@$_SESSION[$sessionname]=='admin') {

  // BUILD DATA PATH
  $file = 'data/'.$TYPE.'.json';

  // IF DATA EXIST AND IS READ ABLE
  if (file_exists($file) && is_readable($file)) {

    // WRITE HEADER
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="'.$TYPE.'.json"');

    // DOWNLOAD FILE
    readfile($file);

    // STOP SCRIPT
    exit();
  }

}

// IF ANYTHING WEND WRONG SHOW AN ERROR
header('HTTP/1.0 404 Not Found', true, 404);
