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


function writeConfigFile($config){
    
    global $PathToRexConfiguration;
    $file = fopen($PathToRexConfiguration."/Rexfile", 'w') or die("can't open file");

    fwrite($file, "user \"root\";\n");
    fwrite($file, "private_key \"/root/.ssh/id_rsa\";\n");
    fwrite($file, "public_key \"/root/.ssh/id_rsa.pub\";\n");
    fwrite($file, "key_auth;\n\n");
    
    fwrite($file, "group martobre => \"192.168.0.151\";\n\n");
    
    fwrite($file, "require Rex::Logger;\n");
    foreach($config->getAvalableModules() as $module){
        
        fwrite($file, "require Service::".$module->getName().";\n");
        foreach($module->getSubModules() as $subModule){
            
            fwrite($file, "require Service::".$module->getName()."::".$subModule->getName().";\n");
        }
    }
    
    fwrite($file, "\ntask \"configure\", group => martobre, sub{\n\n");
    
    foreach($config->getAvalableModules() as $module){
        
        foreach($module->getInstances() as $moduleInstance){
            
            fwrite($file, "\tService::".$module->getName()."::define({\n\n");
            foreach($moduleInstance->getArguments() as $argument)fwrite($file, "\t\t'".$argument[0]."' => '".$argument[1]."',\n");
            fwrite($file, "\t});\n\n");
        }        
        
        foreach($module->getSubModules() as $subModule){
            foreach($subModule->getInstances() as $subModuleInstance){

                //fwrite($file, "require Service::".$module->getName()."::".$subModule->getName().";\n");
                fwrite($file, "\tService::".$module->getName()."::".$subModule->getName()."::define({\n\n");
                foreach($subModuleInstance->getArguments() as $argument)fwrite($file, "\t\t'".$argument[0]."' => '".$argument[1]."',\n");
                fwrite($file, "\t});\n\n");
            }
        }
    }
    
    fwrite($file, "};\n\n");
    
    
    fclose($file);
}

?>