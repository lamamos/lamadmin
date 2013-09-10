<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");


//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


if($_POST['subModuleName'] == NULL){
    
    $module = $config->getModule($_POST['moduleName']);
    $instance = $module->getInstances()[0];

}else{    //if we are editing a submodul
    
    $module = $config->getModule($_POST['moduleName']);
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
