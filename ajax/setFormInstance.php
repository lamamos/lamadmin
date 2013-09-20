<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");


//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);


if($_POST['subModuleName'] == NULL){    //we are editing a mainModule instance
    
    if($_POST['instanceName'] == NULL){$instance = $module->getInstances()[0];}
    else{$instance = $module->getInstance($_POST['instanceName']);}
    
}elseif($_POST['instanceName'] == "Add new"){    //we are adding a new subModule instance
    
    $subModule = $module->getSubModule($_POST['subModuleName']);
    $subModule->addInstance(new Instance("new_subModule", NULL));
    $instance = $subModule->getInstance("new_subModule");
    
}else{    //if we are editing a submodul
    
    $subModule = $module->getSubModule($_POST['subModuleName']);
    $instance = $subModule->getInstance($_POST['instanceName']);
}


foreach($_POST as $key => $value){
    
    if( ($key != 'moduleName') && ($key != 'subModuleName') && ($key != 'instanceName') ){
        
        $instance->setArgument($key, $value);   
    }
}

writeConfigFile($config);

?>
