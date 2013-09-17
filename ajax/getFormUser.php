<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule("user");

$instance = $module->getInstance($_POST['name']);



$response = "salut il y a : ".count($instance->getArguments())." arguments.<br>";
$response .= "<form class=\"userForm\" onsubmit=\"return false;\" method=\"post\">";

foreach($instance->getArguments() as $argument){

	$response .= $argument[0]." : <input type=\"text\" name=\"".$argument[0]."\" value=\"".$argument[1]."\"><br>";
}
$response .= "<input type=\"submit\" value=\"Save\">";
$response .= "<input class=\"deleteInstance\" type=\"button\" value=\"Delete\">";
$response .= "</form>";



echo $response;

?>
