<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

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

//$response = "on a ".count($_POST['values'])." arguments :\n\n";

foreach($_POST['values'] as $field){

  //$response .= $field['title']." = ".$field['value'].",\n";

  if($field['value'] == "false") $instance->setArgument($field['title'], false);
  else if($field['value'] == "true") $instance->setArgument($field['title'], true);
	else $instance->setArgument($field['title'], $field['value']);
}


//echo $response;

$config->writeConfigFile($config);

?>
