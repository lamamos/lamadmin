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

		//$this->readSitePP();
	}


	function readSitePP(){

		global $PathToSitePP;

		$lines = file($PathToSitePP);

		$openBrackets = 0;
		$insideABloc = false;

		foreach ($lines as $lineNumber => $lineContent)
		{

			//if we have a line with an opening bracket we count it
			if(preg_match("/{/", $lineContent)){

				$openBrackets++;
				//if it was the first opening bracket then we remove it
				if($openBrackets==1)continue;
			}
			//when we got a closing bracket we discount it
			if(preg_match("/}/", $lineContent)){

				//if we have the last closing bracket we remove it
				if($openBrackets==1)continue;
				$openBrackets--;
			}

			//we remove the first line (with the name of the node
			if(preg_match("/node.*/", $lineContent))continue;
			//we remove all the comments
			$lineContent = preg_replace("/\#.*\\n/", "", $lineContent, -1);
			//we remove all the empty lines
			if(preg_match("/^\s*$/", $lineContent))continue;



			//Now we are gona cut the file in parts. We need to find out if this part is one line or more
			if($insideABloc){		//we are in a bloc

				$blocTemp[] = $lineContent;

				if( preg_match("/}/", $lineContent) && ($openBrackets == 1) ){	//the end of the bloc

					$this->getObjectFromConfig($blocTemp);
					unset($blocTemp);
					$insideABloc = false;
				}

			}else{		//we are not yet in a bloc

				if( preg_match("/{/", $lineContent) && ($openBrackets == 2) ){	//it a multiple line block

					$blocTemp[] = $lineContent;
					$insideABloc = true;
				}else{	//it's a single line

					$this->getObjectFromConfig($lineContent);
				}
			}


			//echo $lineNumber,' ',$lineContent,' ',$openBrackets,'<br>';
		}
	}



	function getObjectFromConfig($config){



		if(is_array($config)){	//it's a bloc
	
			//we get the name of this module
			//we remove every thing until the first {
			$nameConfig = preg_replace("/^.*\{/", "", $config[0], 1);
			//we remove the ' and "
			$nameConfig = preg_replace("/(\"|\')/", "", $nameConfig, -1);
			//we remove the last :
			$nameConfig = preg_replace("/:/", "", $nameConfig, 1);



			//we get the list of arguments of this module
			for($i=1; $i<(count($config)-1);$i++){

				$argument = explode("=>", $config[$i]);
				//we get only wath is between quotes
				preg_match_all('/".*?"|\'.*?\'/', $argument[1], $matches);
				$argument[1] = $matches[0][0];
				//we remove the ' and "
				$argument[1] = preg_replace("/(\"|\')/", "", $argument[1], -1);
				$keyValue[] = $argument;
			}




			//we need to find out if we have a submodule, a module or a user
			//we get the key word befor the first {
			$keyWord = preg_replace("/\{.*:$/", "", $config[0], 1);

			//if we have a submodule (module::submodule)
			if(preg_match("/.+\:\:.+/", $keyWord)){

				$moduleName = preg_replace("/\:\:.*/", "", $keyWord, 1);
				$moduleName = preg_replace("/\s/", "", $moduleName, 1);
				$subModuleName = preg_replace("/.*\:\:/", "", $keyWord, 1);
				$subModuleName = preg_replace("/\s/", "", $subModuleName, -1);

				//echo "<br><br>Le nom du module est : ".$moduleName.", le subModule est : ".$subModuleName."<br><br>";

				//we search for the right module
				$module = $this->getModule($moduleName);
				//we search for the right submodule
				foreach($module->getSubModules() as $subModule){

					if(preg_match("/".$subModule->getName()."/", $subModuleName)){

						$subModule->addInstance(new Instance($nameConfig, $keyValue));
					}
				}

			}else if(preg_match("/user/", $keyWord)){	//we have a user


				$module = $this->getModule("user");
				$module->addInstance(new Instance($nameConfig, $keyValue));

			}else if(preg_match("/class/", $keyWord)){		//we have a module

				$module = $this->getModule($nameConfig);
				$module->addInstance(new Instance($nameConfig, $keyValue));
			}else{		//wtf is that?


				//echo "wtf is that module?";
			}


		}else{		//it's a one line config

			$config = str_replace("include", "", $config);
			$config = str_replace("\s", "", $config);

			//echo $config.count($this->availableModules)."<br><br>";

			//we search the corresponding module
			$module = $this->getModule($config);
			$module->addInstance(new Instance($config, NULL));
		}
	}


	function initialisation(){

		//we create the user module
		$userArguments[] = "ensure";
		$userArguments[] = "uid";
		$userArguments[] = "gid";
		$userArguments[] = "shell";
		$userArguments[] = "home";
		$userArguments[] = "managehome";
		$userModule = new MainModule("user", NULL);
		$userModule->setArguments($userArguments);

		$this->availableModules[] = $userModule;
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