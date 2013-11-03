<?php

abstract class Argument{

    protected $type;
    protected $name;

    
    abstract protected function toForm();
    abstract protected function toJson();
	abstract protected function getEmptyTemplate();
    abstract protected function toConfigFile();
    abstract protected function toConfigFileArg();
    
    public function getType(){return $this->type;}
    public function getName(){return $this->name;}
}



class StringArg extends Argument{

    private $value;
    
    function __construct($name, $value){
    
        $this->type = "string";
        $this->name = $name;
        $this->value = $value;
    }
    
    
    public function toForm(){
        
        return "<input type=\"text\" name=\"".$this->name."\" value=\"".$this->value."\">";
    }

	public function toJson(){

		return "{\"content_type\" : \"string\", \"title\" : \"".$this->name."\", \"value\" : \"".$this->value."\"}";
	}
    
	public function getEmptyTemplate(){

		return "{\"content_type\" : \"string\", \"title\" : \"\", \"value\" : \"\"}";
	}

    public function toConfigFile(){
        
        return "'".$this->name."' => ".$this->toConfigFileArg().",";
    }
    
    public function toConfigFileArg(){
        
        return "'".$this->value."'";
    }
    
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
}




class AfterArg extends Argument{

    private $value;
    
    function __construct($value){
    
        $this->type = "after";
        $this->name = "after";
        $this->value = $value;
    }
    
    
    public function toForm(){
        
        return "<input type=\"text\" class=\"instanceMenu\" name=\"".$this->name."\" value=\"".$this->value."\">";
    }

	public function toJson(){

		return "{\"content_type\" : \"after\", \"title\" : \"".$this->name."\", \"value\" : \"".$this->value."\"}";
	}

	public function getEmptyTemplate(){

		return "{\"content_type\" : \"after\", \"title\" : \"\", \"value\" : \"\"}";
	}
    
    public function toConfigFile(){
        
        
        return "'".$this->name."' => ".$this->toConfigFileArg().",";
    }
    
    public function toConfigFileArg(){
        
        return "'".$this->value."'";
    }
    
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
}



class NumberArg extends Argument{

    private $value;
    
    function __construct($name, $value){
    
        $this->type = "number";
        $this->name = $name;
        $this->value = $value;
    }
    
    
    public function toForm(){
        
        return "<input type=\"text\" name=\"".$this->name."\" value=\"".$this->value."\" style=\"background-color:#82ff5d;\">";
    }

	public function toJson(){

		return "{\"content_type\" : \"number\", \"title\" : \"".$this->name."\", \"value\" : \"".$this->value."\"}";
	}

	public function getEmptyTemplate(){

		return "{\"content_type\" : \"number\", \"title\" : \"\", \"value\" : \"\"}";
	}
    
    public function toConfigFile(){
        
        return "'".$this->name."' => ".$this->toConfigFileArg().",";
    }
    
    public function toConfigFileArg(){
        
        return "'".$this->value."'";
    }
    
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
}



class BoolArg extends Argument{

    private $value;
    
    function __construct($name, $value){
    
        $this->type = "bool";
        $this->name = $name;
        $this->value = $value;
    }
    
    
    public function toForm(){
        
        $response = "<input type=\"hidden\" name=\"".$this->name."\" value=\"0\" /> <input type=\"checkbox\" name=\"".$this->name."\" value=\"1\"";           
        if($this->value){$response .= " checked";}
        $response .= "/>";

        return $response;
    }

	public function toJson(){

		return "{\"content_type\" : \"bool\", \"title\" : \"".$this->name."\", \"value\" : \"".$this->value."\"}";
	}

	public function getEmptyTemplate(){

		return "{\"content_type\" : \"bool\", \"title\" : \"\", \"value\" : \"\"}";
	}
    
    public function toConfigFile(){
        
        return "'".$this->name."' => ".$this->toConfigFileArg().",";
    }
    
    public function toConfigFileArg(){
        
        if($this->value){return "'1'";}
        else{return "'0'";}
    }
    
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
}



class ArrayArg extends Argument{

    private $subType;
    private $value;
    
    function __construct($name, $subType, $value){
    
        $this->type = "array";
        $this->name = $name;
        $this->subType = $subType;

        //$this->value = $value;
        $this->value[0] = new StringArg("test", $value[0]);

		//foreach($value as $val){

			//$this->value[] = createObjectArgumentBasic($subType, ["", $val]);
/*
function createObjectArgumentBasic($type, $string){
    
    $argName = $string[0];
    $argVal = $string[1];*/
			
		//}
    }
    
    
    public function toForm(){
        
        $form = "";
        for($i=0; $i<count($this->value); $i++){
            
            $element = $this->value[$i];
            $form .= $element->toForm();
            $form .= "<input type=\"button\" class=\"removeElementFromArray\" name=\"remove_".$this->name."[".$i."]\" value=\"-\">";
        }
        
        $form .= "<input type=\"button\" class=\"addElementToArray\" name=\"".$this->name."\" value=\"+\">";
        
        return $form;
    }

	public function toJson(){

		$response = "{\"content_type\" : \"array\", \"title\" : \"".$this->name."\", \"subType\" : \"".$this->getEmptyTemplate()."\", \"value\" : [";

		if(isset($this->value)){

		    for($i=0; $i<count($this->value); $i++){
		        
		        $element = $this->value[$i];
		        $response .= $element->toJson();
				$response .= ",";
		    }
			$response = substr($response, 0, -1); //remove the last useless ","
		}

		$response .= "]}";

		return $response;
	}

	public function getEmptyTemplate(){

		$template = $this->subType->getEmptyTemplate();

		$template = str_replace ("\"", "\\\"", $template);

		return $template;
	}
    
    public function toConfigFile(){
        
        return "'".$this->name."' => ".$this->toConfigFileArg();
    }
    
    public function toConfigFileArg(){
        
        $response = "[";
        
		if(count($this->value)){	//if we have args in this array

		    foreach($this->value as $element){
		     
		        $response .= $element->toConfigFileArg().", ";
		    }
		    $response = substr($response, 0, -2);   //we remove the laste space and coma
		}
        $response .= "],";
        
        return $response;
    }
    
    public function createNewElement(){
        
        $name = $this->name."[".count($this->value)."]";
        $element = createObjectArgumentBasic($this->subType, [$name, ""]);
        $this->value[] = $element;
        
        return $element;
    }
    public function removeElementNum($num){
        
        unset($this->value[$num]);
    }
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getSubType(){return $this->subType;}
    public function getValue(){return $this->value;}
    public function setValue($value){

		$array = [];
        for($i=0; $i<count($value); $i++){

			$array[] = createObjectArgumentBasic($value[$i]['content_type'], [$value[$i]['title'], $value[$i]['value']]);
        }

		$this->value = $array;
    }
}




class HashArg extends Argument{

    private $subType;
    private $value;
    
    
    private $hashDef;
    
    function __construct($name, $hashDefinition){
    
        $this->type = "hash";
        $this->name = $name;
        $this->createHashValue($hashDefinition);        
        
        $this->hashDef = $hashDefinition;
    }
    
    function createHashValue($hashDefinition){
        
        for($i = 0; $i<count($hashDefinition); $i=$i+2){
            
            $type = $hashDefinition[$i];
            $name = $hashDefinition[$i+1];
            
            $this->value[] = createObjectArgumentBasic($type, [$name, NULL]);
        }
    }

    public function toForm(){
        
        $form = "";
        
        foreach($this->value as $element){
            
            $form .= $element->getName()." : ".$element->toForm();            
        }
        
        return $form;
    }
    
	public function toJson(){

		$response = "{\"content_type\" : \"hash\", \"title\" : \"".$this->name."\", \"stuffs\" : {";


        foreach($this->value as $element){
            
            $response .= "\"".$element->getName().":".$element->toForm().",";            
        }

		$response = substr($response, 0, -1); //remove the last useless ","
		$response .= "}}";

		return $response;
	}

	public function getEmptyTemplate(){

		return "dafuk";
	}

    public function toConfigFile(){
        
        //return "'".$this->name."' => ".$this->toConfigFileArg();
    }
    
    public function toConfigFileArg(){
        
        /*$response = "[";
        
        foreach($this->value as $element){
         
            $response .= $element->toConfigFileArg().", ";
        }
        $response = substr($response, 0, -2);   //we remove the laste space and coma
        $response .= "],";
        
        return $response;*/
    }
    
    public function createNewElement(){
        
        /*$name = $this->name."[".count($this->value)."]";
        $element = createObjectArgumentBasic($this->subType, [$name, ""]);
        $this->value[] = $element;
        
        return $element;*/
    }
    public function removeElementNum($num){
        
        //unset($this->value[$num]);
    }
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getSubType(){return $this->subType;}
    public function getValue(){return $this->value;}
    public function setValue($value){

        /*for($i=0; $i<count($this->value); $i++){
                
            $val = $this->value[$i];
            $val->setValue($value[$i]);
        }*/
    }
}









function createObjectArgumentFromString($argObject, $string){
    
    $argName = $string[0];
    $argVal = $string[1];
    $type = $argObject->getType();
    
    if($type === "array"){
            
        $subType = $argObject->getSubType();
        $subArray = array();
        for($i=0; $i<count($argVal); $i++){
            
            $subElement = $argVal[$i];
            
            //$stringArg[0] = $argName."[".$i."]";
			$stringArg[0] = "";
            $stringArg[1] = $subElement;
            $subArray[] = createObjectArgumentBasic($subType, $stringArg);
        }
        
        $object = new ArrayArg($argName, $subType, $subArray);
        return $object;
    }else{
        
        return createObjectArgumentBasic($type, $string);        
    }
    
    return NULL;
}



//TODO : make this function work with arrays and hash (recursively)
//TODO : protect it from NULL putted in $string (for an empty arg object)
function createObjectArgumentBasic($type, $string){
    
    $argName = $string[0];
    $argVal = $string[1];
            
    if($type === "string"){
        
        $object = new StringArg($argName, $argVal);
        return $object;
    }elseif($type === "after"){
        
        $object = new AfterArg($argVal);
        return $object;
    }elseif($type === "number"){
            
        $object = new NumberArg($argName, $argVal);
        return $object;
    }elseif($type === "bool"){
                    
        $object = new BoolArg($argName, $argVal);
        return $object;
    }
    
    return NULL;
}



?>
