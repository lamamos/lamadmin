<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");


//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);


//get the right subModule
if( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "") ){$subModule = $module;}
else{$subModule = $module->getSubModule($_POST['subModuleName']);}

//get the right instance
if($_POST['instanceName'] == "Add new"){    //we are adding a new subModule instance
    
    $instance = new Instance("new_subModule", NULL, $subModule);
    $subModule->addInstance($instance);
    
}elseif( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "") ){
    
    $instance = $subModule->getInstances()[0];
    
}else{    //if we are editing a submodul
    
    $instance = $subModule->getInstance($_POST['instanceName']);
}


echo "kikooo";

//need to associate the key values (!!!arrays and hash!!!)


/*foreach($_POST as $key => $value){
    
    if( ($key != 'moduleName') && ($key != 'subModuleName') && ($key != 'instanceName') ){
        
        $instance->setArgument($key, $value);   
    }
}*/

//$config->writeConfigFile($config);

?>
