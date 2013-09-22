<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");


//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);


//get the right subModule
if($_POST['subModuleName'] == NULL){$subModule = $module;}
else{$subModule = $module->getSubModule($_POST['subModuleName']);}

//get the right instance
if($_POST['instanceName'] == "Add new"){    //we are adding a new subModule instance
    
    $instance = new Instance("new_subModule", NULL, $subModule);
    $subModule->addInstance($instance);
    
}else{    //if we are editing a submodul
    
    $instance = $subModule->getInstance($_POST['instanceName']);
}



foreach($_POST as $key => $value){
    
    if( ($key != 'moduleName') && ($key != 'subModuleName') && ($key != 'instanceName') ){
        
        $instance->setArgument($key, $value);   
    }
}

writeConfigFile($config);

?>
