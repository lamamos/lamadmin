<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

//we search for the module that we are asked by POST

$module = $config->getModule($_POST['name']);



$tabs = "general";

//we generate the list of the submodules for this module
foreach($module->getSubModules() as $submodule){

	$tabs .= ",".$submodule->getName();
}


echo $tabs;


?>
