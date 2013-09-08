<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule("user");

$instance = $module->getInstance($_POST['name']);



foreach($_POST as $key => $value){
    
    $instance->setArgument($key, $value);
}

writeConfigFile($config);

?>
