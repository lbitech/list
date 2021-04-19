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

// DEFAULT DATE IF NOT SET
if ( $dat[@$_GET['id']]['date'] == '' )
  $dat[@$_GET['id']]['date'] = date( $DATE );

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

// IF "OWNER EDIT" IS ALSO ALLOWED FOR NON-ADMINS or USER = ADMIN or NO LOGIN IS USED
if (($AEOW==FALSE OR @$_SESSION[$sessionname]=='admin' OR $USEL == false) and $UTOW == true) {
  // THEN SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 4; // 4 = 3 colls
} else {
  // DON'T SHOW OWNER SELECT BOX IN EDIT FORM
  $col = 6; // 6 = 2 colls
}
?>
<div class="badge grey lighten-3 hoverable" style="padding:1em">
  <h3 class="grey-text text-darken-2"><?php echo $i18n['Edit Issue']; ?></h3>
  <form method="POST">
    <input type="hidden" name="owner" value="<?php echo ($dat[@$_GET['id']]['owner']=='')?$_SESSION[$sessionname]:$dat[@$_GET['id']]['owner']; ?>">
    <div class="row">
      <div class="input-field col m2 s12">
        <input
          value="<?php echo $dat[@$_GET['id']]['date']; ?>"
          id="date"
          name="date"
          type="text"
          class="datepicker"
          maxlength="<?php strlen($PICK); ?>"
          data-length="<?php strlen($PICK); ?>"
          required="required"
          placeholder="<?php echo $dat[@$_GET['id']]['date']; ?>"
        >
        <label class="active" for="date"><?php echo $i18n['Date']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo ($MODE=='C')?$i18n['Creation date!']:$i18n['Deadline!']; ?>
        </span>
      </div>
      <div class="input-field col m10 s12">
        <input
          value="<?php echo $dat[@$_GET['id']]['name']; ?>"
          id="name"
          name="name"
          type="text"
          class="validate"
          maxlength="<?php echo $NLEN; ?>"
          data-length="<?php echo $NLEN; ?>"
          required="required"
          autocomplete="off"
          list="list"
          placeholder="<?php echo strip_tags($dat[@$_GET['id']]['name']); ?>"
        >
        <datalist id="list">
          <?php echo $list; ?>
        </datalist>
        <label class="active" for="name"><?php echo $i18n['Name']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo $i18n['Quick summary or projectname.']; ?>
        </span>
      </div>
      <div class="input-field col s12">
        <textarea
          id="text"
          name="text"
          class="materialize-textarea validate"
          maxlength="<?php echo $TLEN; ?>"
          data-length="<?php echo $TLEN; ?>"
          required="required"
          placeholder="<?php echo strip_tags($dat[@$_GET['id']]['text']); ?>"
        ><?php echo $dat[@$_GET['id']]['text']; ?></textarea>
        <label class="active" for="text"><?php echo $i18n['Text']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo $i18n['Describe the issue in detail.']; ?>
        </span>
      </div>
      <div class="input-field col m<?php echo $col; ?> s12">
        <select name="prio" id="prio">
          <option selected><?php echo @$dat[@$_GET['id']]['prio'];?></option>
          <option value="" disabled><?php echo $i18n['Choose']; ?></option>
          <?php require 'setup/prio.txt'; ?>
        </select>
        <label for="prio"><?php echo $i18n['Priority']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo $i18n['Select one priority.']; ?>
        </span>
      </div>
      <div class="input-field col m<?php echo $col; ?> s12">
        <select name="status" id="status">
          <option selected><?php echo $dat[@$_GET['id']]['status'];?></option>
          <option value="" disabled><?php echo $i18n['Choose']; ?></option>
          <?php require 'types/'.$TYPE.'/status.txt'; ?>
        </select>
        <label for="status"><?php echo $i18n['Label']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo $i18n['Select one label.']; ?>
        </span>
      </div>
  <?php
  // IF "OWNER EDIT" IS ALSO ALLOWED FOR NON-ADMINS or USER = ADMIN or NO LOGIN IS USED
  if (($AEOW==FALSE OR @$_SESSION[$sessionname]=='admin' OR $USEL == false) and $UTOW == true):
    // THEN SHOW OWNER SELECT BOX IN EDIT FORM
  ?>

      <div class="input-field col m<?php echo $col; ?> s12">
        <select name="owner" id="owner">
          <option selected value="<?php echo $dat[@$_GET['id']]['owner']; ?>"><?php echo ucwords($dat[@$_GET['id']]['owner']); ?></option>
          <option value="" disabled><?php echo $i18n['Choose']; ?></option>
          <?php echo $owner_names; ?>
        </select>
        <label for="owner"><?php echo $i18n['Owner']; ?></label>
        <span class="helper-text" data-error="Error" data-success="OK">
          <?php echo $i18n['Select one owner.']; ?>
        </span>
      </div>

  <?php
    endif;
  ?>
  <div class="input-field col m6 s12">
    <button class="black btn waves-effect waves-light" type="submit" name="action">
      <?php echo $i18n['Save Issue']; ?>
      <icon save inverse></icon>
    </button>
    </form>
  </div>
  <?php
  /* ADD 10.02.2020 FILE-ATTACHMENT */
  // IF $UUPL=TRUE IN CONFIG THEN "USE UPLOAD" IS ALLOWED
  if ( isset($UUPL) and $UUPL==TRUE ):
    // THEN SHOW UPLOAD FORM IN EDIT FORM
  ?>
    <div class="input-field col m6 s12">
      <form style="display:inline-block;max-width:390px" class="file-field" action="upload.php?id=<?php echo $dat[@$_GET['id']]['id']; ?>" method="post" enctype="multipart/form-data" target="result" onsubmit="setTimeout(function(){window.location.reload();},3000)">
        <?php /* Get all Files that start with that ID before an underscore '_' */ $file = glob('data/'.$dat[@$_GET['id']]['id']."_*.*"); if ( count($file) < 1) {
        /* NO UPLOADED FILES AVAILABLE YET */ ?>
        <!-- SELECT UPLOAD -->
        <div class="black btn waves-effect waves-light" title="<?php echo $i18n["SELECT UPLOAD"]; ?>">
          <icon add inverse></icon>
          <input type="file" accept="<?php echo rtrim('.'.implode(',.',$UPLOAD_ALLOWED_FILE_TYPES),',.'); ?>" name="fileToUpload" onchange="document.getElementById('filename').value=this.value.replace('C:\\fakepath\\', '');">
        </div>
        <!-- INFO IF NO FILE EXISTS -->
        <div class="file-path-wrapper" style="float:left;padding-right:5px;">
          <input id="filename" type="text" class="file-path" readonly placeholder="<?php echo $i18n["SELECT UPLOAD"]; ?>...">
        </div>
        <!-- UPLOAD -->
        <button title="<?php echo $i18n["UPLOAD"]; ?>" class="black btn waves-effect waves-light" type="submit" name="submit"><icon style="transform:rotate(-90deg);" logout inverse></icon></button>
        <?php } ?>

        <?php if ( count($file) > 0) { /* FILES HAVE ALREADY BEEN UPLOADED */ ?>
        <!-- DELETE -->
        <button style="margin-right:5px;" title="<?php echo $i18n["DELETE"]; ?>" onclick="return confirm('<?php echo $i18n["Are you sure?"] ; ?>');" class="white btn waves-effect waves-light" type="submit" name="delete" ><icon del></icon></button>
        <!-- SELECT UPLOAD -->
        <div class="black btn waves-effect waves-light" title="<?php echo $i18n["SELECT UPLOAD"]; ?>">
          <icon add inverse></icon>
          <input type="file" accept="<?php echo rtrim('.'.implode(',.',$UPLOAD_ALLOWED_FILE_TYPES),',.'); ?>" name="fileToUpload" onchange="document.getElementById('select').innerHTML='<input style=&quot;margin:0 5px&quot; type=&quot;text&quot; class=&quot;file-path&quot; readonly value=&quot;'+this.value.replace('C:\\fakepath\\', '')+'&quot;>';">
        </div>
        <!-- SELECT DOWNLOAD -->
        <div id="select" style="float:left;padding:0 5px;">
        <select name="archivedFile" id="download" title="<?php echo $i18n["SELECT DOWNLOAD"]; ?>">
        <option value=""><?php echo $i18n["SELECT DOWNLOAD"]; ?>...</option>
        <?php foreach($file as $name){$view=explode('_',$name,2);echo'<option value="'.$name.'">'.$view[1].'</option>';} ?>
        </select>
        </div>
        <!-- UPLOAD -->
        <button style="margin-bottom:5px" title="<?php echo $i18n["UPLOAD"]; ?>" class="black btn waves-effect waves-light" type="submit" name="submit"><icon style="transform:rotate(-90deg);" logout inverse></icon></button>
        <!-- DOWNLOAD -->
        <?php /* Build URL of Script without Filename */
          $url=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['HTTP_HOST'].dirname(htmlspecialchars($_SERVER['PHP_SELF'])).'/';
        ?>
        <button title="<?php echo $i18n["DOWNLOAD"]; ?>" style="float:left;margin-left:5px;margin-bottom:5px" class="black btn waves-effect waves-light" onClick="if(document.getElementById('download').options[document.getElementById('download').selectedIndex].value!=''){window.open('<?php echo $url; ?>'+document.getElementById('download').options[document.getElementById('download').selectedIndex].value);}"><icon save inverse></icon></button>
        <?php } ?>

        <iframe name="result" style="width:100%" height="65" frameBorder="0"></iframe>
      </form>
    </div>
  <?php
    endif;
  ?>
    

</div><br>

<?php

// FOOTER
include 'tpl/footer.php';
