<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$tabs = "";
foreach($config->getAvalableModules() as $module){
    if(!preg_match("/".$module->getName()."/", "user")){

        $tabs .= $module->getName();
        
        if($module->isActivated()){$tabs .= ";1";}else{$tabs .= ";0";}
        
        $tabs .= ",";
    }
}
$tabs = substr_replace($tabs ,"",-1);

echo $tabs;

?>