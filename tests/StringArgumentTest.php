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
//to launch at the root of the repo with the command : phpunit tests/StringArgumentTest.php
//need to have the package php5-json installed to work

require_once("include/argumentObject.php");

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

  /**
   * @dataProvider provider
   */
  public function testGetName($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = $name;
    $actual = $stringArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetName
   */
  public function testSetName($name, $value){

    $stringArgument = new StringArg("test", $value);
    $stringArgument->setName($name);

    $expected = $name;
    $actual = $stringArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetValue($name, $value){

    $stringArgument = new StringArg($name, $value);

    $expected = $value;
    $actual = $stringArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetValue
   */
  public function testSetValue($name, $value){

    $stringArgument = new StringArg($name, "test");
    $stringArgument->setValue($value);

    $expected = $value;
    $actual = $stringArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

}

?>
