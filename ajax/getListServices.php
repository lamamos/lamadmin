<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();

$tabs = "[";
foreach($config->getAvalableModules() as $module){
    if(!preg_match("/".$module->getName()."/", "user")){

        $tabs .= "{\"name\":\"".$module->getName()."\", \"activated\":";
        
        if($module->isActivated()){$tabs .= "true";}else{$tabs .= "false";}
        
        $tabs .= "},";
    }
}
$tabs = substr_replace($tabs ,"",-1);
$tabs .= "]";

echo $tabs;

?>
