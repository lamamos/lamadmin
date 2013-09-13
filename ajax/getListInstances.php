<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);
$subModule = $module->getSubModule($_POST['subModuleName']);

foreach($subModule->getInstances() as $instance){

	$response .= $instance->getName().",";
}

$response = substr($response, 0, -1);

echo $response;

?>
