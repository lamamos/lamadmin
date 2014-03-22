<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


//we have two type of requests : general for the mainModule configuration, and subModule for the config of the submodule or the instances
if($_POST['subModuleName'] == "general"){
        
    $instance = $module->getInstances()[0];

}else{		//we need to generate the config of the submodule or it's instances

	$subModule = $module->getSubModule($_POST['subModuleName']);
    $instance = $subModule->getInstance($_POST['instanceName']);
}

$array = $instance->getArgumentObject($_POST['arrayName']);

$array->removeElementNum($_POST['argmentNumber']);


$config->writeConfigFile($config);


?>
