<?php

/*

  ToDo List, Issue and Bug Tracker
  -----------------------------------------------------------
  A simple Bug and Issue Tracker also for MoSCoW-Method
  This is a developer focused tracking tool.
  It has a modern UI making a very fluid user experience.
  It is designed to work without database, on cheap webspace or
  100% offline e.g. on localhost, so it is very fast.
  Using it should be as simple as copying the script code.
  To run simply edit the config and start the index file.
  -----------------------------------------------------------
  All rights reserved - (c) 2018-2020 adilbo

*/

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

// SEARCH HELPER FUNCTION
function contains(array $array, $string) {
  $count = 0;
  foreach ( $array as $value ) {
    if ( false !== stripos($string, $value) )
      ++$count;
  }
  return $count == count($array);
}

// START & CONFIG
ob_start();
require 'setup/config.php';

// SET LANGUAGE
if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and
     file_exists('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php') ) {
   @include_once('lang/'.substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2).'.php');
} else
  @include_once('lang/'.$LANG.'.php');

// INI VARs
$found        = 0;                     // MARKER HOW MANY HITS FOUND A SEARCH
$entryCounter = 0;                     // MARKER HOW MANY ENTRIES FOUND
$done         = 0;                     // MARKER HOW MANY ENTRYs ARE DONE
$msg          = '';                    // TOAST MESSAGE
$file         = 'data/'.$TYPE.'.json'; // DATA FILE NAME

// LOGIN CHECK
session_start();
$sessionname = md5($SALT);
if(!@$_SESSION[$sessionname] && (!isset($USEL) || $USEL == TRUE) )
    die(header('location: login.php'));

// FOR DEBUG ONLY
if ( isset($_GET['php']) AND $_GET['php'] == 'info' ) die( phpinfo() );

// DELETE ENTRY IF DEL LINK IS CLICKED
if ( @$_GET['delete_id'] != '' AND (@$_SESSION[$sessionname]=='admin' OR $ADEL == false OR $USEL == false)) {
	$dat = json_decode( str_replace("\u00a0", '&nbsp;', file_get_contents( $file ) ), true );
	unset( $dat[@$_GET['delete_id']] );
	$convert_array_to_json = array_values( $dat );
	$write_json = json_encode( $convert_array_to_json );
	file_put_contents( $file, $write_json );
	die(header('location: index.php?msg='.urlencode($i18n['Entry successfully deleted.'])));
}

// DEFAULT DATE IF NOT SET
if ( @$_POST['date'] == '' )
  $_POST['date'] = date( $DATE );

// SAVE NEW ENTRY IF FORM POSTED
if ( $_SERVER['REQUEST_METHOD'] == 'POST' and
     @$_POST['name'] != '' and
     @$_POST['text'] != '' and
     @$_POST['status'] != ''
   ) {
  if (!isset($_POST['owner']) or empty($_POST['owner'])) {
    $owner = $_SESSION[$sessionname];
  }else{
    $owner = $_POST['owner'];
  }
	$formdata = array(
	  'id'     => time(),
	  'name'   => $_POST['name'],
	  'text'   => $_POST['text'],
    'status' => $_POST['status'],
    'date'   => $_POST['date'],
    'owner'  => $owner,
    'prio'   => $_POST['prio'],
  );
	$arr_data = array();
	if(file_exists($file)) {
		$arr_data = json_decode( str_replace("\u00a0", '&nbsp;', file_get_contents( $file ) ), true );
	}
	$arr_data[] = $formdata;
	$jsondata = json_encode($arr_data);
	if(file_put_contents($file, $jsondata))
		$msg = $i18n['Data successfully saved!'];
	else
		$msg = $i18n['ERROR &mdash; Data not saved!'];
	die(header( 'location: index.php?msg='.urlencode($msg)));
}else if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
	$_GET['msg'] = $i18n['All fields are required!'];

// CREATE DATA FILE IF NOT EXISTS
if(!is_file($file))
  file_put_contents($file, '');

// READ DATA
$dat = json_decode( str_replace("\u00a0", '&nbsp;', file_get_contents( $file ) ), true );
# var_dump( $dat );

// HEADER
ob_end_flush();
include 'tpl/header.php';

// FORM FOR NEW ENTRY
if ( !isset($_GET['q']) || empty($_GET['q']) ) {
$owner_names = '';
foreach ($DATA as $key => $value) {
  $owner_names .= '<option value="'.$key.'">'.ucwords($key).'</option>';
}

// IF "OWNER EDIT" IS ALSO ALLOWED FOR NON-ADMINS or USER = ADMIN or NO LOGIN IS USED
if (($AEOW==FALSE OR @$_SESSION[$sessionname]=='admin' OR $USEL == false) and $UTOW == true) {
  // THEN SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 4; // 4 = 3 colls
} else {
  // DON'T SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 6; // 6 = 2 colls
}

// NEW ENTRY INPUT FORM
$input_form = '
<div class="badge grey lighten-3 hoverable noprint" style="padding:1em">
  <h3 class="grey-text text-darken-2">'.$i18n['New Issue'].'</h3>
  <form method="POST" action="'.htmlentities($_SERVER['PHP_SELF']).'">
    <div class="row">
      <div class="input-field col m2 s12">
        <input
          value="'.date($DATE).'"
          id="date"
          name="date"
          type="text"
          class="datepicker"
          maxlength="'.strlen($PICK).'"
          data-length="'.strlen($PICK).'" required="required"
        >
        <label for="date">'.$i18n['Date'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.($MODE=='C'?$i18n['Creation date!']:$i18n['Deadline!']).'
        </span>
      </div>
      <div class="input-field col m10 s12">
        <input
          value="'.@$_POST['name'].'"
          id="name"
          name="name"
          type="text"
          class="validate"
          maxlength="'.$NLEN.'"
          data-length="'.$NLEN.'"
          required="required"
          autocomplete="off"
          list="list"
        >
        <datalist id="list">';
        if (is_array($dat)) {
          foreach($dat as $key => $values) {
            if (!isset($marker[$values['name']]))
              $input_form .= '<option value="'.$values['name'].'">';
            $marker[$values['name']] = true;
          }
        }
        $input_form .= '
        </datalist>
        <label for="name">'.$i18n['Name'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.$i18n['Quick summary or projectname.'].'
        </span>
      </div>
      <div class="input-field col s12">
        <textarea
          id="text"
          name="text"
          class="materialize-textarea validate"
          maxlength="'.$TLEN.'"
          data-length="'.$TLEN.'"
          required="required"
        >'.@$_POST['text'].'</textarea>
        <label for="text">'.$i18n['Text'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.$i18n['Describe the issue in detail.'].'
        </span>
      </div>
      <div class="input-field col m'.$col.' s12">
        <select name="prio" id="prio">
          '.(@$_POST['prio']!=''?'<option>'.@$_POST['prio'].'</option>':'').'
          <option value="">'.$i18n['Choose'].'</option>
          '.file_get_contents('setup/prio.txt').'
        </select>
        <label for="prio">'.$i18n['Priority'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.$i18n['Select one priority.'].'
        </span>
      </div>
      <div class="input-field col m'.$col.' s12">
        <select name="status" id="status">
          '.(@$_POST['status']!=''?'<option>'.@$_POST['status'].'</option>':'').'
          <option value="">'.$i18n['Choose'].'</option>
          '.file_get_contents('types/'.$TYPE.'/status.txt').'
        </select>
        <label for="status">'.$i18n['Label'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.$i18n['Select one label.'].'
        </span>
      </div>'.PHP_EOL;

// IF "OWNER ADD" IS ALSO ALLOWED FOR NON-ADMINS or USER = ADMIN or NO LOGIN IS USED
if (($AAOW==FALSE OR @$_SESSION[$sessionname]=='admin' OR $USEL == false) and $UTOW == true) {
  $input_form .= '
      <div class="input-field col m4 s12">
        <select name="owner" id="owner">
          '.(@$_POST['owner']!=''?'<option value="'.@$_POST['owner'].'">'.ucwords(@$_POST['owner']).'</option>':'').'
          <option value="">'.$i18n['Choose'].'</option>
          '.$owner_names.'
        </select>
        <label for="status">'.$i18n['Owner'].'</label>
        <span class="helper-text" data-error="Error" data-success="OK">
          '.$i18n['Select one owner.'].'
        </span>
      </div>'.PHP_EOL;
} // </END> SHOW OWNER SELECT BOX IN ADD FORM

$input_form .= '
    </div>
    <button class="black btn waves-effect waves-light" type="submit" name="action">
      '.$i18n['Save New Issue'].'
      <icon add inverse></icon>
    </button>
  </form>
</div>
<br>';

// SEARCH HEADER
}else{
  $input_form = '';
  echo '<div class="badge grey lighten-3 hoverable" style="padding:1em">
       <h3 class="grey-text text-darken-2">'.$i18n['Search for'].' [&nbsp;'.
       htmlentities($_GET['q']).'&nbsp;]</h3></div><br>';
  $searchArray = explode(' ', $_GET['q']);
}

// DEV ONLY
#die('<h2>DEV</h2><hr><xmp>'.$var.'</xmp><hr>');

// DATA TABLE
if ( !empty( $dat ) ) {

  // TABLE HEADER
  $table = '
  <table class="striped responsive-table hoverable tablesorter {sortlist:['.$SORT.']}" id="table">
    <thead>
      <tr style="line-height:1;">
      <th>'.$i18n['ID'].'</th>
      <th>'.($MODE=='C'?$i18n['Creation date!']:$i18n['Deadline!']).'</th>
      <th>'.$i18n['Name'].'</th>
      <th class="no print">'.$i18n['Text'].'</th>
      <th>'.$i18n['Label'].'</th>
      <th>'.$i18n['Priority'].'</th>'.
      ( ((@$_SESSION[$sessionname]=='admin' OR $USEL == false) AND $UTOW == TRUE )?
      '      <th>'.$i18n['Owner']."</th>\n":'').'
      <th class="noprint" style="text-align:center">'.$i18n['Action'].'</th>
      </tr>
    </thead>
  <tbody>
  ';

  // LOOP EACH ENTRY
  foreach( $dat as $key => $values ) {

    // CHECK OWNER
    if ( @$_SESSION[$sessionname] != 'admin' AND
         @$values['owner'] != @$_SESSION[$sessionname] AND
         $USEL == true
       )
      continue;

    // CHECK SEARCH
    $datLine = $values['date'].' '.$values['name'].' '.$values['text'].' '.$values['status'];
    if ( @$_GET['q'] != '' AND contains($searchArray, $datLine) != count($searchArray) )
      continue;
    else
      $found = 1;

    // SET TABLE LINE
    $css = '';
    require 'types/'.$TYPE.'/status.php';
    $toast = 'M.toast({html: "<span>'.mb_strimwidth($values['name'],0,$NLEN/3,'...').'</span>'.
             '<a href=?delete_id='.$key.' class=toast-action>'.
             $i18n['DELETE'].'</a>&nbsp;&nbsp;"});';
    $table .= '<tr style="line-height:1;"><td style="vertical-align:top;" title="'.
              date($DATE.' '.$TIME,$values['id']).'">'.$values['id'].
              '</td><td style="vertical-align:top;white-space:nowrap;">'.$values['date'];

    // CHECK IF DATE IS REACHED AND ENTRY IS NOT √
    // https://www.geeksforgeeks.org/comparing-two-dates-in-php/
    #die( '#'.strtotime(preg_replace('/[^0-9]/','-',$values['date'])) .'#<br>#'. time().'#' ); // DEV ONLY
    #if ( $values['date'] <= date($DATE) AND

    if ( strtotime(preg_replace('/[^0-9]/','-',$values['date'])) <= time() AND
         $values['status'] != 'DONE'   AND
         $values['status'] != 'Ready'  AND
         $values['status'] != 'FERTIG' AND
         $values['status'] != 'OK'     AND
         $values['status'] != '√'      AND
         $MODE == 'D' /* DEADLINE MODE */
       )
        $table .= '<span style="margin-left:6px;min-width:12px;height:16px;padding:0;line-height:16px;"'.
                  ' class="new badge black pulse" data-badge-caption=""><icon'.
                  ' clock inverse class="tooltipped" data-position="to"'.
                  ' data-tooltip="'.$i18n['Date reached!'].'"></icon></span>';

    // DATA: name, text, status, prio, owner, ACTION BUTTONS (DEL & EDIT)
    if ( isset($UUPL) and $UUPL==TRUE) $upload_file_counter = count(glob('data/'.$values['id']."_*.*")); /* ADD 10.02.2020 FILE-ATTACHMENT */
    $table .= '</td><td style="vertical-align:top;min-width:100px;" title="'.strip_tags($values['name']).'">'.
              mb_strimwidth($values['name'],0,$NLEN/2,'...').
              '</td><td class="no print"
              style="vertical-align:top;min-width:50%;" title="'.strip_tags($values['text']).'">'.
              mb_strimwidth($values['text'],0,$TLEN/2,'...').
              '</td><td style="vertical-align:top;"><span class="new badge '.$css.
              ' darken-1 white-text" data-badge-caption="">'.$values['status'].'</span></td>'.
              '<td style="vertical-align:top;min-width:85px;">'.@$values['prio'].'</td>'.
              (((@$_SESSION[$sessionname]=='admin' OR $USEL == false) AND $UTOW == TRUE)?
              '<td style="vertical-align:top;min-width:85px;">'.
              ucwords(@$values['owner']).'</th>':'').
              '<td class="noprint" style="vertical-align:top;white-space:nowrap;">'.
              /* SHOW DEL LINK ONLY FOR ADMIN (IF IT IS SO CONFIGURED) */
              ((@$_SESSION[$sessionname]=='admin' OR $ADEL == false OR $USEL == false)?
              '<a class="tooltipped" data-position="top"'.
              ' data-tooltip="'.$i18n['Click alert link for delete!'].'"'.
              ' href="#" onclick=\''.$toast.'\'><icon'.
              ' del></icon></a>&emsp;':'').
              '<a class="tooltipped" data-position="top" data-tooltip="'.$i18n['EDIT'].'"'.
              ' href="edit.php?id='.$key.'"><icon edit></icon></a>&emsp;'.
              /* ADD 10.02.2020 FILE-ATTACHMENT */
              ( (isset($UUPL) and $UUPL==TRUE and $upload_file_counter > 1) ? '<span title="&#62; 1">🖇️</span>' : '' ).
              ( (isset($UUPL) and $UUPL==TRUE and $upload_file_counter== 1) ? '<span title="= 1">📎</span>' : '' ).
              '</td></tr>';

    // CALC FOR PROGRESS BAR
    if ( $values['status'] == 'DONE' OR
         $values['status'] == 'Ready' OR
         $values['status'] == 'FERTIG' OR
         $values['status'] == 'OK' OR
         $values['status'] == '√'
       ) $done++;

    // COUNT ENTRIES FOR PROGRESS BAR
    $entryCounter++;

  } // END LOOP

  // SHOW FORM OVER TABLE
  if (!isset($FORM) || $FORM==false) echo $input_form;

  // ECHO PROGRESS-BAR - SEE tpl/css/progress.css
  if (@$_GET['q'] == '' && $entryCounter > 0) {
    // only if there is no search performed AND there is min. on entry
    echo '<div class="progress tooltipped" data-position="top"'.
         ' data-tooltip="'.$done.'/'.$entryCounter.' '.$TYPE.
         '" title="'.$done.'/'.$entryCounter.' '.$TYPE.'">'.
         '<div class="progress-bar green" data-width="'.($done/$entryCounter*100).
         '" style="width:'.($done/$entryCounter*100).'%"></div></div>';
  }

  // SEARCH FOUND NO DATA
  if ( $found == 0 )
    echo '<tr><td colspan="5"><h3 class="center grey-text text-darken-2">'.
         $i18n['Nothing Found!'].'</h3></td></tr>';
  else // ECHO TABLE
    echo $table.'</tbody></table><br>';

  // SHOW FORM UNDER TABLE
  if (isset($FORM) && $FORM==true) echo $input_form;

} // ENDIF no data
else{
  echo $input_form;
}

// FOOTER
include 'tpl/footer.php';
