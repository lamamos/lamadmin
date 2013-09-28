<?

abstract class Argument{

    protected $type;
    protected $name;

    
    abstract protected function toForm();
    abstract protected function toConfigFile();
    
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
    
    public function toConfigFile(){
        
        
        return "'".$this->name."' => '".$this->value."',";
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
    
    public function toConfigFile(){
        
        
        return "'".$this->name."' => '".$this->value."',";
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
    
    public function toConfigFile(){
        
        return "'".$this->name."' => '".$this->value."',";
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
    
    public function toConfigFile(){
        
        $response = "";
        
        if($this->value){$response = "'".$this->name."' => '1',";}
        else{$response = "'".$this->name."' => '0',";}
        
        return $response;
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
        $this->value = $value;
    }
    
    
    public function toForm(){
        
        $form = "";
        //foreach($this->value as $element){
        for($i=0; $i<count($this->value); $i++){
            
            $element = $this->value[$i];
            $form .= $element->toForm();
            $form .= "<input type=\"button\" class=\"removeElementFromArray\" name=\"remove_".$this->name."[".$i."]\" value=\"-\">";
        }
        
        $form .= "<input type=\"button\" class=\"addElementToArray\" name=\"".$this->name."\" value=\"+\">";
        
        return $form;
    }
    
    public function toConfigFile(){
        
        $response = "'".$this->name."' => [";
        
        
        foreach($this->value as $element){
         
            $response .= $element->toConfigFileArg().", ";
        }
        $response = substr($response, 0, -2);   //we remove the laste space and coma
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

        for($i=0; $i<count($this->value); $i++){
                
            $val = $this->value[$i];
            $val->setValue($value[$i]);
        }
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
            
            //$stringArg[0] = $argName."_".$i;
            $stringArg[0] = $argName."[".$i."]";
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