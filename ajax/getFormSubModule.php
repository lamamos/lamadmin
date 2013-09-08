<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


//we have two type of requests : general for the mainModule configuration, and subModule for the config of the submodule or the instances
if($_POST['subModuleName'] == "general"){	//we need to generate the config of the mainModule


	//TODO : we consider that a module is loaded only once, it may be false, need to check that in the config file
	$instance = $module->getInstances()[0];


	$response = "salut il y a : ".count($instance->getArguments())." arguments.<br>";
	$response .= "<form onsubmit=\"return false;\" method=\"post\">";

	foreach($instance->getArguments() as $argument){

		$response .= $argument[0]." : <input type=\"text\" name=\"".$argument[0]."\" value=\"".$argument[1]."\"><br>";
	}
	$response .= "<input type=\"submit\" value=\"Save\">";
	$response .= "</form>";


}else{		//we need to generate the config of the submodule or it's instances

	$subModule = $module->getSubModule($_POST['subModuleName']);


	$response = "<select name=\"select".$_POST['subModuleName']."\" class=\"instanceSelector\" size=\"10\">";

	foreach($subModule->getInstances() as $instance){

		$response .= "<option>".$instance->getName()."</option>";
	}
	$response .= "<option>Add new</option>";
	$response .= "</select>";
}


echo $response;


?>
