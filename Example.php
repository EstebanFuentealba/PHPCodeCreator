<?PHP

require_once(dirname(__FILE__) . "/config.inc.php");
require_once(dirname(__FILE__) . "/class/MappingDB.class.php");
require_once(dirname(__FILE__) . "/class/PHPCode.class.php");
$debug = isset($_GET['debug']) ? (($_GET['debug'] == TRUE) ? TRUE : NULL) : NULL;
$m = new MappingDB(DB_DATABASE, FALSE);
$m->debug();
?>