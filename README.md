<a href="https://docs.google.com/document/d/1OPrBc1271HOu3awjsSPtrfxCQtpiFT6p4DQOtClFSQI/edit?hl=es" target="_blank"><b>TODO</b></a>

<b>Configuration:</b><br>
File: <b>config.inc.php</b><br>
define('DB_SERVER', "");<br>
define('DB_USER', "");<br>
define('DB_PASS', "");<br>
define('DB_DATABASE', "");<br>
define("APPDIR",dirname(__FILE__));<br>

<b>DB_SERVER</b>   = Database server   Ex.: localhost<br>
<b>DB_USER</b>     = User database server Ex.: root<br>
<b>DB_PASS</b>     = Password user database server Ex.: ******<br>
<b>DB_DATABASE</b> = Database name Ex.: geriatria<br>


<b>How to use:</b><br>
Examples:<br>
<a href="#">http://localhost/PHPClassCreator/public_html/index.php/RestServer/Tbl_pacienteController/tblsPacientes.xml?condition=contains&colName=tbl_persona.nombres&q=Es&limit=5</a><br>
<a href="#">http://localhost/PHPClassCreator/public_html/index.php/RestServer/Tbl_pacienteController/tblsPacientes.json?condition=contains&colName=tbl_persona.nombres&q=Es&limit=5</a><br>
<a href="#">http://localhost/PHPClassCreator/public_html/index.php/RestServer/Tbl_pacienteController/tblsPacientes.jsonp?condition=contains&colName=tbl_persona.nombres&q=Es&limit=5&callback=test</a><br>
<b>URL Project</b> / <b> Modules </b> /<b>Controllers</b> / <b>Function</b> . <b>Type</b> ? <b>Params</b>