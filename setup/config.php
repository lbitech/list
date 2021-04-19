<?PHP /* 108 Lines */

/* -------------------------------------------------------- */
/* ------- SYSTEM SETTINGS for v4.0 from 12.02.2020 ------- */
/* -------------------------------------------------------- */

//  SETTINGS
    $TYPE = 'GTD'; // ON CHANGES REMEMBER TO RENAME ALSO THE FILE IN THE /data/ FOLDER
    //      'Issues'     https://en.wikipedia.org/wiki/Issue_tracking_system
    //      'Bugs'       https://en.wikipedia.org/wiki/Bug_tracking_system
    //      'MOSCOW'     https://en.wikipedia.org/wiki/MoSCoW_method
    //      'GTD'        https://en.wikipedia.org/wiki/Getting_Things_Done
    //      'Aufgaben'   Tracker Type in German (Deutsch)
    //      'Etapas'     Tracker Type in Portuguese (Brazil)

    // SET THE DATE MODE - USE 'C' = 'Creation date!' OR 'D' = 'Deadline!'
    $MODE = 'D'; // DEFAULT 'D'

    // SHOW THE ADD FORM AT THE PAGE BOTTOM (true) - IF false SHOW FORM AT PAGE TOP
    $FORM = false; // DEFAULT false

    // ONLY THE ADMIN IS ALLOWED TO DELETE ENTRIES - TRUE = YES AND FALSE = NO
    $ADEL = true; // DEFAULT true

    // USE THE OWNER FEATURE - TRUE = YES AND FALSE = NO
    $UTOW = true; // DEFAULT true

    // ONLY THE ADMIN IS ALLOWED TO ADD THE ENTRIES OWNER - TRUE = YES AND FALSE = NO
    $AAOW = true; // DEFAULT true

    // ONLY THE ADMIN IS ALLOWED TO EDIT THE ENTRIES OWNER - TRUE = YES AND FALSE = NO
    $AEOW = false; // DEFAULT true

    // SHOW BACKUP-LINK IN MAIN NAVIGATION (ALWAYS FOR ADMIN ONLY) - TRUE = YES AND FALSE = NO
    $SBAK = true; // DEFAULT true

    //  SHOW Excel-EXPORT-LINK IN MAIN NAVIGATION (ALWAYS FOR ADMIN ONLY) - TRUE = YES AND FALSE = NO
    $SEXP = true; // DEFAULT true

    // USE FILE UPLOAD IN EDIT MOD (ALWAYS FOR ALL USER) - TRUE = YES AND FALSE = NO
    $UUPL = true; // DEFAULT true
    // define all allowed file types (NEVER ALLOW json)
    $UPLOAD_ALLOWED_FILE_TYPES = array(
      'jpg', 'png', 'jpg', 'jpeg', 'gif', 'bmp', /* IMAGES */
      'doc', 'rtf', 'txt', 'docx', 'pdf',        /* TEXT   */
      'mp3', 'mp4', 'wav', 'mpg',                /* MEDIA  */    
      'xls', 'csv', 'xlsx',                      /* TABLE  */
      'zip', 'rar',                              /* ARCHIV */
    );
    // define max. File Size in MB for one upload file
    $UPLOAD_MAX_FILE_SIZE_ADMIN = 5; // in MB


//  LOGIN
    // MAKE USE OF LOGIN - TRUE = YES AND FALSE = NO
    $USEL = true; // DEFAULT true

    // ACCOUNTS (USERNAME => PASSWORD) - SHOULD BE CHANGED BEFORE FIRST REAL USE!
    // USER SEE ONLY THERE OWN RECORDS, ONLY THE USER NAMED 'admin' SEE ALL RECORDS!
    $DATA = array(
    /* User   =>  Password */
      'admin' => 'demo',
      'demo'  => 'demo',
      'tom'   => 'demo',
      'john'  => 'demo',
      'harry' => 'demo',
    );


//  FORMAT
    // SET TIMEZONE - SEE: https://www.php.net/manual/en/timezones.php
    date_default_timezone_set('UTC'); // DEFAULT 'UTC'

    // DATE FORMAT FOR DATA FILE - SEE: http://php.net/manual/en/function.date.php
    $DATE = 'Y.m.d'; // DEFAULT 'Y.m.d'
    #$DATE = 'd.m.Y'; // DEFAULT for germany
    $TIME = 'H:m'; // DEFAULT 'H:m'

    // DATE FORMAT FOR JS DATEPICKER - SEE: http://materializecss.com/pickers.html
    $PICK = 'yyyy.mm.dd'; // DEFAULT 'yyyy.mm.dd'
    #$PICK = 'dd.mm.yyyy'; // DEFAULT for germany

    // INITIAL TABLE SORT - SEE: https://mottie.github.io/tablesorter/docs/index.html#sortlist
    // EXAMPLE: '[1,0],[2,0]' = sort columns (2nd and 3rd) both Ascending
    // The second parameter is the sortDirection: 0 is for Ascending and 1 is for Descending
    $SORT = '[4,1],[1,0]'; // DEFAULT '[1,0]' // SORT BY 2nd COLUMN DATE, OLDEST DATE FIRST

    // DATE FORMAT FOR TABLE SORT
    $DSOR = 'yyyymmdd'; // DEFAULT 'yyyymmdd'
    #$DSOR = 'ddmmyyyy'; // DEFAULT for germany

    // NAME LENGHT IN CHARACTERS (Half the number of characters are displayed in the table)
    $NLEN = 128; // DEFAULT 128

    // TEXT LENGHT IN CHARACTERS (Half the number of characters are displayed in the table)
    $TLEN = 256; // DEFAULT 256

    // SET DEFAULT LANGUAGE (IF BROWSER-LANGUAGE DOSN'T EXIST) 'en' = english 'de' = german 'pt' = portuguese
    // AND don't forget to rename the /setup/xx_prio.txt you need to /setup/prio.txt
    $LANG = 'en'; // USE 'en', 'de' or 'pt' 

    // LOGIN SESSION HASH
    $SALT = 'XQEoI6R4C3'; // USE first value from URL https://randomkeygen.com/

/* ----------------- */
/* EOF - END OF FILE */
/* ----------------- */
