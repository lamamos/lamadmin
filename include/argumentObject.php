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



?>