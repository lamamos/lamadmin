<?php

//test file for phpunit
//to launch in the include folder with the command : phpunit ../tests/StringArgumentTest.php
//need to have the package php5-json installed to work

require_once("../include/argumentObject.php");

class StringArgumentTest extends PHPUnit_Framework_TestCase
{

  public function provider()
  {
    return array(
      array("roger", "value"),
      array("bernard15", "5val1u"),
      array("_bertaluçe", "vamérô")
    );
  }


  /**
   * @dataProvider provider
   */
  public function testToForm($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = "<input type=\"text\" name=\"".$name."\" value=\"".$value."\"/>";
    $actual = $stringArgument->toForm();

    $this->assertXmlStringEqualsXmlString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToJson($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = "{\"content_type\" : \"string\", \"title\" : \"".$name."\", \"value\" : \"".$value."\"}";
    $actual = $stringArgument->toJson();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetEmptyTemplate($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = "{\"content_type\" : \"string\", \"title\" : \"\", \"value\" : \"\"}";
    $actual = $stringArgument->getEmptyTemplate();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToConfigFile($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = "%w'".$name."'%w=>%w'".$value."'%w,%w"; // %w stands for 0 or more white space
    $actual = $stringArgument->toConfigFile();

    $this->assertStringMatchesFormat($expected, $actual);
  }


/*

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

*/



}

?>
