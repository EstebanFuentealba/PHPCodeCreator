<?PHP

require_once('DBFKConstraint.class.php');

class DBConstraintArray {

    var $constraints = array();

    public function __construct() {
        
    }

    public function AddConstraint($constraint) {
        $this->constraints[] = $constraint;
    }

}

?>