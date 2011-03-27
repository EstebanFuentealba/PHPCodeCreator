<?PHP

class PHPCode {
	protected $identacion=0;
	public function getIdent($suma=0) {
		return "\n".str_pad("",($this->identacion+$suma),"\t");
	}
	public function getHeader() {
		return $this->getIdent()."/*"
		.$this->getIdent(1)."@PHPClassCreator [Doctrine + CodeIgniter]"
		.$this->getIdent(1)."@Autor: Esteban Fuentealba"
		.$this->getIdent(1)."@Email:	mi [dot] warezx [at] gmail [dot] com"
		.$this->getIdent()."*/";
	}
	
}
class PHPCodeMethod extends PHPCode {
	private $methodName ="";
	private $methodParameters;
	private $lineMethod=array();
	private $visibility = "public";
	public function __construct($name,$arrayParameters,$identacion=0,$visible="public") {
		$this->methodName = $name;
		$this->methodParameters = $arrayParameters;
		$this->identacion = $identacion;
		$this->visibility = $visible;
	}
	public function addData($line) {
		$this->lineMethod[] = $line;
	}
	public function addComment($coment) {
		$this->lineMethod[] = $coment;
	}
	public function __toString() {
		$parametros ="";
		$i=0;
		foreach($this->methodParameters as $n => $v) {
			if(is_numeric($n)) { $parametros .= "$".$v;} 
			else { $parametros .= "$".$n."=".$v; }
			if($i < (count($this->methodParameters)-1)) { $parametros .= ","; }
			$i++;
		}
		return $this->getIdent().$this->visibility." function ".$this->methodName."(".$parametros."){"
				.$this->getIdent(1).join($this->getIdent(1),$this->lineMethod)
				.$this->getIdent()."}";
	}
	
}
class PHPCodeClass extends PHPCode {
	private $methods = array();
	private $attributes=array();
	private $className = "";
	private $classExtends = array();
	private $classRequires = array();
	public function __construct($name,$arrayExtends=array("Doctrine_Record"),$requires=array()) {
		$this->className = $name;
		foreach($arrayExtends as $extend) {
			$this->classExtends[] = $extend;
		}
		foreach($requires as $require) {
			$this->classRequires[] = $require;
		}
	}
	public function addAttribute($atrib) {
		$this->attributes[] = $atrib;
	}
	public function addMethod($methodObject) {
		$this->methods[] = $methodObject;
	}
	public function __toString() {
		$extends ="";
		$i=0;
		if(is_array($this->classExtends )) {
			foreach($this->classExtends as $extend) {
				$extends .= $extend;
				if($i < (count($this->classExtends)-1)) {
					$extends .= ",";
				}
				$i++;
			}
		}
		$class = "<"."?php\n";
		$class .= $this->getHeader()
				.$this->getIdent()
				."// models/".$this->className.".php";
		if(is_array($this->classRequires )) {
			foreach($this->classRequires as $require) {
				$class.= $this->getIdent()."require_once(".$require.");";
			}
		}
		$class .= $this->getIdent()."class ".$this->className." extends ".$extends." {\n";
		if(is_array($this->attributes )) {
			foreach($this->attributes as $attribute) {
				$class .= $this->getIdent(1).$attribute;
			}
		}
		if(is_array($this->methods )) {
			foreach($this->methods as $method) {
				$class .= $method;
			}
		}
		$class .= $this->getIdent()."}\n\n";
		return $class;
	}
}


?>