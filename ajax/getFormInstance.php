<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);
$subModule = $module->getSubModule($_POST['subModuleName']);
$instance = $subModule->getInstance($_POST['instanceName']);


$response = "salut il y a : ".count($instance->getArguments())." arguments.<br>";
$response .= "<form onsubmit=\"return false;\" method=\"post\">";

foreach($instance->getArguments() as $argument){

// First name: <input type="text" name="firstname"><br>

	$response .= $argument[0]." : <input type=\"text\" name=\"".$argument[0]."\" value=\"".$argument[1]."\"><br>";

}
$response .= "<input type=\"submit\" value=\"Save\">";
$response .= "</form>";

echo $response;


?>
