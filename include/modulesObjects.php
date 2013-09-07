<?
abstract class Module{

	protected $name;

	//TODO : remove this variable it should be useless
	protected $instanceName;
	protected $instances;
	protected $pathToConfigFolder;
	protected $pathToConfigFile;
	protected $arguments;
	protected $userRelated = false;


	abstract function __construct($name, $configFolder, $parentModule);


	protected function readConfigurationFile(){

		$file = file_get_contents($this->pathToConfigFile);
        
        //we get the paragraph wich is between =HEAD1 ARGUMENTS and the next =something
        preg_match_all("/=head1 ARGUMENTS[^=]*/", $file, $out);
		$arguments = split("\n", $out[0][0]);
        array_shift($arguments);
        
        //we remove the empty arguments
        foreach($arguments as $arg){
            
            if($arg != NULL)$argumentsExport[] = $arg;
        }
        
		return $argumentsExport;
	}


	public function addInstance($instance){$this->instances[] = $instance;}
	public function getInstances(){return $this->instances;}
	public function getInstance($name){

		foreach($this->instances as $instance){

			if(preg_match("/".$instance->getName()."/", $name)){

				return $instance;
			}
		}
	}
	public function getName(){return $this->name;}
	public function setArguments($arguments){$this->arguments = $arguments;}


	//TODO : remove this two function, they chould be useless
	public function getInstanceName(){return $this->instanceName;}
	public function setInstanceName($name){$this->instanceName = $name;}
}


class MainModule extends Module{

	protected $subModules;


	function __construct($name, $configFolder, $parentModule=NULL){

		$this->name = $name;

		if($configFolder != NULL){

			$this->pathToConfigFolder = $configFolder;
			$this->pathToConfigFile = $configFolder."/__module__.pm";
			$this->arguments = $this->readConfigurationFile();

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
		$this->arguments = $this->readConfigurationFile();
	}
}


class Instance{

	private $name;
	private $arguments;

	function __construct($name, $arguments){

		$this->name = $name;
		$this->arguments = $arguments;
	}

	public function getName(){return $this->name;}
	public function setName($name){$this->name = $name;}
	public function getArguments(){return $this->arguments;}
	public function setArguments($arguments){$this->arguments = $arguments;}
}



?>
