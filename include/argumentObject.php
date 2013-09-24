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

?>