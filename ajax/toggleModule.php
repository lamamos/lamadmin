<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

$module = $config->getModule($_POST['moduleToggled']);

if($module->isActivated()){
    
    $module->clearInstances();
    foreach($module->getSubModules() as $submodule)$submodule->clearInstances();
}else{
    
    $module->addInstance(new Instance($_POST['moduleToggled'], NULL, $module));
}

$config->writeConfigFile($config);

?>
