<?PHP
require_once('DBColumn.class.php');
class DBTable {
	var $name_table	= null;
	var $columns_table = array();
	var $constraints 	= null;
	public function __construct($name) {
		$this->name_table = $name;
	}
	public function AddColumn($db_column) {
		$this->columns_table[$db_column->name_column] = $db_column;
	}
	public function __toString() {
		return array($this->name_table => array('columns'=>$this->columns_table,'constraints'=>$this->constraints));
	}
	public function GetPK() {
		$pks=array();
		foreach($this->columns_table as $column) {
			if($column->isPrimaryKey_column) { 
				$pks[] = $column->name_column;
			}
		}
		return $pks;
	}
	public function GetUCName() {
		return ucwords($this->GetLowerName());
	}
	public function GetLowerName() {
		return strtolower($this->name_table);
	}
	public function GetPluralName() {
		return $this->GetUnionName(true);
	}
	public function GetUnionName($plural=false) {
		$ex = @explode("_",$this->GetLowerName());
		if(count($ex) > 0) {
			$d="";
			$x=0;
			foreach($ex as $palabra) {
				if($x==0) {
					$d .= $palabra;
					if($plural) { $d .= "s"; }
				} else {
					$d .= ucwords($palabra);
					if($plural) { $d .= "s"; }
				}
				$x++;
			}
			return $d;
		}
		return $this->GetLowerName()."s";
	}
	public function GetTypeColumns() {
		$arr=null;
		foreach($this->columns_table as $columnName => $columnData) {
			$pos = strpos($columnData->type_column, "(");
			$arr[$columnName] = (($pos > 1) ? substr($columnData->type_column,0,$pos) : $columnData->type_column);
		}
		return var_export($arr, TRUE);
	}
}
?>