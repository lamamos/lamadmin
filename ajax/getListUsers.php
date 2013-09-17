<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();
$module = $config->getModule("user");

$tabs = "";
foreach($module->getInstances() as $user){

    $tabs .= $user->getName().",";
}
$tabs = substr_replace($tabs ,"",-1);

echo $tabs;

?>