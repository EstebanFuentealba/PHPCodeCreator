<?PHP

class DBColumn {

    public function __construct($rowData) {
        $this->name_column = $rowData['Field'];
        $this->type_column = $rowData['Type'];
        $this->null_column = $rowData['Null'];
        $this->isForeignKey_column = ($rowData['Key'] == "MUL");
        $this->isPrimaryKey_column = ($rowData['Key'] == "PRI");
        $this->default_column = $rowData['Default'];
        $this->extra_column = $rowData['Extra'];
    }

    public function __toString() {
        
    }

}

?>