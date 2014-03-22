<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


//we have two type of requests : general for the mainModule configuration, and subModule for the config of the submodule or the instances
if($_POST['subModuleName'] == "general"){	//we need to generate the config of the mainModule

    if($module->isActivated()){
        
        //We are considering the the Mainmodule may only be loaded instanciated once.
        $instance = $module->getInstances()[0];
        $response = $instance->toForm();
    }


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
