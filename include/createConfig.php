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
    
    
    
    
    
    $config = createAfterLinks($config);
    
    while($config->isCompletlyWritten() == false){
        
        foreach($config->getAvalableModules() as $module){
            
            foreach($module->getInstances() as $moduleInstance){
                
                if( ($moduleInstance->getHasBeenWritten() == false) && ($moduleInstance->isReadyToBeWritten() == true) ){
                    fwrite($file, "\tService::".$module->getName()."::define({\n\n");
                    foreach($moduleInstance->getArguments() as $argument)fwrite($file, "\t\t'".$argument[0]."' => '".$argument[1]."',\n");
                    fwrite($file, "\t});\n\n");
                                        
                    $moduleInstance->setHasBeenWritten(true);
                }
            }        
            
            foreach($module->getSubModules() as $subModule){
                foreach($subModule->getInstances() as $subModuleInstance){
    
                if( ($subModuleInstance->getHasBeenWritten() == false) && ($subModuleInstance->isReadyToBeWritten() == true) ){
                        fwrite($file, "\tService::".$module->getName()."::".$subModule->getName()."::define({\n\n");
                        foreach($subModuleInstance->getArguments() as $argument)fwrite($file, "\t\t'".$argument[0]."' => '".$argument[1]."',\n");
                        fwrite($file, "\t});\n\n");
                        
                        $subModuleInstance->setHasBeenWritten(true);
                    }
                }
            }
        }        
    }
    

    fwrite($file, "};\n\n");
 
    fclose($file);
}



function createAfterLinks($config){
    
    foreach($config->getAvalableModules() as $module){
        
        foreach($module->getInstances() as $moduleInstance){
            
            if(!($moduleInstance->getArgument("after") === "")){
                
                $object = getInstanceFromString($moduleInstance->getArgument("after"), $config);
                $moduleInstance->addAfterObject($object);
            }
        }        
        
        foreach($module->getSubModules() as $subModule){
            foreach($subModule->getInstances() as $subModuleInstance){

                //a submodule must always be defined after the mainModule it is associated to
                $subModuleInstance->addAfterObjects($module->getInstances());
                
                if(!($subModuleInstance->getArgument("after") === "")){
                    
                    $object = getInstanceFromString($subModuleInstance->getArgument("after"), $config);
                    $subModuleInstance->addAfterObject($object);
                }
            }
        }
    }
    
    return $config;
}


function getInstanceFromString($string, $config){
    
    $names = explode("::", $string);
    
    $module = $config->getModule($names[0]);
    $subModule = $module->getSubModule($names[1]);
    $instance = $subModule->getInstance($names[2]);

    return $instance;
}



?>