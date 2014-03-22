<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


if($_POST['instanceName'] == "Add new"){    //if we are creating a new instance
    
    if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] != NULL) )$subModule = $module->getSubModule($_POST['subModuleName']);
    else $subModule = $module;
    
	$response = $subModule->toJson();

    
}else{  //if we are editing an existing instance
    
    if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] == "general") ){   //if we are editing mainModule with on ly one instance
    
        $subModule = $module;	//This line may be useless
        $instance = $module->getInstances()[0];
        
    }else if( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "") ){  //if we are editing a mainModule with multiple instances (user for exemple)
        
        $subModule = $module;	//This line may be useless
        $instance = $module->getInstance($_POST['instanceName']);
        
    }else{  //if we are editing a subModule
        
        $subModule = $module->getSubModule($_POST['subModuleName']);
        $instance = $subModule->getInstance($_POST['instanceName']);
    }
    
	$response = $instance->toJson();
}

echo $response;
return;

?>
