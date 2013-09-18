<?
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$response = "<ul class=\"menu\">";

foreach($config->getModules() as $module){
    
    $response .= "<li><a href\"javascript:void(0)\">".$module->getName()."</a>";
    
    if(count($module->getSubModules()) > 0){
        $response .= "<ul>";
        foreach($module->getSubModules() as $subModule){
            
            $response .= getSubModuleMenu($subModule, $module->getName());
        }
        $response .= "</ul>";
    }
    
    $response .= "</li>";

}

$response .= "</ul>";

echo $response;








function getSubModuleMenu($subModule, $moduleName){
    
    $menu = "";
    $menu .= "<li><a href\"javascript:void(0)\">".$subModule->getName()."</a>";
    
    if(count($subModule->getInstances()) > 0){
        $menu .= "<ul>";
        foreach($subModule->getInstances() as $instance){
            
            $instanceName = $moduleName."::".$subModule->getName()."::".$instance->getName();
            $menu .= "<li><a href\"javascript:void(0)\" onclick=\"formatInstanceMenu('".$instanceName."');\">".$instance->getName()."</a></li>";
        }
        $menu .= "</ul>";
    }

    $menu .= "</li>";
    
    return $menu;
}



?>