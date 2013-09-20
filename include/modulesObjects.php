<?
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


	abstract function __construct($name, $configFolder, $parentModule);


	protected function readConfigurationFile(){

		$file = file_get_contents($this->pathToConfigFile);
        
        //we get the paragraph wich is between =HEAD1 ARGUMENTS and the next =something
        preg_match_all("/=head1 ARGUMENTS(\s|.)*\n\=/U", $file, $out);
		$arguments = split("\n", $out[0][0]);
        array_shift($arguments);    
        array_pop($arguments);
        
        //we remove the empty arguments
        foreach($arguments as $arg){
            if($arg != NULL){
                $argumentsExport[] = $arg;
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
	}

    public function isActivated(){if(count($this->instances) == 0)return 0;else return 1;}
	public function addInstance($instance){$this->instances[] = $instance;}
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
	public function setArguments($arguments){$this->arguments = $arguments;}
    public function addArgument($argument){
        
        $this->arguments[] = $argument;
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

}




class SubModule extends Module{

	private $parentModule;


	function __construct($name, $configFolder, $parentModule){

		$this->name = $name;
		$this->pathToConfigFolder = $configFolder;
		$this->pathToConfigFile = $configFolder."/".$name.".pm";
		$this->parentModule = $parentModule;
		$this->readConfigurationFile();
        
        $this->addArgument("after");
	}
}


class Instance{

	private $name;
	private $arguments;
    private $hasBeenWritten;
    private $afterObjects;

	function __construct($name, $arguments){

        $this->hasBeenWritten = false;
		$this->name = $name;
		$this->arguments = $arguments;
        $this->afterObjects = [];
	}

	public function getName(){return $this->name;}
	public function setName($name){$this->name = $name;}
	public function getArguments(){return $this->arguments;}
    public function getArgument($name){
    
        foreach($this->arguments as $argument){
            
            if($argument[0] == $name){
                
                return $argument[1];
            }
        }
        return "";
    }
	public function setArguments($arguments){$this->arguments = $arguments;}
    public function setArgument($argument, $value){
        
        for($i=0; $i<count($this->arguments); $i++){
            
            if($this->arguments[$i][0] == $argument){
                
                $this->arguments[$i][1] = $value;
                return 0;
            }
        }
        
        //if we are still here it's that the argument didn't already exist, so we create it
        $this->arguments[] = [$argument, $value];        
    }
    
    public function getHasBeenWritten(){return $this->hasBeenWritten;}
    public function setHasBeenWritten($val){$this->hasBeenWritten = val;}
    
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
