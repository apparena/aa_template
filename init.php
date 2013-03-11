<?php
/**
 * Setup the environment
 */
date_default_timezone_set('Europe/Berlin'); // Set timezone
ini_set('session.gc_probability', 0); // Disable session expired check
header('P3P: CP=CAO PSA OUR'); // Fix IE save cookie in iframe problem
define("ROOT_PATH", realpath(dirname(__FILE__))); // Set include path
set_include_path(ROOT_PATH . '/libs/' . PATH_SEPARATOR);

/**
 * Include necessary libraries
 */
require_once ROOT_PATH . '/config.php';
require_once ROOT_PATH . '/libs/fb-php-sdk/3.2/src/facebook.php';
require_once ROOT_PATH . '/libs/AA/fb_helper.php';
require_once ROOT_PATH . '/libs/functions.php';
require_once ROOT_PATH . '/libs/AA/1.0/src/aa_helper.php';
require_once ROOT_PATH . '/libs/AA/1.0/src/AppManager.php';
require_once ROOT_PATH . '/libs/Zend/Translate.php';

/* Try to init some basic variables */
$aa = false;
$aa_inst_id = false;
if ( isset($_GET['aa_inst_id']) ) $aa_inst_id = $_GET['aa_inst_id'];
if ( isset($_POST['aa_inst_id']) ) $aa_inst_id = $_POST['aa_inst_id'];

/* Initialize and set Facebook information in the session */
if (isset ($_REQUEST["signed_request"])) {
    $aa['fb'] = array();
    $fb_signed_request = parse_signed_request($_REQUEST["signed_request"]);
    $is_fb_user_admin = is_fb_user_admin();
    $is_fb_user_fan = is_fb_user_fan();
    $fb_data = array("is_fb_user_admin" => $is_fb_user_admin,
        "is_fb_user_fan" => $is_fb_user_fan,
        "signed_request" => $fb_signed_request,
    );
    if (isset($fb_signed_request['page'])) {
        $fb_data['page'] = $fb_signed_request['page'];
    }
    if (isset($fb_signed_request['user'])) {
        $fb_data['user'] = $fb_signed_request['user'];
    }
    if (isset($fb_signed_request['user_id'])) {
        $fb_data['fb_user_id'] = $fb_signed_request['user_id'];
    }
    foreach ($fb_data as $k => $v) {
        $aa['fb'][$k] = $v;
    }
    $aa['fb']['share_url'] = "https://apps.facebook.com/" . $aa['instance']['fb_app_url'] . "/libs/AA/fb_share.php?aa_inst_id=" . $aa['instance']['aa_inst_id'];
}

/* Initialize localization */
$cur_locale = $aa_default_locale;
if ( isset( $aa['fb']['signed_request']['app_data'] ) ) {
    $app_data = json_decode( $aa['fb']['signed_request']['app_data'] , true);
}
if ( isset( $_GET['locale'] ) ) {
    $cur_locale = $_GET['locale'];
} else if ( isset( $app_data ) && isset( $app_data['locale'] ) ) {
    $cur_locale = $app_data['locale'];
} else if ( isset( $aa_inst_id ) && isset( $_COOKIE['aa_inst_locale_' . $aa_inst_id] ) ) {
    $cur_locale = $_COOKIE['aa_inst_locale_' . $aa_inst_id];
}

/*  Connect to App-Arena.com App-Manager and init session */
$appmanager = new AA_AppManager(array(
    'aa_app_id' => $aa_app_id,
    'aa_app_secret' => $aa_app_secret,
    'aa_inst_id' => $aa_inst_id,
    'locale' => $cur_locale
));

/* Start session and initialize App-Manager content */
$aa_instance = $appmanager->getInstance();
$aa_scope = 'aa_' . $aa_instance['aa_inst_id'];
session_name($aa_scope);
session_start();

$fb_temp = $aa['fb'];
$aa =& $_SESSION;
$aa['instance'] = $appmanager->getInstance();
$aa['config'] = $appmanager->getConfig();
$aa['locale'] = $appmanager->getTranslation( $cur_locale );
$aa['fb'] = $fb_temp;

/* Collect environment information */
require_once 'libs/Mobile_Detect.php';
$detector = new Mobile_Detect;
if ( isset( $_REQUEST['signed_request'] ) ) {
    if ( isset( $fb_signed_request['page'] ) ){
        $aa['env']['base_url'] = $aa['instance']['page_tab_url'];
        $aa['env']['base'] = 'page';
    } else {
        $aa['env']['base_url'] = "https://apps.facebook.com/" . $aa['instance']['fb_app_url'] . "/?aa_inst_id=" . $aa['instance']['aa_inst_id'];
        $aa['env']['base'] = 'canvas';
    }
} else {
    $aa['env']['base_url'] = $aa['instance']['fb_canvas_url'] . "?aa_inst_id=" . $aa['instance']['aa_inst_id'];
    $aa['env']['base'] = 'website';
}
if ( $detector->isMobile() ) {
    $aa['env']['device'] = 'mobile';
} else if ( $detector->isTablet() ) {
    $aa['env']['device'] = 'tablet';
} else {
    $aa['env']['device'] = 'desktop';
}
// Add browser info to the env
$aa['env']['browser'] = getBrowser();

/* Setup the translation objects */
$aa_locale = new Zend_Translate('array', $aa['locale'], $cur_locale );
$aa_locale->setLocale( $cur_locale );
global $aa_translate;
$aa_translate->translate = $aa_locale;
setcookie('aa_inst_locale_' . $aa_instance['aa_inst_id'], $cur_locale);
$aa_locales = explode(',',$aa['config']['admin_locale_switch']['value']);

/* Catch error, in case there is no instance */
if ( $aa['instance'] == "instance not activated" || $aa['instance'] == "instance not exist" ) {
    include ('templates/error.php');
    exit();
}


/**
 * Setup mysql database connection
 */
if ($db_activated) {
    require_once ROOT_PATH . '/libs/Zend/Db/Adapter/Pdo/Mysql.php';
    $db = new Zend_Db_Adapter_Pdo_Mysql(array(
        'host' => $db_host,
        'username' => $db_user,
        'password' => $db_pass,
        'dbname' => $db_name
    ));
    // setup db in case the zend db does not work
    $connection = mysql_connect($db_host, $db_user, $db_pass);
    if ($connection) {
        $db_raw = mysql_select_db($db_name, $connection);
        mysql_query("set names utf8;");
    }
}