<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


if($_POST['instanceName'] == "Add new"){    //if we are creating a new instance
    
    $subModule = $module->getSubModule($_POST['subModuleName']);

    $response = "salut il y a : ".count($subModule->getArguments())." arguments.<br>";
    $response .= "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
    
    foreach($subModule->getArguments() as $argument){
    
        $response .= $argument." : <input type=\"text\" name=\"".$argument."\" value=\"\"><br>";    
    }
    $response .= "<input type=\"submit\" value=\"Save\">";
    $response .= "</form>";
    
    echo $response;
    return;
    
}else{  //if we are editing an existing instance
    
    if($_POST['subModuleName'] == "general"){
    
        $instance = $module->getInstances()[0];        
    }else{
        
        $subModule = $module->getSubModule($_POST['subModuleName']);
        $instance = $subModule->getInstance($_POST['instanceName']);
    }
    
    
    $response = "salut il y a : ".count($instance->getArguments())." arguments.<br>";
    $response .= "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
    
    foreach($instance->getArguments() as $argument){
    
        $response .= $argument[0]." : <input type=\"text\" name=\"".$argument[0]."\" value=\"".$argument[1]."\"><br>";    
    }
    $response .= "<input type=\"submit\" value=\"Save\">";
    $response .= "</form>";
    
    echo $response;
    return;
}

?>
