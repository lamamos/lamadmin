<?php
/*
 Copyright (C) 2013-2014 Clément Roblot

This file is part of lamadmin.

Lamadmin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Lamadmin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Lamadmin.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once("configuration.php");
include_once("createConfig.php");
include_once("modulesObjects.php");
include_once("argumentObject.php");

require_once("FirePHPCore/FirePHP.class.php");

class Configuration{

	protected $availableModules;
	protected $installedSubModules;



	function __construct(){

    global $PathToRexConfiguration;
        
		$this->initialisation();

        if(is_array(getListOfAvalableModules())){
    		foreach(getListOfAvalableModules() as $module){

                $configFolder = $PathToRexConfiguration."/lib/Service/".$module;
                $this->availableModules[] = new MainModule($module, $configFolder);
    		}
        }

		$this->readRexifile();
	}


	function readRexifile(){

		global $PathToRexConfiguration;

		$lines = file_get_contents($PathToRexConfiguration."/Rexfile");

        //The U at then end of the regexp is for ungready
        preg_match_all("/Service::.*\(\{(\s|.)*\}\)\;/U", $lines, $out);
        
        if(is_array($out[0])){
            foreach($out[0] as $instanceString){
                
                //find module name
                preg_match("/^.*\(\{/", $instanceString, $out);
                //we remove the two last char of the strig (which are "({")
                $firstLine = substr($out[0], 0, -2);
                
                $names = explode("::", $firstLine);
                //we remove the first element which is "Service"
                array_shift($names);
                //we remove the last element which is "define"
                array_pop($names);
                
                
                //if the names array has a size of one we have a mainModule, if it is 2 it's a usbmodule
                //we are getting the right object in the configuration
                switch(count($names)){
                    
                    case 1:     //if we got a mainModule
                                $type = $this->getModule($names[0]);
                                break;
                    
                    case 2:     //if we got a submodule
                                $type = $this->getModule($names[0])->getSubModule($names[1]);
                                break;
                    
                    default:    //if there is something strange
                                break;
                    
                }
                
                
                
                //we now gets the arguments given to this element
                preg_match_all("/\'(\s|.)*\'\s*=>\s*(\s|.)*,\n/U", $instanceString, $args);
                $args = $args[0];


                unset($keyValue);
                $keyValue = array();
                if(is_array($args)){
                    foreach($args as $argument){

                        $argument = explode("=>", $argument, 2);	//we cut only at the first "=>" (the 2, is for 2 elements max returned)
                        //we get only what is between quotes
                        preg_match_all('/".*?"|\'.*?\'/', $argument[0], $matches);
                        $argument[0] = $matches[0][0];
                        $argument[0] = substr($argument[0], 1, -1);

                        $argument[1] = readArgument($argument[1]);
                        
                        $keyValue[] = $argument;
                    }
                }
    			//keyval is an array of array shaped like this : ['arg_Name', 'arg_value'], avec arg-value an array is needed

                
                //we now create the object corresponding to this instance
                //we first find the name of the instance
                $nameVar = $type->getNameVarInstance();
                unset($name);
                $name = "";
                if(is_array($keyValue)){
                    foreach($keyValue as $tmp){
                        
                        if($tmp[0] == $nameVar){
                         
                            $name = $tmp[1];
                        }
                    }
                }
                if($name == NULL){if( (isset($names[1])) && ($names[1] != NULL) )$name = $names[1]; else $name = $names[0];}

                //we then create the instance
                $type->addInstance(new Instance($name, $keyValue, $type));
            }
        }
	}


	function initialisation(){

	}



	function getModules(){return $this->availableModules;}
	function getModule($moduleName){

        if(is_array($this->availableModules)){
    		foreach($this->availableModules as $module){

    			if(preg_match("/".$module->getName()."/", $moduleName)){

    				return $module;
    			}
    		}
        }
	}

	function getAvalableModules(){return $this->availableModules;}
	function ping(){return "kikoo";}
    
    function isCompletlyWritten(){
        
        if(is_array($this->getAvalableModules())){
            foreach($this->getAvalableModules() as $module){

                if(is_array($module->getInstances())){
                    foreach($module->getInstances() as $moduleInstance){
                        
                        if($moduleInstance->getHasBeenWritten() == false){return false;}
                    }  
                }      
                
                if(is_array($module->getSubModules())){
                    foreach($module->getSubModules() as $subModule){

                        if(is_array($subModule->getInstances())){
                            foreach($subModule->getInstances() as $subModuleInstance){
                
                                if($subModuleInstance->getHasBeenWritten() == false){return false;}
                            }
                        }
                    }
                }
            }
        }
        
        return true;
    }
    
    
    function numberInstanceStillToWrite(){
        
        $count = 0;
        
        if(is_array($this->getAvalableModules())){
            foreach($this->getAvalableModules() as $module){

                if(is_array($module->getInstances())){
                    foreach($module->getInstances() as $moduleInstance){
                        
                        if($moduleInstance->getHasBeenWritten() == false){$count++;}
                    }
                }
                
                if(is_array($module->getSubModules())){
                    foreach($module->getSubModules() as $subModule){
                        if(is_array($subModule->getInstances())){
                            foreach($subModule->getInstances() as $subModuleInstance){
                
                                if($subModuleInstance->getHasBeenWritten() == false){$count++;}
                            }
                        }
                    }
                }
            }
        }
            
        return $count;
    }
    
    
    
    
    
    
    function writeConfigFile(){
    
        global $PathToRexConfiguration;
        $file = fopen($PathToRexConfiguration."/Rexfile", 'w') or die("can't open file");
    
        fwrite($file, "user \"root\";\n");
        fwrite($file, "password \"test\";\n");
        fwrite($file, "#private_key \"/root/.ssh/id_rsa\";\n");
        fwrite($file, "#public_key \"/root/.ssh/id_rsa.pub\";\n");
        fwrite($file, "#key_auth;\n");
        fwrite($file, "pass_auth;\n\n");
        
        fwrite($file, "group server => \"localhost\";\n\n");

        fwrite($file, "use config;\n");
        fwrite($file, "use install;\n");
        fwrite($file, "use init;\n");
        fwrite($file, "\n");

        fwrite($file, "require Rex::Logger;\n");
        fwrite($file, "require communication;\n");
        fwrite($file, "\n");

        if(is_array($this->getAvalableModules())){
            foreach($this->getAvalableModules() as $module){
                
                fwrite($file, "require Service::".$module->getName().";\n");

                if(is_array($module->getSubModules())){
                    foreach($module->getSubModules() as $subModule){
                        
                        fwrite($file, "require Service::".$module->getName()."::".$subModule->getName().";\n");
                    }
                }
            }
        }
        
        fwrite($file, "\ntask \"configure\", group => server, sub{\n\n");
        
        fwrite($file, "  initialise();\n\n");
        
        
        
        $this->createAfterLinks();        
        $stillToWritte = 0;
        
        do{
            $stillToWritte = $this->numberInstanceStillToWrite();
            
            if(is_array($this->getAvalableModules())){
                foreach($this->getAvalableModules() as $module){
                    
                    if(is_array($module->getInstances())){
                        foreach($module->getInstances() as $moduleInstance){
                            
                            if( ($moduleInstance->getHasBeenWritten() == false) && ($moduleInstance->isReadyToBeWritten() == true) ){
                                fwrite($file, "\tService::".$module->getName()."::define({\n\n");
                                foreach($moduleInstance->getArguments() as $argument){
                                    
                                    $argumentInString = $argument->toConfigFile();
                                    fwrite($file, "\t\t".$argumentInString."\n"); //fwrite($file, "\t\t'".$argument[0]."' => '".$argument[1]."',\n");
                                    
                                }
                                fwrite($file, "\t});\n\n");
                                                    
                                $moduleInstance->setHasBeenWritten(true);
                            }
                        }
                    }
                    
                    if(is_array($module->getSubModules())){
                        foreach($module->getSubModules() as $subModule){
                            if(is_array($subModule->getInstances())){
                                foreach($subModule->getInstances() as $subModuleInstance){
                    
                                    if( ($subModuleInstance->getHasBeenWritten() == false) && ($subModuleInstance->isReadyToBeWritten() == true) ){
                                        fwrite($file, "\tService::".$module->getName()."::".$subModule->getName()."::define({\n\n");

                                        if(is_array($subModuleInstance->getArguments())){
                                            foreach($subModuleInstance->getArguments() as $argument){
                                                
                                                $argumentInString = $argument->toConfigFile();
                                                fwrite($file, "\t\t".$argumentInString."\n");
                                            }
                                        }
                                        fwrite($file, "\t});\n\n");
                                        
                                        $subModuleInstance->setHasBeenWritten(true);
                                    }
                                }
                            }
                        }
                    }
                }  
            }      
        }while($stillToWritte != $this->numberInstanceStillToWrite());    //when we don't write anything anymore in the file
        
        
        if($this->numberInstanceStillToWrite() != 0){ //if there is still instances that has not been written, it's that there is a loop
            
            //TODO : Send error message saying the elements still to write are wrong
        }
        
    
        fwrite($file, "  finalise();\n");

        fwrite($file, "};\n\n");
     
        fclose($file);
    }
    
    
    
    function createAfterLinks(){
        
        if(is_array($this->getAvalableModules())){
            foreach($this->getAvalableModules() as $module){
                
                if (is_array($module->getInstances())){
                    foreach($module->getInstances() as $moduleInstance){
                        
                        if (is_array($module->getAfterModules())){
                            //add the after statement in the description of the module (after section)
                            foreach($module->getAfterModules() as $afterModule){
                                
                                $afterModuleObject = $this->getModule($afterModule);
                                $afterModuleInstances = $afterModuleObject->getMainAndSubInstances();
                                $moduleInstance->addAfterObjects($afterModuleInstances);
                            }
                        }
                        
                        if(!($moduleInstance->getArgument("after") === "")){
                            
                            $object = $this->getInstanceFromString($moduleInstance->getArgument("after"));
                            $moduleInstance->addAfterObject($object);
                        }
                    }  
                }      
                
                if (is_array($module->getSubModules())){
                    foreach($module->getSubModules() as $subModule){
                        if (is_array($subModule->getInstances())){
                            foreach($subModule->getInstances() as $subModuleInstance){
                
                                //a submodule must always be defined after the mainModule it is associated to
                                $subModuleInstance->addAfterObjects($module->getInstances());
                                
                                if (is_array($subModule->getAfterModules())){
                                    //add the after statement in the description of the module (after section)
                                    foreach($subModule->getAfterModules() as $afterModule){
                                        
                                        $afterModuleObject = $this->getModule($afterModule);
                                        $afterModuleInstances = $afterModuleObject->getMainAndSubInstances();
                                        $subModuleInstance->addAfterObjects($afterModuleInstances);
                                    }
                                }
                                
                                if(!($subModuleInstance->getArgument("after") === "")){
                                    
                                    $object = $this->getInstanceFromString($subModuleInstance->getArgument("after"));
                                    $subModuleInstance->addAfterObject($object);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    
    
    function getInstanceFromString($string){
        
        $names = explode("::", $string);
        
        $module = $this->getModule($names[0]);
        $subModule = $module->getSubModule($names[1]);
        $instance = $subModule->getInstance($names[2]);
    
        return $instance;
    }

    
    
    
}

?>
