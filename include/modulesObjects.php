<?php

include_once("argumentObject.php");


abstract class Module{

	protected $name;

	//TODO : remove this variable it should be useless
	protected $instanceName;
	protected $instances;
    protected $nameVarInstance; //the name of the variables which is the name of this module
	protected $pathToConfigFolder;
	protected $pathToConfigFile;
	protected $arguments;
	protected $userRelated = false;
    protected $afterModules;


	abstract function __construct($name, $configFolder, $parentModule);


	protected function readConfigurationFile(){

		$file = file_get_contents($this->pathToConfigFile);
        
        //we get the paragraph wich is between =HEAD1 ARGUMENTS and the next =something
        preg_match_all("/=head1 ARGUMENTS(\s|.)*\n\=/U", $file, $out);
		$arguments = split("\n", $out[0][0]);
        array_shift($arguments);    //remode the line with =head1 ARGUMENTS
        array_pop($arguments);  //remove the line with the next = statement of the file
        
        //we remove the empty arguments
        foreach($arguments as $arg){
            if($arg != NULL){
                
                $parts = split(" ", $arg);
                
                $type = $parts[0];
                
                if($type === "string"){
                    
                    $name = $parts[1];
                    $argumentsExport[] = new StringArg($name, NULL);
                }elseif($type === "after"){
                    
                    $name = $parts[1];
                    $argumentsExport[] = new AfterArg(NULL);
                }elseif($type === "number"){
                    
                    $name = $parts[1];
                    $argumentsExport[] = new NumberArg($name, NULL);
                }elseif($type === "bool"){
                    
                    $name = $parts[1];
                    $argumentsExport[] = new BoolArg($name, NULL);
                }elseif($type === "array"){
                    
                    $subType = createObjectArgumentBasic($parts[1], [NULL, NULL]);
                    $name = $parts[2];
                    $argumentsExport[] = new ArrayArg($name, $subType, NULL);
                }elseif($type === "hash"){
                    
                    $name = $parts[1];
                    $endHash = array_search("endhash", $parts);
                    
                    $hashDefinition = array_slice($parts, 2, $endHash-2);
                    
                    $argumentsExport[] = new HashArg($name, $hashDefinition);
                }
            }
        }
        $this->arguments = $argumentsExport;
        
        
        //we now search for the argument which correspond to the instance name
        preg_match_all("/=head1 INSTANCENAME(\s|.)*\n\=/U", $file, $out);
        $linesName = split("\n", $out[0][0]);        
        array_shift($linesName);    
        array_pop($linesName);

        foreach($linesName as $nameVar){
            if(!preg_match("/^\s*$/", $nameVar) ){
                $nameVarExport[] = $nameVar;
            }
        }
        if($nameVarExport != NULL)$this->nameVarInstance = $nameVarExport[0];
        
        
        //we now search for the after statement. The list of module after which this module mult be defined
        preg_match_all("/=head1 AFTER(\s|.)*\n\=/U", $file, $out);
        $afterNames = split("\n", $out[0][0]);        
        array_shift($afterNames);    
        array_pop($afterNames);
        
        $this->afterModules = array();
        foreach($afterNames as $nameVar){
            if(!preg_match("/^\s*$/", $nameVar) ){
                $this->afterModules[] = $nameVar;
            }
        }
	}

    public function isActivated(){if(count($this->instances) == 0)return 0;else return 1;}
	public function addInstance($instance){$this->instances[] = $instance;}
    public function getAfterModules(){return $this->afterModules;}
	public function getInstances(){return $this->instances;}
	public function getInstance($name){

		foreach($this->instances as $instance){

			if(preg_match("/".$instance->getName()."/", $name)){
                
				return $instance;
			}
		}
        
        return NULL;
	}
    public function deleteInstance($name){
                
        for($i=0; $i<count($this->instances); $i++){

            $instance = $this->instances[$i];
			if(preg_match("/".$instance->getName()."/", $name)){
                
				//return $instance;
                array_splice($this->instances, $i, 1);
                return "done";
			}
		}        
    }
    public function clearInstances(){$this->instances = NULL;}
	public function getName(){return $this->name;}
    public function getArguments(){return $this->arguments;}
    public function getArgument($name){
         
        foreach($this->arguments as $argument){
            
            if($argument->getName() == $name){
                
                return $argument;
            }
        }
        
        return NULL;
    }

	public function setArguments($arguments){$this->arguments = $arguments;}
    public function addArgument($argument){$this->arguments[] = $argument;}
    public function getArgType($name){
        
        foreach($this->arguments as $argument){
            
            if($argument->getName() == $name){
                
                return $argument->getType();
            }
        }
        
        return NULL;
    }
    
        
    public function toForm(){
        
        $form = "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
        $form .= "salut il y a : ".count($this->arguments)." arguments.<br>";
    
        foreach($this->arguments as $argument){
                
            $form .= $argument->getName()." : ";
            $form .= $argument->toForm()."<br>";
        }
        
        $form .= "<input type=\"submit\" value=\"Save\">";
        $form .= "<input class=\"deleteInstance\" type=\"button\" value=\"Delete\">";
        $form .= "</form>";
        
        return $form;
    }

    public function toJson(){
        
        $response = "[";

        foreach($this->arguments as $argument){
                
            $response .= $argument->toJson().",";
        }

		$response = substr($response, 0, -1); //remove the last useless ","
        $response .= "]";
		return $response;
    }
    
	//TODO : remove this two function, they chould be useless
	public function getInstanceName(){return $this->instanceName;}
	public function setInstanceName($name){$this->instanceName = $name;}
    
    public function getNameVarInstance(){return $this->nameVarInstance;}
}


class MainModule extends Module{

	protected $subModules;

	function __construct($name, $configFolder, $parentModule=NULL){

		$this->name = $name;

		if($configFolder != NULL){

			$this->pathToConfigFolder = $configFolder;
			$this->pathToConfigFile = $configFolder."/__module__.pm";
			$this->readConfigurationFile();

            $this->subModules = array();
			foreach(getListOfAvalableSubModules($name) as $subModule){	//crete the list of submodules

				$this->subModules[] = new SubModule($subModule, $configFolder, $this);
			}
		}
	}


	public function getSubModule($name){

		foreach($this->subModules as $subModule){

			if(preg_match("/".$subModule->getName()."/", $name)){

				return $subModule;
			}
		}
	}
    
	public function getSubModules(){return $this->subModules;}

    //function return an array containing all the instances of the module
    //and all the instances of the suModulles associated to it
    public function getMainAndSubInstances(){
        
        $instances = $this->getInstances();
        
        foreach($this->getSubModules() as $subModule){
            
            $instances = array_merge(instances, $subModule->getInstances());
        }
    
        return $instances;
    }
    
    
}




class SubModule extends Module{

	private $parentModule;


	function __construct($name, $configFolder, $parentModule){

		$this->name = $name;
		$this->pathToConfigFolder = $configFolder;
		$this->pathToConfigFile = $configFolder."/".$name.".pm";
		$this->parentModule = $parentModule;
		$this->readConfigurationFile();
        
        $this->addArgument(new AfterArg(NULL));
	}
}


class Instance{

	private $name;
	private $arguments;
    private $hasBeenWritten;
    private $afterObjects;
    private $motherModule;

	function __construct($name, $arguments, $motherModule){

        $this->hasBeenWritten = false;
		$this->name = $name;
        $this->motherModule = $motherModule;
        $this->arguments = array();

		//TODO : the second arg migth not be usefull
        if( (isset($arguments)) && ($arguments != NULL) ) $this->createArguments($arguments, $motherModule);
        $this->afterObjects = array();
	}
    
    
    private function createArguments($arguments){
        
        foreach($arguments as $argument){
                
            $argName = $argument[0];
            $argObject = $this->motherModule->getArgument($argName);     
            $this->arguments[] = createObjectArgumentFromString($argObject, $argument);
        }
    }

	public function getName(){return $this->name;}
	public function setName($name){$this->name = $name;}
	public function getArguments(){return $this->arguments;}
    public function getArgument($name){
    
        foreach($this->arguments as $argument){
            
            if($argument->getName() == $name){
                
                return $argument->getValue();
            }
        }
        return "";
    }
    public function getArgumentObject($name){
    
        foreach($this->arguments as $argument){
            
            if($argument->getName() == $name){
                
                return $argument;
            }
        }
        return NULL;
    }
	public function setArguments($arguments){$this->arguments = $arguments;}
    public function setArgument($argumentName, $value){
        
        for($i=0; $i<count($this->arguments); $i++){
            
            if($this->arguments[$i]->getName() == $argumentName){
                
                $this->arguments[$i]->setValue($value);
                return 0;
            }
        }
        
        //if we are still here it's that the argument didn't already exist, so we create it
        //first we need to find the type of this arg by checking in the motherModule
        $type = $this->motherModule->getArgType($argumentName);
        
        if($type === "string"){
            
            $this->arguments[] = new StringArg($argumentName, $value);
        }elseif($type === "after"){
            
            $this->arguments[] = new AfterArg($value);
        }elseif($type === "number"){
                    
            $this->arguments[] = new NumberArg($argumentName, $value);
        }elseif($type === "bool"){
                    
            $this->arguments[] = new BoolArg($argumentName, $value);
        }elseif($type === "array"){

			//$newArgument = clone $this->motherModule->getArgument($argumentName);

			$newArgument = new ArrayArg($argumentName, NULL);
			$newArgument->setValue($value);	//$value
			$this->arguments[] = $newArgument;
			
            //$this->arguments[] = new ArrayArg($argumentName, $value);
        }    
            
    }
    
    
    public function toForm(){
        
        $form = "<form class=\"instanceForm\" onsubmit=\"return false;\" method=\"post\">";
        $form .= "salut il y a : ".count($this->motherModule->getArguments())." arguments.<br>";
    
        foreach($this->motherModule->getArguments() as $argument){
                
            $form .= $argument->getName()." : ";
            
            $instanceArg = $this->getArgumentObject($argument->getName());
            //if the arg is not defined in this instance, we ask the module to display the form
            if(isset($instanceArg))$form .= $instanceArg->toForm()."<br>";
            else $form .= $argument->toForm()."<br>";
        }
        
        
        $form .= "<input type=\"submit\" value=\"Save\">";
        $form .= "<input class=\"deleteInstance\" type=\"button\" value=\"Delete\">";
        $form .= "</form>";
        
        
        return $form;
    }


    public function toJson(){
        
        $response = "[";

        foreach($this->motherModule->getArguments() as $argument){
        
            $instanceArg = $this->getArgumentObject($argument->getName());
            //if the arg is not defined in this instance, we ask the module to display the form
            if(isset($instanceArg))$response .= $instanceArg->toJson().",";
            else $response .= $argument->toJson().",";
        }

		$response = substr($response, 0, -1); //remove the last useless ","
        $response .= "]";

		return $response;
    }
    
    public function getHasBeenWritten(){return $this->hasBeenWritten;}
    public function setHasBeenWritten($val){$this->hasBeenWritten = $val;}
    
    public function getAfterObjects(){return $this->afterObjects;}
    public function addAfterObject($object){$this->afterObjects[] = $object;}
    public function addAfterObjects($objects){$this->afterObjects = array_merge($this->afterObjects, $objects);}
    public function isReadyToBeWritten(){
        
        foreach($this->afterObjects as $object){
            
            if($object->getHasBeenWritten() == false){return false;}
        }
        
        return true;
    }
}



?>
