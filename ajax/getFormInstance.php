<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);


if($_POST['instanceName'] == "Add new"){    //if we are creating a new instance
    
    $subModule = $module->getSubModule($_POST['subModuleName']);

    $response = "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
    $response .= "salut il y a : ".count($subModule->getArguments())." arguments.<br>";

    foreach($subModule->getArguments() as $argument){
    
        $response .= $argument." : <input type=\"text\" name=\"".$argument."\" value=\"\"><br>";    
    }
    $response .= "<input type=\"submit\" value=\"Save\">";
    $response .= "</form>";
    
    echo $response;
    return;
    
}else{  //if we are editing an existing instance
    
    if($_POST['subModuleName'] == "general"){   //if we are editing the mainModule
    
        $subModule = $module;
        $instance = $module->getInstances()[0];        
    }else{  //if we are editing a subModule
        
        $subModule = $module->getSubModule($_POST['subModuleName']);
        $instance = $subModule->getInstance($_POST['instanceName']);
    }
    
    
    $response = "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
    $response .= "salut il y a : ".count($subModule->getArguments())." arguments.<br>";

    foreach($subModule->getArguments() as $argument){
    
        $response .= $argument." : <input type=\"text\" name=\"".$argument."\" value=\"".$instance->getArgument($argument)."\"><br>";    
    }
    
    $response .= "<input type=\"submit\" value=\"Save\">";
    $response .= "</form>";
    
    echo $response;
    return;
}

?>
