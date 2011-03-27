<?PHP
require_once(dirname(__FILE__) ."/../config.inc.php");
require_once(dirname(__FILE__) ."/Database.class.php");
require_once(dirname(__FILE__) .'/DBConstraintArray.class.php');
require_once(dirname(__FILE__) .'/DBTablesArray.class.php');
require_once(dirname(__FILE__) .'/PHPClassCreator.class.php');

class MappingDB {
	var $db = null;
	var $data_base_name = null;
	var $debug	= false;

	public function __construct($data_base_name,$debug=false)
    {
		$this->debug = $debug;
		$this->data_base_name = $data_base_name;
	}
	/*
		Obtiene un Objeto DBTable con sus respectivas columnas
	*/
	public function getTable($name) {
		$this->db = new Database(DB_SERVER, DB_USER, DB_PASS, $this->data_base_name);
		$this->db->connect();
		$sql = 'DESC '.$name;
		$rows = $this->db->fetch_all_array($sql);
		$this->db->close();
		$table = new DBTable($name);
		foreach($rows as $rowData) {
			$table->AddColumn(new DBColumn($rowData));
		}
		return $table;
	}
	/*
		Retorna un DBTablesArray Con todas las tablas de la base de datos
	*/
	public function getAllTables() {
		$this->db = new Database(DB_SERVER, DB_USER, DB_PASS, $this->data_base_name);
		$this->db->connect();
		$sql = 'SHOW TABLES';
		$rows = $this->db->fetch_all_array($sql);
		$this->db->close();
		$tables=new DBTablesArray();
		foreach($rows as $rowData) {
			foreach($rowData as $k=> $v) {
				$tbl 				= $this->getTable($v);
				$tbl->constraints	= $this->getTableConstraints($tbl);
				$tables->AddTable($tbl);
			}
		}
		return $tables;
	}
	public function debug() {
		$classCreator = new PHPClassCreator($this->getAllTables()->tables,$this->debug);
		$classCreator->__debug($this->getAllTables()->tables);
	}
	/*
		
	*/
	public function createPHPCode() {
		$returnCode = array();
		$classCreator = new PHPClassCreator($this->getAllTables()->tables,$this->debug);
		$classCreator->Fix();
		return json_encode($returnCode);
		
	}
	/*
		Retorna JSON para ser implementado por la biblioteca JITJS
	*/
	public function getJitCode() {
		$returnCode = array();
		foreach($this->getAllTables()->tables as $table) {
			$constraints = array();
			if($table->constraints != null) {
				foreach($table->constraints->constraints as $constraint) {
					$constraints[] = array(
										"nodeTo" 	=> $constraint->references_table,
										"nodeFrom"	=> $table->name_table
									);
				}
			}
			$returnCode[] = array(
				"adjacencies" => $constraints
				,
				"data" => array(
					"$"."color"		=>	"#83548B",   
					"$"."type"		=>	"square",
					"$"."dim"		=>	10,
					"columns"		=> 	$table->columns_table
				),
				"id" 	=> 	$table->name_table,   
				"name" 	=>	$table->name_table
			);
		}
		return json_encode($returnCode);
		
	}
	/*
		Retorna las Constraints de una Tabla
	*/
	public function getTableConstraints($tableObject) {
		$this->db = new Database(DB_SERVER, DB_USER, DB_PASS, $this->data_base_name);
		$this->db->connect();
		$sql = "SELECT 	K.constraint_name," 
					."K.table_name 	AS 'local_table'," 
					."K.column_name 	AS 'local_column',"
					."K.referenced_table_name		AS	'foreign_table',"
					."K.referenced_column_name	AS	'foreign_column',"
					."RC.update_rule,"
					."RC.delete_rule,"
					."RC.unique_constraint_name "
				."FROM 	information_schema.referential_constraints RC " 
					."INNER JOIN information_schema.key_column_usage K "
					."ON K.constraint_name = RC.constraint_name "
					."WHERE 	K.table_name = '".$tableObject->name_table."' "
					."AND	RC.constraint_schema='".$this->data_base_name."'";
		$rows = $this->db->fetch_all_array($sql);
		$this->db->close();
		if(count($rows)>0) {
			$constraints = new DBConstraintArray();
			foreach ($rows as $row) {
				$FKConstraint = new DBFKConstraint();
				$FKConstraint->nombre_constraint 	= $row['constraint_name'];
				$FKConstraint->foreign_key_column 	= $row['local_column'];
				$FKConstraint->references_column 	= $row['foreign_column'];
				$FKConstraint->references_table 	= $row['foreign_table'];
				$FKConstraint->onDeleteOnCascade 	= $row['update_rule'];
				$FKConstraint->onUpdateOnCascade 	= $row['delete_rule'];
				$constraints->AddConstraint($FKConstraint);
			}
			return $constraints;
		}
		
	}
	/*
	public function getTableConstraints($tableObject) {
		$this->db = new Database(DB_SERVER, DB_USER, DB_PASS, $this->data_base_name);
		$this->db->connect();
		$sql = 'SHOW CREATE TABLE ' . $tableObject->name_table;
		$rows = $this->db->query($sql);
		$row = $this->db->fetch_array($rows);
		$this->db->close();
		
		if (!$row) {
			return false;
		}

		$regExp  = '/,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
			. 'REFERENCES `([^`]*)` \(`([^`]*)`\)'
			. '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
			. '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?/';
		$matches = array();
		preg_match_all($regExp, $row['Create Table'], $matches, PREG_SET_ORDER);
		if(count($matches)>0) {
			$constraints = new DBConstraintArray();
			foreach ($matches as $match) {
				$constraints->AddConstraint(new DBFKConstraint($match));
			}
			return $constraints;
		} else {

		}
		
	}
	*/
}


?>