<?php

/* EDIT */

// DISPLAY ALL ERRORS
ini_set('display_errors', 1);
error_reporting(E_ALL);

// REMOVE XSS ATTACKS WHILE STILL ALLOWING HTML (FIX 04.06.2019) - https://tinyurl.com/yye4h2dm
require_once 'lib/HTMLPurifier.standalone.php';
$HTMLPurifier_config = HTMLPurifier_Config::createDefault();
$HTMLPurifier = new HTMLPurifier($HTMLPurifier_config);
if (isset($_POST)) {
  foreach($_POST as $key => $dirty_input) {
    $_POST[$key] = $HTMLPurifier->purify($dirty_input);
  }
}
if (isset($_GET)) {
  foreach($_GET as $key => $dirty_input) {
    $_GET[$key] = $HTMLPurifier->purify($dirty_input);
  }
}

// START & CONFIG
ob_start();
require 'setup/config.php';

// SET LANGUAGE
if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND
     file_exists('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php')
   ) {
   @include_once('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php');
} else {
  @include_once('lang/'.$LANG.'.php');
}

// INI VARs
$file = 'data/'.$TYPE.'.json'; // DATA FILE NAME

// LOGIN CHECK
session_start();
$sessionname = md5($SALT);
if(!@$_SESSION[$sessionname] && (!isset($USEL) || $USEL == TRUE) )
    die(header('location: login.php'));

// READ DATA
$dat = json_decode( str_replace("\u00a0", '&nbsp;', file_get_contents( $file ) ), true );

// GENERATE LIST FOR DATA-LIST IN NAME INPUT FIELD
$list = '';
foreach ( $dat as $key => $values )
  $list .= '<option value="'.$values['name'].'">';
/*
// DEFAULT DATE IF NOT SET
if ( $dat[@$_GET['id']]['date'] == '' )
  $dat[@$_GET['id']]['date'] = date( $DATE );
*/
// SAVE NEW ENTRY IF FORM POSTED
if ( $_SERVER['REQUEST_METHOD'] == 'POST' AND
     @$_POST['name'] != '' AND
     @$_POST['text'] != '' AND
     @$_POST['status'] != '' ) {
	$dat = json_decode( str_replace("\u00a0", '&nbsp;', file_get_contents( $file ) ), true );
	foreach ( $dat as $key => $entry ) {
		if ( $key == $_GET['id'] ) {
			$dat[$key]['date']   = $_POST['date'];
			$dat[$key]['name']   = $_POST['name'];
			$dat[$key]['text']   = $_POST['text'];
			$dat[$key]['status'] = $_POST['status'];
			$dat[$key]['owner']  = $_POST['owner'];
			$dat[$key]['prio']   = $_POST['prio'];

		}
	}
	$newJsonString = json_encode( $dat );
	file_put_contents( $file, $newJsonString );
	header( 'location: index.php?msg='.$i18n['Entry successfully edited!'] );
} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
	$_GET['msg'] = $i18n['All fields are required!'];

// HEADER
ob_end_flush();
include 'tpl/header.php';

// FORM FOR EDIT ENTRY
$owner_names = '';
foreach ($DATA as $key => $value) {
  $owner_names .= '<option value="'.$key.'">'.ucwords($key).'</option>';
}

//PARSE SETUP
// PARSE INITIALISATION SECTION
// echo 'got here! In db query <br />';
require 'parse-php-sdk/autoload.php';

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
use Parse\ParseClient;

$app_id = '2fdFVmfY2WTa3RISTWMLQ8N3FuTRgmc4cKYXvpPa';
$rest_key = 'MCCMMpGiAwpvk4Kc8gN1n5YgdS0S2zJgvAI6H4Yk';
$master_key = 'kHb3KWDYaodWMBhh2sEMs7ZuVILvBCGJMC94DMZL';
//echo 'got here! 3<br />';

ParseClient::initialize( $app_id, $rest_key, $master_key);
ParseClient::setServerURL('https://parseapi.back4app.com','/');
//echo 'got here! 1<br />';
$health = ParseClient::getServerHealth();
if($health['status'] === 200) {
    //echo 'All cool!';
}
else {
  //echo 'ALERT!<br /> Status = ' . $health['status'] . '<br /> Response = ' . $health['response'] . '<br />';
}




// TODOS QUERY
$query = new ParseQuery('ToDo');

// Finds objects whose title is equal to "Documentation"
//echo 'id to get is ' . $_GET['id'];
$query->equalTo("note", $_GET['id']);
$results = $query->find();

// IF "OWNER EDIT" IS ALSO ALLOWED FOR NON-ADMINS or USER = ADMIN or NO LOGIN IS USED
if (($AEOW==FALSE OR @$_SESSION[$sessionname]=='admin' OR $USEL == false) and $UTOW == true) {
  // THEN SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 4; // 4 = 3 colls
} else {
  // DON'T SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 6; // 6 = 2 colls
}




// TABLE2 HEADER
  $table2 = '
  <table class="striped responsive-table hoverable tablesorter {sortlist:['.$SORT.']}" id="table">
    <thead>
      <tr style="line-height:1;">
      <th style="vertical-align:top;text-align:center">Item</th>
      <th style="vertical-align:top;text-align:center">Variants</th>
      <th style="vertical-align:top;text-align:center">Status</th>
      </tr>
    </thead>
  <tbody>
  ';
?>
<div class="badge grey lighten-3 hoverable" style="padding:1em">
<?php
  foreach($results as $result) {

        // SET TABLE LINE
        $css = '';
        $table2 .= '<tr style="line-height:1;">.
        <td style="vertical-align:top;text-align:center" title="">' . $result->get("text") . '</td>.
        <td style="vertical-align:top;text-align:center;white-space:nowrap;">' . $result->get("variants") . '</td>.
        <td title="Edit" style="vertical-align:top;text-align:center">'.
              /* SHOW DEL LINK ONLY FOR ADMIN (IF IT IS SO CONFIGURED) */
              //((@$_SESSION[$sessionname]=='admin' OR $ADEL == false OR $USEL == false)?
              //'<a class="tooltipped" data-position="top"'.
              //' data-tooltip="'.$i18n['Click alert link for delete!'].'"'.
              //' href="#" onclick=\''.$toast.'\'><icon'. 
              //' del></icon></a>&emsp;':''). 
              '<a class="tooltipped" data-position="top" data-tooltip="'.$i18n['EDIT'].'"'.
              ' href="edit.php?delId='. $result->getObjectId() .'"><icon edit></icon></a>&emsp;'.
              '</td></tr>';
  }
  
  ?>

</div>


<div class="badge grey lighten-3 hoverable" style="padding:1em">
  <h3 class="grey-text text-darken-2">ORDER</h3>
  <?php echo $table2.'</tbody></table><br>'; ?>
  </div>
  
    

</div><br>

<?php

// FOOTER
include 'tpl/footer.php';
