<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();


if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] != NULL) ){
        
    $module = $config->getModule($_POST['moduleName']);
    $subject = $module->getSubModule($_POST['subModuleName']);
}else{

    $subject = $config->getModule($_POST['moduleName']);
}

$response = "";

foreach($subject->getInstances() as $instance){

	$response .= $instance->getName().",";
}

$response = substr($response, 0, -1);

echo $response;

?>
