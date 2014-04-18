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
//to launch in the include folder with the command : phpunit ../tests/BooleanArgumentTest.php
//need to have the package php5-json installed to work

require_once("../include/argumentObject.php");

class BooleanArgumentTest extends PHPUnit_Framework_TestCase
{

  public function provider()
  {
    return array(
      array("test", 1, TRUE),
      array("bernard15", 0, FALSE),
      array("_bertaluçe", "true", TRUE),
      array("_bertaluçe", "false", FALSE)      
    );
  }


  /**
   * @dataProvider provider
   */
  public function testToForm($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    if($real_value)$expected = "<input type=\"hidden\" name=\"".$name."\" value=\"0\" /> <input type=\"checkbox\" name=\"".$name."\" value=\"1\" checked/>";
    else $expected = "<input type=\"hidden\" name=\"".$name."\" value=\"0\" /> <input type=\"checkbox\" name=\"".$name."\" value=\"1\"/>";

    $actual = $BoolArgument->toForm();

    $this->assertStringMatchesFormat($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToJson($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    $expected = "";
    if($real_value)$expected = "{\"content_type\" : \"bool\", \"title\" : \"".$name."\", \"value\" : true}";
    else $expected = "{\"content_type\" : \"bool\", \"title\" : \"".$name."\", \"value\" : false}";
    $actual = $BoolArgument->toJson();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetEmptyTemplate($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    $expected = "{\"content_type\" : \"bool\", \"title\" : \"\", \"value\" : \"\"}";
    $actual = $BoolArgument->getEmptyTemplate();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToConfigFile($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    $expectedValue = "5";
    if($real_value) $expectedValue = "1";
    else $expectedValue = "0";

    $expected = "%w'".$name."'%w=>%w'".$expectedValue."'%w,%w"; // %w stands for 0 or more white space
    $actual = $BoolArgument->toConfigFile();

    $this->assertStringMatchesFormat($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetName($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    $expected = $name;
    $actual = $BoolArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetName
   */
  public function testSetName($name, $value, $real_value){

    $BoolArgument = new BoolArg("test", $value);
    $BoolArgument->setName($name);

    $expected = $name;
    $actual = $BoolArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetValue($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, $value);

    $expected = $real_value;
    $actual = $BoolArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetValue
   */
  public function testSetValue($name, $value, $real_value){

    $BoolArgument = new BoolArg($name, "test");
    $BoolArgument->setValue($value);

    $expected = $real_value;
    $actual = $BoolArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

}

?>
