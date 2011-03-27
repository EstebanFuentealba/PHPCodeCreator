<?PHP
require_once('DBTable.class.php');
class DBTablesArray {
	var $tables = array();
	public function __construct()
    {
	}
	public function AddTable($db_table) {
		$this->tables[$db_table->name_table] = $db_table;
	}
	public function Fix() {
		return json_encode($this->tables);
	}
}
?>