<?php
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
    
    $subModulesClear = array();
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


function readArgument($string){
    

	if(preg_match('/\{.*?\}/', $string, $matches)){	//if we have a hash

    $fullHash = $matches[0];
    $fullHash = substr($fullHash, 1, -1);

    //TODO dont split if the "," is in a string or something else. Or a hash of hashes
    $splitHash = explode(",", $fullHash);

    $array = array();
    foreach($splitHash as $element){

      $splitSubArg = explode("=>", $element);

      if(isset($splitSubArg[1])){	//if we got an arg, and not an empty value

        $subArgName = $splitSubArg[0];
        $subArgValue = $splitSubArg[1];

        $subArgName = readArgument($subArgName);
        $subArgValue = readArgument($subArgValue);

        $array[$subArgName] = $subArgValue;
      }
    }

    return $array;

   }elseif(preg_match('/\[.*?\]/', $string, $matches)){   //if we have an array
        
        $fullArray = $matches[0];
        $fullArray = substr($fullArray, 1, -1);
        
        //TODO dont split if the "," is in a string or something else. Or an array of arrays.
        $splitArray = explode(",", $fullArray);
        
        $array = array();
        foreach($splitArray as $element){
            
            $array[] = readArgument($element);
        }

        return $array;
        
    }else{

        //we get the value (on the right of the => sign)
        preg_match_all('/".*?"|\'.*?\'/', $string, $matches);
        if(count($matches[0]) == 0) $argument = [];
        else{

          $argument = $matches[0][0];
          //we remove the ' and ", first and last char of the string
          //$argument[0] = substr($argument[0], 1, -1);
          $argument = substr($argument, 1, -1);
        }
        
        return $argument;
    }
    
    //return NULL;
}




?>
