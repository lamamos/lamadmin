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


require_once("FirePHPCore/FirePHP.class.php");
include_once("argumentObject.php");


/** \brief Abstract class used to define the common methodes between the MainModules and SubModules
  *
  */
abstract class Module{

  protected $name;                /**< The name of this module */

  protected $instances;           /**< An array of the instances of this module */
  protected $nameVarInstance;     /**< The name of the variables which is the name of this module */
  protected $pathToConfigFolder;  /**< The path to the folder in wich this module is defined */
  protected $pathToConfigFile;    /**< Tha path to the file containing the definition of this module */
  protected $arguments;           /**< An array of the arguments defining this module */
  protected $userRelated = false; /**< If this module is owned by a specific user or not */
  protected $afterModules;        /**< An array of the module after which this one must be written in the Rexify */


  /** \brief Constructor of the Module object.
    *
    * \param $name The name of this SubModule (as a string).
    * \param $configFolder The folder in wich we can find the definition of the module (and so read the arguments that need to be displayed to the user)
    * \param $parentModule The module to which this one refers to (cf constructor of MainModule and SubModule)
  */
	abstract function __construct($name, $configFolder, $parentModule);

  /** \brief Function getting the arguments of the module from it's definition file.
    *
    * This function is going to build the arguments necessary to the configuration of this module.
  */
	protected function readConfigurationFile(){

		$file = file_get_contents($this->pathToConfigFile);
        
    //we get the paragraph wich is between =HEAD1 ARGUMENTS and the next =something
    preg_match_all("/=head1 ARGUMENTS(\s|.)*\n\=/U", $file, $out);
    if(count($out[0]) == 0) $arguments = [];
		else $arguments = split("\n", $out[0][0]);
        array_shift($arguments);    //remove the line with =head1 ARGUMENTS
        array_pop($arguments);  //remove the line with the next = statement of the file

        $argumentsExport = [];
        
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
                    
                    $argumentsExport[] = new HashArg($name, $hashDefinition, NULL);
                }
            }
        }
        $this->arguments = $argumentsExport;
        
        //we now search for the argument which correspond to the instance name
        preg_match_all("/=head1 INSTANCENAME(\s|.)*\n\=/U", $file, $out);
        if(count($out[0]) == 0) $linesName = [];
        else $linesName = split("\n", $out[0][0]);        
        array_shift($linesName);    
        array_pop($linesName);

        foreach($linesName as $nameVar){
            if(!preg_match("/^\s*$/", $nameVar) ){
                $nameVarExport[] = $nameVar;
            }
        }
        if(isset($nameVarExport) && ($nameVarExport != NULL))$this->nameVarInstance = $nameVarExport[0];
        
        
        //we now search for the after statement. The list of module after which this module mult be defined
        preg_match_all("/=head1 AFTER(\s|.)*\n\=/U", $file, $out);
        if(count($out[0]) == 0) $afterNames = [];        
        else $afterNames = split("\n", $out[0][0]);        
        array_shift($afterNames);    
        array_pop($afterNames);
        
        $this->afterModules = array();
        foreach($afterNames as $nameVar){
            if(!preg_match("/^\s*$/", $nameVar) ){
                $this->afterModules[] = $nameVar;
            }
        }
	}

  /** \brief Function to test if this module is activated (used on the server).
    *
    * \return 0 if this module is not used (nothing written in Rexify about this module), else 1.
  */
  public function isActivated(){if(count($this->instances) == 0)return 0;else return 1;}
  /** \brief Function adding an instance to the ones of this module
    *
    * \param $instance The Instance object to add to the ones of this Module.
  */
	public function addInstance($instance){$this->instances[] = $instance;}
  /** \brief Function Returning the array of modules this one must be written after.
    *
    * \return An array of Modules object
  */
  public function getAfterModules(){return $this->afterModules;}
  /** \brief Function Returning the array of instances of this module.
    *
    * \return An array of Instance objects
  */
	public function getInstances(){return $this->instances;}
  /** \brief Function Returning the instances we are searching for.
    *
    * \param $name The name of the instance we are searching for.
    * \return The Instance object we are searching for.
  */
	public function getInstance($name){

		foreach($this->instances as $instance){

			if(preg_match("/".$instance->getName()."/", $name)){
                
				return $instance;
			}
		}
        
        return NULL;
	}

  /** \brief Function deletting an instances of the module.
    *
    * \param $name The name of the instance we want to delete.
  */
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
  /** \brief Function deletting all the instances of the module.
    *
  */
  public function clearInstances(){$this->instances = NULL;}
  /** \brief Function returning the name of the module.
    *
    * \return The name of the module as a string
  */
	public function getName(){return $this->name;}
  /** \brief Function Returning the array of Arguments of this module.
    *
    * \return An array of Argument objects
  */
  public function getArguments(){return $this->arguments;}
  /** \brief Function Returning the Arguments we are searching for.
    *
    * \param $name The name of the Arguments we are searching for.
    * \return The Arguments object we are searching for.
  */
  public function getArgument($name){
         
    foreach($this->arguments as $argument){

      if($argument->getName() == $name){

          return $argument;
      }
    }

    return NULL;
  }

  /** \brief Function setting the arguments of the module.
    *
    * \param $arguments Array of arguments to define the Module
  */
	public function setArguments($arguments){$this->arguments = $arguments;}
  /** \brief Function adding an argument to the module.
    *
    * \param $argument Argument object to add to this module
  */
  public function addArgument($argument){$this->arguments[] = $argument;}
  /** \brief Function Returning the type of an Argument of the module
    *
    * \param $name The name of the Arguments we are searching the type.
    * \return The type of Arguments object we are searching for (as a string).
  */
  public function getArgType($name){
      
      foreach($this->arguments as $argument){
          
          if($argument->getName() == $name){
              
              return $argument->getType();
          }
      }
      
      return NULL;
  }
  
  /** \brief Function returning the current Module as an html form.
  *
  * \return The form input corresponding to this Module, as a string.
  */
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

  /** \brief Function returning the current Module as JSON.
    *
    * \return The Module as a JSON string.
  */
  public function toJson(){

    $response = "[";

    foreach($this->arguments as $argument){
      
      $response .= $argument->toJson().",";
    }

    $response = substr($response, 0, -1); //remove the last useless ","
    $response .= "]";
    return $response;
  }
  
  /** \brief Function returning the name of the variables which is the name of this module
    *
    * \todo Check if we actualy are using this nameVarInstance variable.
    *
    * \return The name of the variables which is the name of this module
  */
  public function getNameVarInstance(){return $this->nameVarInstance;}
}















/** \brief Object used to define a Mainmodule (a software that is available on the server (like apache))
  *
  */
class MainModule extends Module{

	protected $subModules; /**< An array of SubModules objects refering to this MainModule */

  /** \brief Constructor of the MainModule object.
    *
    * \param $name The name of this SubModule (as a string).
    * \param $configFolder The folder in wich we can find the definition of the module (and so read the arguments that need to be displayed to the user)
    * \param $parentModule The MainModule don't refer to any parent module (leave empty)
  */
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

  /** \brief Function to get a submodule refering to this MainModule
    *
    * \param $name The name of the submodule we are searching for (as a string).
    * \return The SubModule object corresponding to the name you inputed.
  */
	public function getSubModule($name){

		foreach($this->subModules as $subModule){

			if(preg_match("/".$subModule->getName()."/", $name)){

				return $subModule;
			}
		}
	}
    
  /** \brief Function to get all the submodules refering to this MainModule
    *
    * \return An array of SubModule objects
  */
  public function getSubModules(){return $this->subModules;}

  /** \brief Function to get all the submodules refering to this MainModule, and the SubModules referging to thoses SubModules
    *
    * \return An array of SubModule objects
  */
  public function getMainAndSubInstances(){

    $instances = $this->getInstances();

    foreach($this->getSubModules() as $subModule){

      $instances = array_merge(instances, $subModule->getInstances());
    }

    return $instances;
  }

    
}













/** \brief Object used to define a submodule (a submodule is always refering to a Module)
  *
  * A submodule refers to one MainModule, but a MainModule can have multiple submodules
  */
class SubModule extends Module{

	private $parentModule; /**< The Module to which this SubModule refers to */

  /** \brief Constructor of the SubModule object.
    *
    * \param $name The name of this SubModule (as a string).
    * \param $configFolder The folder in wich we can find the definition of the module (and so read the arguments that need to be displayed to the user)
    * \param $parentModule The MainModule to which this submodule refers to.
  */
	function __construct($name, $configFolder, $parentModule){

		$this->name = $name;
		$this->pathToConfigFolder = $configFolder;
		$this->pathToConfigFile = $configFolder."/".$name.".pm";
		$this->parentModule = $parentModule;
		$this->readConfigurationFile();
        
    $this->addArgument(new AfterArg(NULL));
	}
}






















/** \brief Object used to define an instance of a module or submodule.
  *
  * A configuration of a server is constituded of objects Instance of modules and submodules
  */
class Instance{

  private $name;            /**< The name of this instance */
  private $arguments;       /**< An array of Arguments refering to this Instance */
  private $hasBeenWritten;  /**< Boolean telling if this Instance have already been written to Rexify */
  private $afterObjects;    /**< An array of Instances after which this Instance must be writtent in the Rexify */
  private $motherModule;    /**< The module or submodule from which this object is an instance */

  /** \brief Constructor of the Instance object.
    *
    * \param $name The name of this instance (as a string).
    * \param $arguments The arguments that define the instance (as an array of 2 strings : the name of the argument, then it's value.).
    * \param $motherModule The module (or submodule) that we are instanciating.
  */
  function __construct($name, $arguments, $motherModule){

    $this->hasBeenWritten = false;
    $this->name = $name;
    $this->motherModule = $motherModule;
    $this->arguments = array();


    if( (isset($arguments)) && ($arguments != NULL) ){

      $this->createArguments($arguments);
    }

    $this->afterObjects = array();
  }

  /** \brief Function creating an argument for this instance.
    *
    * \param $arguments The arguments to create (as an array of 2 strings : the name of the argument, then it's value.).
  */
  private function createArguments($arguments){

    foreach($arguments as $argument){

      $argName = $argument[0];
      $argObject = $this->motherModule->getArgument($argName);
      $this->arguments[] = createObjectArgumentFromString($argObject, $argument);
    }
  }

  /** \brief Function returning the name of the current instance
    *
    * \return The name of the current instance as a string.
  */
  public function getName(){return $this->name;}
  /**
   * \brief Function used to set the name of this instance
   *
   * \param $name The name we want this instance to have from now on, as a string.
   */
  public function setName($name){$this->name = $name;}
  /** \brief Function returning the arguments of the current instance
    *
    * \return The arguments of the current instance as an array of Argument objects.
  */
  public function getArguments(){return $this->arguments;}
  /** \brief Function returning an argument of the current instance
    *
    * \param $name The name of the argument we want to get
    * \return The value of arguments we asked as a string.
  */
  public function getArgument($name){

    foreach($this->arguments as $argument){

      if($argument->getName() == $name){

        return $argument->getValue();
      }
    }
    return "";
  }
  /** \brief Function returning an argument of the current instance
    *
    * \param $name The name of the argument we want to get
    * \return The value of arguments we asked as a Argument object.
  */
  public function getArgumentObject($name){

    foreach($this->arguments as $argument){

      if($argument->getName() == $name){

        return $argument;
      }
    }
    return NULL;
  }
  /** \brief Function setting the arguments of the current instance.
    *
    * We set all the arguments of the instance at once.
    *
    * \param $arguments An array of arguments to set into this instance
  */
  public function setArguments($arguments){$this->arguments = $arguments;}
  /** \brief Function setting an argument of the current instance.
    *
    * We set one of the arguments of the instance.
    *
    * \param $argumentName The name of the argument we want to set (as a string).
    * \param $value The value we want to set the argument to (as what is suttable for this argument type).
  */
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

      $newArgument = new ArrayArg($argumentName, NULL);
      $newArgument->setValue($value);	//$value
      $this->arguments[] = $newArgument;
    }elseif($type === "hash"){

      $hashRef = $this->motherModule->getArgument($argumentName);

      $newArgument = new HashArg($argumentName, $hashRef->gethashDef(), NULL);
      $newArgument->setValue($value);
      $this->arguments[] = $newArgument;
    }

  }

  /** \brief Function returning the current instance as an html form.
  *
  * \return The form input corresponding to this instance, as a string.
  */
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

  /** \brief Function returning the current instance as JSON.
    *
    * \return The instance as a JSON string.
  */
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

  /** \brief Function returning if this instance have been written into to Rexify file
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \return True if it has already been written.
  */
  public function getHasBeenWritten(){return $this->hasBeenWritten;}
  /** \brief Function setting if this instance have been written into to Rexify file
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \param $val True to set it as it has been written, else false.
  */
  public function setHasBeenWritten($val){$this->hasBeenWritten = $val;}

  /** \brief Function returning the instance after wich it must be written
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \return The object after wich this instance must be written (as an array of Instance objects).
  */
  public function getAfterObjects(){return $this->afterObjects;}
  /** \brief Function adding an instance to the ones this one must be written after
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \param $object The object after wich this instance must be written (as an Instance object).
  */
  public function addAfterObject($object){$this->afterObjects[] = $object;}
  /** \brief Function adding instances to the ones this one must be written after
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \param $objects The objects after wich this instance must be written (as an array of Instance objects).
  */
  public function addAfterObjects($objects){$this->afterObjects = array_merge($this->afterObjects, $objects);}
  /** \brief Function telling if this Instance is ready to be written into Rexify
    *
    * Function used in order to specify the order in wich the instances must be written into the Rexify
    *
    * \return True is all the object after wich this one must be written have already been written to Rexify, false else.
  */
  public function isReadyToBeWritten(){

    foreach($this->afterObjects as $object){

      if($object->getHasBeenWritten() == false){return false;}
    }

    return true;
  }
}



?>
