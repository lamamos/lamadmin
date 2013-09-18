<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule("user");

if(!($_POST['name'] === "Add new")){$instance = $module->getInstance($_POST['name']);}

$response = "salut il y a : ".count($module->getArguments())." arguments.<br>";
$response .= "<form class=\"userForm\" onsubmit=\"return false;\" method=\"post\">";

if($_POST['name'] === "Add new"){

    foreach($module->getArguments() as $argument){
    
        $response .= $argument." : <input type=\"text\" name=\"".$argument."\" value=\"\"><br>";
    }
    $response .= "<input type=\"submit\" value=\"Save\">";

}else{
    
    foreach($module->getArguments() as $argument){
    
        $response .= $argument." : <input type=\"text\" name=\"".$argument."\" value=\"".$instance->getArgument($argument)."\"><br>";
    }
    $response .= "<input type=\"submit\" value=\"Save\">";
    $response .= "<input class=\"deleteInstance\" type=\"button\" value=\"Delete\">";
}

$response .= "</form>";



echo $response;

?>
