<?php

// FILE UPLOAD
/* ############################################################################################# */

// READ SETTINGS
require 'setup/config.php';

// CHECK IF USER IS ADMIN
session_start();
$sessionname = md5($SALT);

// IF USER IS ADMIN
if (@$_SESSION[$sessionname]!='admin') {
  // IF ANYTHING WEND WRONG SHOW AN ERROR
  header('HTTP/1.0 404 Not Found', true, 404);
}

// SET LANGUAGE
if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and
     file_exists('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php') ) {
   @include_once('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php');
} else
  @include_once('lang/'.$LANG.'.php');

/* ############################################################################################# */  

echo '<body style="background-color:#eee;padding:7px;margin:0;"><tt><small>'; // ini frame output
if(isset($_POST["delete"])) { // delete the given file
  $DELETE_FILE = explode('_', basename($_POST['archivedFile']), 2);
  if (unlink($_POST['archivedFile']) )
    die($i18n["File is deleted:"].' '.$DELETE_FILE[1]);
  else die($i18n["No File to deleted!"] );
} else if ( isset($_POST["submit"]) ) { // Only if the Form is send
  $UPLOAD_FILE = 'data/' . @$_GET['id'].'_'.basename($_FILES["fileToUpload"]["name"]); // Build File Name with Path
  $UPLOAD_OK = 1; // INI Var
  $UPLOAD_FILE_TYPE = strtolower(pathinfo($UPLOAD_FILE, PATHINFO_EXTENSION)); // Get File Type from Extention
  if (basename($_FILES["fileToUpload"]["name"]) == '') { // Check if a file was selected
    die($i18n["No File selected."] . '<br>');
  }
  $CHECK_IF_IMAGE = getimagesize($_FILES["fileToUpload"]["tmp_name"]); // Check if image file is a actual image or not
  if($CHECK_IF_IMAGE !== false) {
    echo $i18n["File is an"] . ' ' . $CHECK_IF_IMAGE["mime"] . '.<br>';
    $UPLOAD_OK = 1;
  } else {
    echo $i18n["File is not an image."]  . '<br>';
    $UPLOAD_OK = 1;
  }
  if (file_exists($UPLOAD_FILE)) { // Check if file already exists
    echo $i18n["Sorry, file already exists."] . '<br>';
    $UPLOAD_OK = 0;
  }
  // Check max ADMIN file size
  $UPLOAD_MAX_FILE_SIZE_SYSTEM = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
  $UPLOAD_MAX_FILE_SIZE_SYSTEM = str_replace('M', '', $UPLOAD_MAX_FILE_SIZE_SYSTEM);
  if (round(($_FILES["fileToUpload"]["size"] / 1025 / 1025 ) ,4) > $UPLOAD_MAX_FILE_SIZE_ADMIN ) { // 'MB'
    echo $i18n["Sorry, file too large (max."] . ' ' . $UPLOAD_MAX_FILE_SIZE_ADMIN . ' MB).<br>';
    $UPLOAD_OK = 0;
  // Check max SYSTEM file size
  }else if (round(($_FILES["fileToUpload"]["size"] / 1025 / 1025), 4) > $UPLOAD_MAX_FILE_SIZE_SYSTEM ) { // 'MB'
    echo $i18n["Sorry, file too large (max."] . ' ' . $UPLOAD_MAX_FILE_SIZE_SYSTEM . ' MB).<br>';
    $UPLOAD_OK = 0;
  }
  if( !in_array($UPLOAD_FILE_TYPE, $UPLOAD_ALLOWED_FILE_TYPES) ) { // Allow certain file formats
    echo $i18n["Sorry, only this files-types:"] . ' <small>' . rtrim(implode(', ', $UPLOAD_ALLOWED_FILE_TYPES), ', ') . '</small><br>';
    $UPLOAD_OK = 0;
  }
  if ($UPLOAD_OK == 0) { // Check if $UPLOAD_OK is set to 0 by an error
    echo $i18n["Sorry, file was not uploaded."] . '<br>';
  } else { // If everything is ok, try to upload file
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $UPLOAD_FILE)) {
      echo $i18n["The file is uploaded:"]  . ' ' . basename( $_FILES["fileToUpload"]["name"]) . '<br>';
    } else {
      echo $i18n["Sorry, there was an error."] . '<br>' . $_FILES['fileToUpload']['error'];
    }
  }
}
