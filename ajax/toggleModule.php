<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleToggled']);

if($module->isActivated()){
    
    $module->clearInstances();
    foreach($module->getSubModules() as $submodule)$submodule->clearInstances();
}else{
    
    $module->addInstance(new Instance($_POST['moduleToggled'], NULL, $module));
}

writeConfigFile($config);

?>
