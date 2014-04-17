<?php

//test file for phpunit
//to launch in the include folder with the command : phpunit ../tests/NumberArgumentTest.php
//need to have the package php5-json installed to work

require_once("../include/argumentObject.php");

class NumberArgumentTest extends PHPUnit_Framework_TestCase
{

  public function provider()
  {
    return array(
      array("test", "5"),
      array("bernard15", "1562"),
      array("_bertaluçe", "-1585")
    );
  }


  /**
   * @dataProvider provider
   */
  public function testToForm($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = "<input type=\"text\" name=\"".$name."\" value=\"".$value."\" style=\"background-color:#82ff5d;\"/>";
    $actual = $NumberArgument->toForm();

    $this->assertXmlStringEqualsXmlString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToJson($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = "{\"content_type\" : \"number\", \"title\" : \"".$name."\", \"value\" : \"".$value."\"}";
    $actual = $NumberArgument->toJson();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetEmptyTemplate($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = "{\"content_type\" : \"number\", \"title\" : \"\", \"value\" : \"\"}";
    $actual = $NumberArgument->getEmptyTemplate();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToConfigFile($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = "%w'".$name."'%w=>%w'".$value."'%w,%w"; // %w stands for 0 or more white space
    $actual = $NumberArgument->toConfigFile();

    $this->assertStringMatchesFormat($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetName($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = $name;
    $actual = $NumberArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetName
   */
  public function testSetName($name, $value){

    $NumberArgument = new NumberArg("test", $value);
    $NumberArgument->setName($name);

    $expected = $name;
    $actual = $NumberArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetValue($name, $value){

    $NumberArgument = new NumberArg($name, $value);

    $expected = $value;
    $actual = $NumberArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetValue
   */
  public function testSetValue($name, $value){

    $NumberArgument = new NumberArg($name, "test");
    $NumberArgument->setValue($value);

    $expected = $value;
    $actual = $NumberArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

}

?>
