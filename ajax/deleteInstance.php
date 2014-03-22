<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

/*
                activeModule: activeModule,
                activeSubModule: activeSubModule,
                instanceName: instanceName,*/

if($_POST['moduleName'] === "user"){
    
    $module = $config->getModule($_POST['moduleName']);    
    $module->deleteInstance($_POST['instanceName']); 
    
}else{
        
    $module = $config->getModule($_POST['moduleName']);
    
    $subModule = $module->getSubModule($_POST['subModuleName']);
    
    $subModule->deleteInstance($_POST['instanceName']);      
}



$config->writeConfigFile($config);

?>
