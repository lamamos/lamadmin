<?
include_once("configuration.php");
include_once("createConfig.php");
include_once("modulesObjects.php");

class Configuration{

	protected $availableModules;
	protected $installedSubModules;



	function __construct(){

        global $PathToRexConfiguration;
        
		$this->initialisation();

		foreach(getListOfAvalableModules() as $module){

			$configFolder = $PathToRexConfiguration."/lib/Service/".$module;
            $this->availableModules[] = new MainModule($module, $configFolder);
		}

		$this->readRexifile();
	}


	function readRexifile(){

		global $PathToRexConfiguration;

		$lines = file_get_contents($PathToRexConfiguration."/Rexfile");

        //The U at then end of the regexp is for ungready
        preg_match_all("/Service::.*\(\{(\s|.)*\}\)\;/U", $lines, $out);
        
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
            preg_match_all("/\'(\s|.)*\'\s*=>\s*\'(\s|.)*\',/U", $instanceString, $args);
            $args = $args[0];
            
            unset($keyValue);
            foreach($args as $argument){
                
                $argument = explode("=>", $argument);
                //we get only what is between quotes
                preg_match_all('/".*?"|\'.*?\'/', $argument[0], $matches);
                $argument[0] = $matches[0][0];
                preg_match_all('/".*?"|\'.*?\'/', $argument[1], $matches);
                $argument[1] = $matches[0][0];
                //we remove the ' and ", first and last char of the string
                $argument[0] = substr($argument[0], 1, -1);
                $argument[1] = substr($argument[1], 1, -1);

                $keyValue[] = $argument;
            }
            
            //we now create the object corresponding to this instance
            //we first find the name of the instance
            $nameVar = $type->getNameVarInstance();
            unset($name);
            foreach($keyValue as $tmp){
                
                if($tmp[0] == $nameVar){
                 
                    $name = $tmp[1];
                }
            }
            if($name == NULL)if($names[1] != NULL)$name = $names[1]; else $name = $names[0];

            //we then create the instance
            $type->addInstance(new Instance($name, $keyValue));
        }
	}


	function initialisation(){

	}




	function getModule($moduleName){

		foreach($this->availableModules as $module){

			if(preg_match("/".$module->getName()."/", $moduleName)){

				return $module;
			}
		}
	}

	function getAvalableModules(){return $this->availableModules;}
	function ping(){return "kikoo";}
}

?>