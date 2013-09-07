<?
session_start();
include_once("configuration.php");

function getListOfAvalableModules(){

    global $PathToRexConfiguration;
        
    $modules = scandir($PathToRexConfiguration."/lib/Service/");
    $modules = array_slice($modules, 2);
    
    return $modules;
}

function getListOfAvalableSubModules($moduleName){
    
    global $PathToRexConfiguration;
    
    $subModules = scandir($PathToRexConfiguration."/lib/Service/".$moduleName);
    $subModules = array_slice($subModules, 2);
    
    foreach($subModules as $tmp){
        
        //We get only the .pm files
        //we remove the __module__.pm file too (because it is the main module itself)
        if( (preg_match("/\.pm$/", $tmp)) && (!preg_match("/__module__\.pm/", $tmp)) ){
        
            $subModulesClear[] = basename($tmp, ".pm");
        }
    }
    $subModules = $subModulesClear;

    return $subModules;
}

?>