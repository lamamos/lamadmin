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


//test file for phpunit
//to launch at the root of the repo with the command : phpunit tests/ArrayArgumentTest.php
//need to have the package php5-json installed to work

require_once("include/argumentObject.php");

class ArrayArgumentTest extends PHPUnit_Framework_TestCase
{

  public function provider()
  {
    return array(
      array("roger", new StringArg("", ""), [new StringArg("test1", "value1"), new StringArg("test2", "value2")])
      //array("bernard15", "string", "5val1u"),
      //array("_bertaluçe", "string", "vamérô")
    );
  }

  /**
   * @dataProvider provider
   */
  public function testGetEmptyTemplate($name, $subtype, $value){

    $ArrayArgument = new ArrayArg($name, $subtype, $value);

    $expected = $subtype->getEmptyTemplate();

    $actual = $ArrayArgument->getEmptyTemplate();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetEmptyTemplate
   */
  public function testToJson($name, $subtype, $value){

    $ArrayArgument = new ArrayArg($name, $subtype, $value);

    $emptyTemplate = $ArrayArgument->getEmptyTemplate();
    $emptyTemplate = str_replace ("\"", "\\\"", $emptyTemplate);

    $expected = "{\"content_type\" : \"array\", \"title\" : \"".$name."\", \"subType\" : \"".$emptyTemplate."\", \"value\" : [";

    for($i=0; $i<count($value); $i++){
        
      $element = $value[$i];
      $expected .= $element->toJson();
      $expected .= ",";
    }
    $expected = substr($expected, 0, -1); //remove the last useless ","
    $expected .= "]}";


    $actual = $ArrayArgument->toJson();


    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToConfigFile($name, $subtype, $value){

    $ArrayArgument = new ArrayArg($name, $subtype, $value);

    $expectedValues = "%w[%w";
    foreach($value as $element) $expectedValues .= "%w".$element->toConfigFileArg()."%w,";
    $expectedValues = substr($expectedValues, 0, -1);   //we remove the last coma
    $expectedValues .= "%w]%w";


    $expected = "%w'".$name."'%w=>%w".$expectedValues."%w,%w"; // %w stands for 0 or more white space
    $actual = $ArrayArgument->toConfigFile();

    $this->assertStringMatchesFormat($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetName($name, $subtype, $value){

    $ArrayArgument = new ArrayArg($name, $subtype, $value);

    $expected = $name;
    $actual = $ArrayArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetName
   */
  public function testSetName($name, $subtype, $value){

    $ArrayArgument = new ArrayArg("test", $subtype, $value);
    $ArrayArgument->setName($name);

    $expected = $name;
    $actual = $ArrayArgument->getName();

    $this->assertEquals($expected, $actual);
  }

}

?>
