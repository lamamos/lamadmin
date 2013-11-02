<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");


//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);


//get the right subModule
if( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "general") || ($_POST['subModuleName'] == "") ){$subModule = $module;}
else{$subModule = $module->getSubModule($_POST['subModuleName']);}

//get the right instance
if($_POST['instanceName'] == "Add new"){    //we are adding a new subModule instance
    
    $instance = new Instance("new_subModule", NULL, $subModule);
    $subModule->addInstance($instance);
    
}elseif( $_POST['subModuleName'] == "general" ){

    $instance = $subModule->getInstances()[0];
    
}else{    //if we are editing a submodul
    
    $instance = $subModule->getInstance($_POST['instanceName']);
}


foreach($_POST['values'] as $field){

	$instance->setArgument($field['title'], $field['value']);
}

$config->writeConfigFile($config);

?>
