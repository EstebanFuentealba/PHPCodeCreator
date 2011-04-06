<?PHP

require_once(dirname(__FILE__) . '/MappingDB.class.php');
require_once(dirname(__FILE__) . "/ZipFile.class.php");
require_once(dirname(__FILE__) . "/PHPCode.class.php");

class PHPClassCreator {

    public $MYSQL_DATA_TYPES = array(
        'varchar' => 0,
        'int' => 1,
        'tinyint' => 1,
        'text' => 0,
        'bit' => 2,
        'bool' => 2,
        'boolean' => 2,
        'bigint' => 1,
        'binary' => 1,
        'blob' => 6,
        'char' => 0,
        'date' => 10,
        'datetime' => 10,
        'decimal' => 4,
        'double' => 4,
        'enum' => 11,
        'float' => 3,
        'integer' => 1,
        'numeric' => 1,
        'time' => 9,
        'timestamp' => 8
    );
    public $DOCTRINE_DATA_TYPES = array(
        'string', //0
        'integer', //1
        'boolean', //2
        'float', //3
        'decimal', //4
        'object', //5
        'blob', //6
        'clob', //7
        'timestamp', //8
        'time', //9
        'date', //10
        'enum', //11
        'gzip', //12
    );
    public $tableObject = null;
    public $constraintFK = array();
    public $tableDefinition = array();
    public $modelClassDefinition = array();
    public $controllerClassDefinition = array();
    public $phpCode = null;
    public $debug = false;

    public function __debug($tables, $debug=false) {
        $this->debug = $debug;
        $zipfile = new ZipFile();
        $zip = new ZipArchive();
        /**
         * Agrega ci + doctrine + assets y public_html al zip
         */
        $dir_files = $this->dir_tree(APPDIR . "/files");
        foreach ($dir_files as $dir_file) {
            if (is_file($dir_file)) {
                $dir = explode(APPDIR . "/files/", $dir_file);
//Otra forma de comprecion nativa de php si existe test,zip
//                if ($zip->open('test.zip') === TRUE) {
//                    $zip->addFile($dir_file, $dir[1]);
//                    $zip->close();
//                    echo 'ok';
//                } else {
//                    echo 'failed';
//                }
                $handle = fopen($dir_file, "r");
                if (filesize($dir_file) != 0) {
                    $contenido = fread($handle, filesize($dir_file));
                    $zipfile->add_file($contenido, $dir[1]);
                }
                fclose($handle);
            }
        }
        $phpCIDatabaseConfig = '<?php
        if (!defined("BASEPATH"))
            exit("No direct script access allowed");
        /*
          | -------------------------------------------------------------------
          | DATABASE CONNECTIVITY SETTINGS
          | -------------------------------------------------------------------
          | This file will contain the settings needed to access your database.
          |
          | For complete instructions please consult the "Database Connection"
          | page of the User Guide.
          |
          | -------------------------------------------------------------------
          | EXPLANATION OF VARIABLES
          | -------------------------------------------------------------------
          |
          |	["hostname"] The hostname of your database server.
          |	["username"] The username used to connect to the database
          |	["password"] The password used to connect to the database
          |	["database"] The name of the database you want to connect to
          |	["dbdriver"] The database type. ie: mysql.  Currently supported:
          mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
          |	["dbprefix"] You can add an optional prefix, which will be added
          |				 to the table name when using the  Active Record class
          |	["pconnect"] TRUE/FALSE - Whether to use a persistent connection
          |	["db_debug"] TRUE/FALSE - Whether database errors should be displayed.
          |	["cache_on"] TRUE/FALSE - Enables/disables query caching
          |	["cachedir"] The path to the folder where cache files should be stored
          |	["char_set"] The character set used in communicating with the database
          |	["dbcollat"] The character collation used in communicating with the database
          |	["swap_pre"] A default table prefix that should be swapped with the dbprefix
          |	["autoinit"] Whether or not to automatically initialize the database.
          |	["stricton"] TRUE/FALSE - forces "Strict Mode" connections
          |							- good for ensuring strict SQL while developing
          |
          | The $active_group variable lets you choose which connection group to
          | make active.  By default there is only one group (the "default" group).
          |
          | The $active_record variables lets you determine whether or not to load
          | the active record class
         */

        $active_group = "default";
        $active_record = TRUE;

        $db["default"]["hostname"] = "'.DB_SERVER.'";
        $db["default"]["username"] = "'.DB_USER.'";
        $db["default"]["password"] = "'.DB_PASS.'";
        $db["default"]["database"] = "'.DB_DATABASE.'";
        $db["default"]["dbdriver"] = "mysql";
        $db["default"]["dbprefix"] = "";
        $db["default"]["pconnect"] = TRUE;
        $db["default"]["db_debug"] = TRUE;
        $db["default"]["cache_on"] = FALSE;
        $db["default"]["cachedir"] = "";
        $db["default"]["char_set"] = "utf8";
        $db["default"]["dbcollat"] = "utf8_general_ci";
        $db["default"]["swap_pre"] = "";
        $db["default"]["autoinit"] = TRUE;
        $db["default"]["stricton"] = FALSE;


        // load Doctrine library
        require_once BASEPATH . "/database/doctrine/Doctrine.php";

        // this will allow Doctrine to load Model classes automatically
        spl_autoload_register(array("Doctrine", "autoload"));

        // we load our database connections into Doctrine_Manager
        // this loop allows us to use multiple connections later on
        foreach ($db as $connection_name => $db_values) {

            // first we must convert to dsn format
            $dsn = $db[$connection_name]["dbdriver"] .
                    "://" . $db[$connection_name]["username"] .
                    ":" . $db[$connection_name]["password"] .
                    "@" . $db[$connection_name]["hostname"] .
                    "/" . $db[$connection_name]["database"];

            Doctrine_Manager::connection($dsn, $connection_name);
        }

        // CodeIgniter"s Model class needs to be loaded
        //require_once BASEPATH . "/libraries/Model.php";

        // telling Doctrine where our models are located
        Doctrine::loadModels(APPPATH . "/models");


        // (OPTIONAL) CONFIGURATION BELOW
        // this will allow us to use "mutators"
        Doctrine_Manager::getInstance()->setAttribute(
                Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

        // this sets all table columns to notnull and unsigned (for ints) by default
        Doctrine_Manager::getInstance()->setAttribute(
                Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS, array("notnull" => true, "unsigned" => true));

        // set the default primary key to be named "id", integer, 4 bytes
        Doctrine_Manager::getInstance()->setAttribute(
                Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS, array("name" => "id", "type" => "integer", "length" => 4));

        /* End of file database.php */
        /* Location: ./application/config/database.php */';
        /* Agrega Configuración de Database CodeIgniter */
        $zipfile->add_file($phpCIDatabaseConfig, "ci/app/config/database.php");
        

        $class = new PHPCodeClass("KoalaController", array("REST_Controller"), array("APPPATH.'/libraries/REST_Controller.php'"));
        $class->addAttribute("protected $" . "conditions = array('equal' => array(\"%f='%s'\",false,\"string\"),'notEqual' => array(\"%f!='%s'\",false,\"string\"),'startsWith' => array(\"%f like '%s%'\",false,\"string\"),'notStartsWith' => array(\"not(%f like '%s%')\",false,\"string\"),'contains' => array(\"%f like '%%s%'\",false,\"string\"),'notContains' => array(\"not(%f like '%%s%')\",false,\"string\"),'biggerThan' => array(\"%f>'%s'\",false,\"integer\"),'biggerOrEqual' => array(\"%f>='%s'\",false,\"integer\"),'smallerThan' => array(\"%f<'%s'\",false,\"integer\"),'smallerOrEqual' => array(\"%f<='%s'\",false,\"integer\"),'between' => array(\"%f between '%s1' and '%s2'\",true,array(\"integer\",\"date\",\"datetime\")),'notBetween' => array(\"not(%f between '%s1' and '%s2')\",true,array(\"integer\",\"date\",\"datetime\")));");
//#####
//	METHOD __construct
//#####
        $method = new PHPCodeMethod("__construct", array(), 1);
        $method->addData("parent::__construct();");
        $class->addMethod($method);
//#####
//	METHOD flush
//#####
        $method = new PHPCodeMethod("flush", array(), 1);
        $method->addData('$manager = Doctrine_Manager::getInstance();');
        $method->addData('$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);');
        $method->addData('$conn = Doctrine_Manager::connection();');
        $method->addData('$conn->flush();');
        $class->addMethod($method);
//#####
//	METHOD __isType
//#####
        $method = new PHPCodeMethod("__isType", array("object", "CType"), 1, "private");
        $method->addData("$" . "tipo = gettype($" . "object);");
        $method->addData("if(is_array($" . "CType)) {");
        $method->addData("$" . "index = array_search($" . "tipo, $" . "CType);");
        $method->addData("if(!$" . "index) { return false; }");
        $method->addData("} else {");
        $method->addData("if($" . "tipo != $" . "CType) { return false; }");
        $method->addData("}");
        $method->addData("return true;");
        $class->addMethod($method);
//#####
//	METHOD getConditionByName
//#####
        $method = new PHPCodeMethod("getConditionByName", array("conditionName", "columnName", "values"), 1);
        $method->addData("$" . "condition = @$" . "this->conditions[$" . "conditionName];");
        $method->addData("$" . "COperator = $" . "condition[0];");
        $method->addData("$" . "CMulti = $" . "condition[1];");
        $method->addData("$" . "CType = $" . "condition[2];");
        $method->addData("if($" . "condition) {");
        $method->addData("\t$" . "stringCondition = str_replace(\"%f\",$" . "columnName,$" . "COperator);");
        $method->addData("\tif(count($" . "values)>0) {");
        $method->addData("\t\tif($" . "CMulti==true) {");
        $method->addData("\t\t\tforeach($" . "values as $" . "index => $" . "value) {");
        $method->addData("\t\t\t\t$" . "tipo = $" . "this->__isType($" . "value,$" . "CType);");
        $method->addData("\t\t\t\tif(!$" . "tipo) { return false; }");
        $method->addData("\t\t\t\t$" . "stringCondition = str_replace(\"%s\".$" . "index,$" . "value,$" . "stringCondition);");
        $method->addData("\t\t\t}");
        $method->addData("\t\t} else {");
        $method->addData("\t\t\t$" . "tipo = $" . "this->__isType($" . "values,$" . "CType);");
        $method->addData("\t\t\tif(!$" . "tipo) { return false; }");
        $method->addData("\t\t\t$" . "stringCondition = str_replace(\"%s\",$" . "values,$" . "stringCondition);");
        $method->addData("\t\t}");
        $method->addData("\t\treturn $" . "stringCondition;");
        $method->addData("\t} else {");
        $method->addData("\t\treturn false;");
        $method->addData("\t}");
        $method->addData("} else {");
        $method->addData("\treturn false;");
        $method->addData("}");
        $class->addMethod($method);
//echo $class;
        $zipfile->add_file($class->__toString(), "ci/app/modules/RestServer/controllers/KoalaController.class.php");

        $webservice_list = array();
        foreach ($tables as $table) {
            $this->tableObject = $table;
            $pks = $table->GetPK();
            $lowerName = $table->GetLowerName();
            $className = $table->GetUCName();

            /*
              Controller
             */
            
            $Ccontroller = new PHPCodeClass($className . "Controller", array("KoalaController"), array("dirname(__FILE__) .'/KoalaController.class.php'"));
//$Ccontroller->addAttribute("public $"."columns = ".$table->GetTypeColumns().";");
//#####
//	METHOD __construct
//#####
            $Mconstructor = new PHPCodeMethod("__construct", array(), 1);
            $Mconstructor->addData("parent::__construct();");
            $Mconstructor->addData("$" . "this->load->database();");

            $Ccontroller->addMethod($Mconstructor);
//#####
//	METHOD "add".$table->GetUnionName()."_post"
//#####
            $Magregar = new PHPCodeMethod($table->GetUnionName() . "_post", array(), 1);
            $Magregar->addData('$args = @$this->post();');
            $Magregar->addData('if(is_array($args)) {');
            $Magregar->addData("\ttry {");
                //Insert or Update
                $Magregar->addData('if($args["action"]=="update"){');
                    $Magregar->addData('$o = Doctrine::getTable("'.$className.'")->find($args["'.$pks[0].'"]);');
                $Magregar->addData('} else {');
                    $Magregar->addData('$o = new '.$className.'();');
                $Magregar->addData('}');
                //
                $Magregar->addData("\t\tforeach($"."args as $"."key => $"."val) {");
                    $Magregar->addData("\t\t\t$"."exists = @$"."o->columns[$"."key];");
                    $Magregar->addData("\t\t\t".'if($exists) { $o->$key = (($exists=="datetime")? date("Y-m-d",strtotime($val)) :$val); }');
                $Magregar->addData("\t\t}");
                $Magregar->addData("\t\t$"."o->save();");
                $Magregar->addData("\t\t$"."this->response(array('status' => 200,'message' => 'added'), 200);");
                $Magregar->addData("\t} catch(Doctrine_Validator_Exception $"."e) {");
                $Magregar->addData("\t\t$"."records = $"."e->getInvalidRecords();");
                $Magregar->addData("\t\t$"."errors = $"."records[0]->getErrorStack();");
                $Magregar->addData("\t\tforeach($"."errors as $"."k => $"."v) {");
                    $Magregar->addData("\t\t\t$"."err[] = array('name'=>$"."k, 'validate'=>$"."v[0]);");
                $Magregar->addData("\t\t}");
                $Magregar->addData("\t\t$"."this->response(array('status' => 0,'error' => array('validates' => $"."err)), 200);");
                $Magregar->addData("\t} catch(Exception $"."e) {");
                $Magregar->addData("\t\t$"."this->response(array('status' => $"."e->getCode(),'error' => array('message' => $"."e->getMessage())), 200);");
                $Magregar->addData("\t}");
                $Magregar->addData("}");
            $Ccontroller->addMethod($Magregar);

            if (is_array($pks)) {
                /*
                  Set Attrs
                 */
                $ifIsSet = "if(";
                $i = 0;
                foreach ($pks as $pk_name) {
                    $ifIsSet .= "!$" . "this->get('" . $pk_name . "')";
                    if ($i < (count($pks) - 1)) {
                        $ifIsSet .= " && ";
                    }
                    $i++;
                }
                $ifIsSet .= "){ $" . "this->response(array('error' => 'Data could not be found'), 200); }";
            }

//#####
//	METHOD $table->GetUnionName()."_get"
//#####
            $MgetById = new PHPCodeMethod($table->GetUnionName() . "_get", array(), 1);
            $MgetById->addData($ifIsSet);
            if (count($pks) == 1) {
                $MgetById->addData("$" . "data = Doctrine::getTable('" . $table->name_table . "')->find($" . "this->get('" . $pks[0] . "'));");
                $MgetById->addData("if($" . "data) {");
                $MgetById->addData("\t$" . "this->response($" . "data->toArray(), 200);");
                $MgetById->addData("} else {");
                $MgetById->addData("\t$" . "this->response(array('error' => 'Data could not be found'), 200);");
                $MgetById->addData("}");
            } else {
//TODO
//Buscar por varias PK Ids
            }
            $Ccontroller->addMethod($MgetById);

//#####
//	METHOD $table->GetPluralName()."_get"
//#####
            $MgetAll = new PHPCodeMethod($table->GetPluralName()."_get",array(),1);
                $MgetAll->addData("$"."limit = ($"."this->get('limit')) ? $"."this->get('limit') : 10;");
                $MgetAll->addData("$"."offset = ($"."this->get('offset')) ?  $"."this->get('offset') : 0;");
                $MgetAll->addData("$"."colName =  ($"."this->get('colName')) ? $"."this->get('colName') : false;");
                $MgetAll->addData("$"."condition = ($"."this->get('condition')) ? $"."this->get('condition') : false;");
                $MgetAll->addData("$"."compare = ($"."this->get('q')) ? $"."this->get('q') : false;");
                $MgetAll->addData('$table = ($this->get("table")) ? $this->get("table") : false;');
                $MgetAll->addData('$sortname = ($this->get("sortname")) ? $this->get("sortname") : false;');
                $MgetAll->addData('$sortorder = ($this->get("sortorder")) ? $this->get("sortorder") : false;');

                $joinText = '$this->db->select("*")->from("'.$table->name_table.'")';
                //$q = $this->db->select("*")->from("tbl_paciente")->join("tbl_persona","tbl_paciente.RUT = tbl_persona.RUT")->limit($limit)->offset($offset);
                if(is_object($this->tableObject->constraints)){
                        foreach($this->tableObject->constraints->constraints as $constraint) {
                                $joinText .= '->join("'.$constraint->references_table.'","'.$table->name_table.'.'.$constraint->foreign_key_column.' = '.$constraint->references_table.'.'.$constraint->references_column.'")';
                        }
                }
                $MgetAll->addData('$q = '.$joinText.'->limit($limit)->offset($offset);');
                $MgetAll->addData('if($sortname && $sortorder){ $q->order_by($sortname, $sortorder); }');
                $MgetAll->addData("if($"."colName && $"."condition && $"."compare) {");
                        $MgetAll->addData("\t$"."fullCondition = $"."this->getConditionByName($"."condition,$"."colName,$"."compare);");
                        $MgetAll->addData("\tif($"."fullCondition) {");
                                $MgetAll->addData("\t\t$"."q->where($"."fullCondition);");
                                $MgetAll->addData("\t\t$"."data = $"."q->get()->result();");
                                $MgetAll->addData($joinText.';');
                                $MgetAll->addData('$total = $this->db->count_all_results();');
                                $MgetAll->addData("\t\tif($"."data) {");
                                        //
                                        $MgetAll->addData('if($table){');
                                        $MgetAll->addData('$result = array();');
                                        $MgetAll->addData('foreach($data as $d){');
                                                $MgetAll->addData('$values = array();');
                                                $MgetAll->addData('foreach($d as $key =>$value){ $values[] = $value; }');
                                                $MgetAll->addData('$result[] = array("id" => $d->RUT,"cell"=> array_values($values));');
                                        $MgetAll->addData('}');
                                                $MgetAll->addData('$this->response(array("page"=>1,"total"=>$total,"rows"=>$result), 200);');
                                        $MgetAll->addData('} else {');
                                                $MgetAll->addData('$this->response($data, 200);');
                                        $MgetAll->addData('}');
                                        //
                                $MgetAll->addData("\t\t} else {");
                                        $MgetAll->addData("\t\t\t$"."this->response(array('error' => 'Data could not be found'), 200);");
                                $MgetAll->addData("\t\t}");
                        $MgetAll->addData("\t}");
                $MgetAll->addData("}");
                $MgetAll->addData("$"."data = $"."q->get()->result();");
                $MgetAll->addData($joinText.';');
                $MgetAll->addData('$total = $this->db->count_all_results();');

                $MgetAll->addData("if($"."data) {");
                        //
                        $MgetAll->addData('if($table){');
                        $MgetAll->addData('$result = array();');
                        $MgetAll->addData('foreach($data as $d){');
                                $MgetAll->addData('$values = array();');
                                $MgetAll->addData('foreach($d as $key =>$value){ $values[] = $value; }');
                                $MgetAll->addData('$result[] = array("id" => $d->RUT,"cell"=> array_values($values));');
                        $MgetAll->addData('}');
                                $MgetAll->addData('$this->response(array("page"=>1,"total"=>$total,"rows"=>$result), 200);');
                        $MgetAll->addData('} else {');
                                $MgetAll->addData('$this->response($data, 200);');
                        $MgetAll->addData('}');
                        //
                $MgetAll->addData("} else {");
                        $MgetAll->addData("\t$"."this->response(array('error' => 'Data could not be found'), 200);");
                $MgetAll->addData("}");
            $Ccontroller->addMethod($MgetAll);
            
            $webservice_list[] = array(
                $className . "Controller" => array(
                "addupdate"=>array(
                    "method"=>"POST",
                    "path"=> $className . "Controller/".$table->GetUnionName()
                ),
                "single"=>array(
                    "method"=>"GET",
                    "path"=> $className . "Controller/".$table->GetUnionName().".{FORMAT}?".$pks[0]."={PARAMETER}"
                ),
                "list"=>array(
                    "method"=>"GET",
                    "path"=> $className . "Controller/".$table->GetPluralName().".{FORMAT}"
                )
            ));
            $zipfile->add_file($Ccontroller->__toString(), "ci/app/modules/RestServer/controllers/" . $className . "Controller.php");
//echo $Ccontroller;

            /*
              Class
             */
            $class = new PHPCodeClass($className);
            $class->addAttribute("public $" . "columns = " . $this->tableObject->GetTypeColumns() . ";");
            $TableDefinition = new PHPCodeMethod("setTableDefinition", array(), 1);
            $TableDefinition->addData("$" . "this->setTableName('" . $this->tableObject->name_table . "');");
            foreach ($this->tableObject->columns_table as $column) {
                $phpType = $this->getDataType($column->type_column);
                if (is_array($phpType)) {
                    $extras = array();
                    $hasColumn = "$" . "this->hasColumn('" . $column->name_column . "', '" . $phpType['column_type'] . "'";
                    if ($phpType['column_maxSize']) {
                        $hasColumn .= "," . $phpType['column_maxSize'];
                        $extras[] = "'length' => '" . $phpType['column_maxSize'] . "'";
                    } else {
                        $hasColumn .= ",null";
                    }
                    if ($column->isPrimaryKey_column) {
                        $extras[] = "'primary' => true";
                    }
                    if (isset($column->default_column)) {
                        $extras[] = "'default' => '" . $column->default_column . "'";
                    }
                    if (isset($column->null_column) && ($column->null_column == "NO")) {
                        $extras[] = "'notnull' => true";
                    } else {
                        $extras[] = "'notnull' => false";
                    }
                    $extras[] = "'type' => '" . $phpType['column_type'] . "'";
                    $extras[] = "'unsigned' => false";
                    $txt = ", array(" . join(",\r\n\t\t\t", $extras) . "\r\n\t\t)";
                    if ($txt != ", array()") {
                        $hasColumn .= $txt;
                    }
                    $hasColumn .= ");";
                    $TableDefinition->addData($hasColumn);
                }
            }

            $class->addMethod($TableDefinition);
            $setUp = new PHPCodeMethod("setUp", array(), 1);
            if (is_object($this->tableObject->constraints)) {
                foreach ($this->tableObject->constraints->constraints as $constraint) {
                    $referencesClassName = ucwords(strtolower($constraint->references_table));
                    $setUp->addData("$" . "this->hasOne('" . $referencesClassName . " as " . $referencesClassName . "', array(");
                    $setUp->addData("\t\t'local' => '" . $constraint->foreign_key_column . "',");
                    $setUp->addData("\t\t'foreign' => '" . $constraint->references_column . "'");
                    $setUp->addData("\t)");
                    $setUp->addData(");");
                }
            }
            foreach ($tables as $tbl) {
                if ($tbl->constraints != null) {
                    foreach ($tbl->constraints->constraints as $constraint) {
                        $tblName = ucwords(strtolower($tbl->name_table));
                        if ($constraint->references_table == $table->name_table) {
                            $referencesClassName = ucwords(strtolower($constraint->references_table));
                            $setUp->addData("$" . "this->hasMany('" . $tblName . " as " . $tblName . "', array(");
                            $setUp->addData("\t\t'local' => '" . $constraint->foreign_key_column . "',");
                            $setUp->addData("\t\t'foreign' => '" . $constraint->references_column . "'");
                            $setUp->addData("\t)");
                            $setUp->addData(");");
                        }
                    }
                }
            }
            $class->addMethod($setUp);
//echo $class->__toString();
            $zipfile->add_file($class->__toString(), "ci/app/modules/RestServer/models/" . $className . ".php");
        }
        /* Agrega archivo json con todos los webservices Rest Disponibles */
        $zipfile->add_file(json_encode($webservice_list), "ci/app/modules/RestServer/controllers/list.json");
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=PHPClassCreator-" . time() . ".zip");
        echo $zipfile->file();
    }

    public function __construct($tables, $debug=false) {
        $this->debug = $debug;
        if ($this->debug) {
            foreach ($tables as $table) {
                $this->tableObject = $table;
                $className = ucwords(strtolower($this->tableObject->name_table));

#####################################################################################
##	INICIO CLASS
                $this->modelClassDefinition[$this->tableObject->name_table]['class']['name'] = $className;
//$this->modelClassDefinition[$this->tableObject->name_table]['class'][] = "class ".$className." extends Doctrine_Record {";
#################################################################################
##	INICIO METHOD TABLEDEFINITION
                $this->tableDefinition = array();
//$this->tableDefinition[] = "public function setTableDefinition() {";
                $definition = array();
                $definition[] = "$" . "this->setTableName('" . $this->tableObject->name_table . "');";

                foreach ($this->tableObject->columns_table as $column) {
                    $phpType = $this->getDataType($column->type_column);
                    if (is_array($phpType)) {
                        $extras = array();
                        $hasColumn = "$" . "this->hasColumn('" . $column->name_column . "', '" . $phpType['column_type'] . "'";
                        if ($phpType['column_maxSize']) {
                            $hasColumn .= "," . $phpType['column_maxSize'];
                            $extras[] = "'length' => '" . $phpType['column_maxSize'] . "'";
                        }
                        if ($column->isPrimaryKey_column) {
                            $extras[] = "'primary' => true";
                        }
                        if (isset($column->default_column)) {
                            $extras[] = "'default' => '" . $column->default_column . "'";
                        }
                        if (isset($column->null_column) && ($column->null_column == "NO")) {
                            $extras[] = "'notnull' => true";
                        }
                        $extras[] = "'type' => '" . $phpType['column_type'] . "'";
                        $extras[] = "'unsigned' => false";
                        $txt = ", array(" . join(",\r\n\t\t\t", $extras) . "\r\n\t\t)";
                        if ($txt != ", array()") {
                            $hasColumn .= $txt;
                        }
                        $hasColumn .= ");";
                        $definition[] = $hasColumn;
                    }
                }
                $this->tableDefinition = $definition;
//$this->tableDefinition[] = "} ";
                $this->modelClassDefinition[$this->tableObject->name_table]['class']['method']['setTableDefinition'] = $this->tableDefinition;
##	FIN METHOD TABLEDEFINITION
#################################################################################
##	INICIO METHOD SETUP
                if ($table->constraints != null) {
                    $this->constraintFK = array();
//$this->constraintFK['method'][] = "public function setUp() {";

                    $setup = array();
                    foreach ($this->tableObject->constraints->constraints as $constraint) {
                        $referencesClassName = ucwords(strtolower($constraint->references_table));
                        $setup[] = "$" . "this->hasOne('" . $referencesClassName . " as " . $referencesClassName . "', array(";
                        $setup[] = "\t\t'local' => '" . $constraint->foreign_key_column . "',";
                        $setup[] = "\t\t'foreign' => '" . $constraint->references_column . "'";
                        $setup[] = "\t)";
                        $setup[] = ");";
                    }
                    $this->constraintFK['fk_' . $constraint->references_table . '_' . $constraint->references_column] = $setup;
//$this->constraintFK['method'][] = "}";
                    $this->modelClassDefinition[$this->tableObject->name_table]['class']['method']['setUp'] = $this->constraintFK;
                }
## FIN METHOD SETUP
##	FIN CLASS
//$this->modelClassDefinition[$this->tableObject->name_table]['class'][] = "}";
            }

            foreach ($tables as $table) {
                if ($table->constraints != null) {
                    $i = 0;
                    foreach ($table->constraints->constraints as $constraint) {
                        $tblName = ucwords(strtolower($table->name_table));
                        $referencesClassName = ucwords(strtolower($constraint->references_table));
                        $setup = array();
                        $setup[] = "$" . "this->hasMany('" . $tblName . " as " . $tblName . "', array(";
                        $setup[] = "\t\t'local' => '" . $constraint->foreign_key_column . "',";
                        $setup[] = "\t\t'foreign' => '" . $constraint->references_column . "'";
                        $setup[] = "\t)";
                        $setup[] = ");";
                        $this->modelClassDefinition[$constraint->references_table]['class']['method']['setUp']['fk_' . $table->name_table . '_' . $constraint->references_column] = $setup;
                    }
                }
            }


//print_r($this->modelClassDefinition);
        }
    }

    public function getDataType($data) {
        preg_match_all("#(.[a-zA-Z]+)\((\d+)\)#", $data, $matches);
        if ((count($matches) == 3) || (count($matches) == 2)) {
            $key = (empty($matches[1][0])) ? $data : $matches[1][0];
            if (array_key_exists($key, $this->MYSQL_DATA_TYPES)) {
                return array('column_type' => $this->DOCTRINE_DATA_TYPES[$this->MYSQL_DATA_TYPES[$key]],
                    'column_maxSize' => ((count($matches) == 3 && !empty($matches[2][0])) ? $matches[2][0] : null)
                );
            }
        }
        return false;
    }

    public function Fix() {

        echo $this->__toString();
    }

    public function getCode($arr) {
        $ModelPHPCode = "";
        $ControllerPHPCode = "";
        $zipfile = new ZipFile();
        foreach ($arr as $k => $v) {
## MODEL Class
            $ModelPHPCode = "";
            $ModelPHPCode .= "<" . "?php\r\n";
            $ModelPHPCode .= "/*\r\n\t@PHPClassCreator [Doctrine + CodeIgniter]\r\n\t@Autor: Esteban Fuentealba\r\n\t@Email:	mi [dot] warezx [at] gmail [dot] com\r\n*/\r\n";
            $ModelPHPCode .= "// models/" . $v['class']['name'] . ".php\r\n";
            $ModelPHPCode .= "class " . $v['class']['name'] . " extends Doctrine_Record {\r\n";
## setTableDefinition
            $ModelPHPCode .="\tpublic function setTableDefinition() {\r\n";
            foreach ($v['class']['method']['setTableDefinition'] as $index => $valor) {
                $ModelPHPCode .="\t\t" . $valor . "\r\n";
            }
            $ModelPHPCode .="\t}\r\n";
## setUp
            $ModelPHPCode .="\tpublic function setUp() {\r\n";
            foreach ($v['class']['method']['setUp'] as $index => $valor) {
                foreach ($valor as $key => $val) {
                    $ModelPHPCode .="\t\t" . $val . "\r\n";
                }
            }
            $ModelPHPCode .="\t}\r\n";

            $ModelPHPCode .= "}\r\n";
            if ($this->debug) {
                echo $ModelPHPCode;
            } else {
                $zipfile->add_file($ModelPHPCode, "ci/app/modules/RestServer/models/" . $v['class']['name'] . ".php");
            }
## FIN CLASS MODEL
## CONTROLLER MODEL
            $ControllerPHPCode = "";
            $ControllerPHPCode .= "<" . "?php\r\n";
            $ControllerPHPCode .= "/*\r\n\t@PHPClassCreator [Doctrine + CodeIgniter]\r\n\t@Autor: Esteban Fuentealba\r\n\t@Email:	mi [dot] warezx [at] gmail [dot] com\r\n*/\r\n";
            $ControllerPHPCode .= "// system/application/controllers/" . $v['class']['name'] . "Controller.php\r\n";
            $ControllerPHPCode .= "require_once(dirname(__FILE__) .'/KoalaController.class.php');\r\n";
            $ControllerPHPCode .= "class " . $v['class']['name'] . "Controller extends KoalaController {\r\n";
## CONSTRUCTOR
            $ControllerPHPCode .="\tpublic function __construct() {\r\n";
            $ControllerPHPCode .="\t\tparent::Controller();\r\n";
            $ControllerPHPCode .="\t\tparse_str($" . "_SERVER['QUERY_STRING'],$" . "_GET);\r\n";
            $ControllerPHPCode .="\t}\r\n";
## METHOD INSERT
            $ControllerPHPCode .="\tpublic function Insert() {\r\n";
            $ControllerPHPCode .="\t\t$" . "clean = $" . "this->input->xss_clean($" . "_GET);\r\n";
            $ControllerPHPCode .="\t\t$" . "c = new " . $v['class']['name'] . "();\r\n";
            $ControllerPHPCode .="\t\ttry {\r\n";
            $ControllerPHPCode .="\t\t\tforeach($" . "clean as $" . "param => $" . "value) {\r\n";
            $ControllerPHPCode .="\t\t\t\t$" . "c->$" . "param = isset($" . "value) ? $" . "value : null;\r\n";
            $ControllerPHPCode .="\t\t\t}\r\n";
            $ControllerPHPCode .="\t\t} catch(Doctrine_Record_UnknownPropertyException $" . "e) {\r\n";
            $ControllerPHPCode .="\t\t\t//xD\r\n";
            $ControllerPHPCode .="\t\t}\r\n";
            $ControllerPHPCode .="\t\ttry {\r\n";
            $ControllerPHPCode .="\t\t\tforeach($" . "clean as $" . "param => $" . "value) {\r\n";
            $ControllerPHPCode .="\t\t\t\t$" . "c->$" . "param = isset($" . "value) ? $" . "value : null;\r\n";
            $ControllerPHPCode .="\t\t\t}\r\n";
            $ControllerPHPCode .="\t\t} catch(Doctrine_Record_UnknownPropertyException $" . "e) {\r\n";
            $ControllerPHPCode .="\t\t\t//xD\r\n";
            $ControllerPHPCode .="\t\t}\r\n";
            $ControllerPHPCode .="\t}\r\n";
            $ControllerPHPCode .= "}\r\n";
## FIN CONTROLLER MODEL
            if ($this->debug) {
                echo $ControllerPHPCode;
            } else {
                $zipfile->add_file($ControllerPHPCode, "app/controllers/" . $v['class']['name'] . "Controller.php");
            }
        }
        if (!$this->debug) {
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=PHPClassCreator-" . time() . ".zip");
            echo $zipfile->file();
        }
    }

    public function dir_tree($dir) {
        $path = '';
        $stack[] = $dir;
        while ($stack) {
            $thisdir = array_pop($stack);
            if ($dircont = scandir($thisdir)) {
                $i = 0;
                while (isset($dircont[$i])) {
                    if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                        $current_file = "{$thisdir}/{$dircont[$i]}";
                        if (is_file($current_file)) {
                            $path[] = "{$thisdir}/{$dircont[$i]}";
                        } elseif (is_dir($current_file)) {
                            $path[] = "{$thisdir}/{$dircont[$i]}";
                            $stack[] = $current_file;
                        }
                    }
                    $i++;
                }
            }
        }
        return $path;
    }

    public function __toString() {
        return $this->getCode($this->modelClassDefinition);
    }

}

?>