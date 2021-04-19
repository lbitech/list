<?php

// EXPORT (DOWNLOAD DATA FILE)
/* ############################################################################################# */

// READ SETTINGS
require 'setup/config.php';

// CHECK IF USER IS ADMIN
session_start();
$sessionname = md5($SALT);

// IF USER IS ADMIN
if (@$_SESSION[$sessionname]=='admin') {

  // EXPORT for path and filename
  export2excel('data/', $TYPE);

  // STOP SCRIPT
  exit();
}

// IF ANYTHING WEND WRONG SHOW AN ERROR
header('HTTP/1.0 404 Not Found', true, 404);

/* ############################################################################################# */

function export2excel($path, $fileName, $headLine='', $DEBUG=FALSE) {
  // Make sure the content is shown in the Browser
  if ( $DEBUG == TRUE ) print( '<xmp>' );
  // Get JSON string from /data/ File
  $JSONdata = file_get_contents( $path.$fileName.'.json' );
  // Convert JSON string to Array
  $JSONarray = json_decode( $JSONdata, true );
  // No Cache Header
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  // Only force the download if not in DEBUG mode
  if ($DEBUG == FALSE) {
    header('Content-Type: application/force-download');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/download');
    header('Content-Disposition: attachment;filename=' . $fileName . '.xls');
    header('Content-Transfer-Encoding: binary');
  }
  // Make Headline
  print( $headLine . ($headLine==''?'':' ') . $fileName . PHP_EOL . PHP_EOL );
  // Make column labels
  $columnHeadings = '';
  foreach ( array_keys($JSONarray[0]) as $labelName ) {
    $columnHeadings .= ucfirst($labelName) . ', ';
  }
  print( rtrim($columnHeadings, ', ') . PHP_EOL );
  // Put data records from json by foreach loop
  foreach($JSONarray as $row) { // REPLACE ord(44) comma (,) WITH single low quotation mark (‚) &sbquo;
    print( html_entity_decode(str_replace(',', '‚', $row['id']    )) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['name']  )) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['text']  )) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['status'])) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['date']  )) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['owner'] )) . ', ' );
    print( html_entity_decode(str_replace(',', '‚', $row['prio']  ))        );
    print( PHP_EOL );
  } 
}
