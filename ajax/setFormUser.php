<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule("user");

$instance = $module->getInstance($_POST['name']);


if($instance != NULL){  //if the user exist, we change it's arguments

    foreach($_POST as $key => $value){
        
        $instance->setArgument($key, $value);
    }
}else{  //if it doesn't existe, we create it
    
    foreach($_POST as $key => $value){
        
        $variables[] = [$key, $value];
    }
    $module->addInstance(new Instance($name, $variables));
}

writeConfigFile($config);

?>
