<?PHP

require_once('DBConstraint.class.php');

class DBFKConstraint extends DBConstraint {

    var $foreign_key_column = null;
    var $references_column = null;
    var $references_table = null;
    var $onDeleteOnCascade = null;
    var $onUpdateOnCascade = null;

    public function __construct($dataArray=null) {
        if (is_null($dataArray)) {
            $this->nombre_constraint = $dataArray[1];
            $this->foreign_key_column = $dataArray[2];
            $this->references_column = $dataArray[4];
            $this->references_table = $dataArray[3];
            $this->onDeleteOnCascade = $dataArray[5];
            $this->onUpdateOnCascade = $dataArray[5];
        }
    }

}

?>