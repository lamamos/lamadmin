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
//to launch in the include folder with the command : phpunit ../tests/AfterArgumentTest.php
//need to have the package php5-json installed to work

require_once("../include/argumentObject.php");

class AfterArgumentTest extends PHPUnit_Framework_TestCase
{

  public function provider()
  {
    return array(
      array("value"),
      array("5val1u"),
      array("vamérô")
    );
  }


  /**
   * @dataProvider provider
   */
  public function testToForm($value){

    $AfterArgument = new AfterArg($value);

    $expected = "<input type=\"text\" class=\"instanceMenu\" name=\"after\" value=\"".$value."\"/>";
    $actual = $AfterArgument->toForm();

    $this->assertXmlStringEqualsXmlString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToJson($value){

    $AfterArgument = new AfterArg($value);

    $expected = "{\"content_type\" : \"after\", \"title\" : \"after\", \"value\" : \"".$value."\"}";
    $actual = $AfterArgument->toJson();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetEmptyTemplate($value){

    $AfterArgument = new AfterArg($value);

    $expected = "{\"content_type\" : \"after\", \"title\" : \"\", \"value\" : \"\"}";
    $actual = $AfterArgument->getEmptyTemplate();

    $this->assertJsonStringEqualsJsonString($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testToConfigFile($value){

    $AfterArgument = new AfterArg($value);

    $expected = "%w'after'%w=>%w'".$value."'%w,%w"; // %w stands for 0 or more white space
    $actual = $AfterArgument->toConfigFile();

    $this->assertStringMatchesFormat($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetName($value){

    $AfterArgument = new AfterArg($value);

    $expected = "after";
    $actual = $AfterArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetName
   */
  public function testSetName($value){

    $AfterArgument = new AfterArg($value);
    $AfterArgument->setName($value);

    $expected = "after";
    $actual = $AfterArgument->getName();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   */
  public function testGetValue($value){

    $AfterArgument = new AfterArg($value);

    $expected = $value;
    $actual = $AfterArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

  /**
   * @dataProvider provider
   * @depends testGetValue
   */
  public function testSetValue($value){

    $AfterArgument = new AfterArg("test");
    $AfterArgument->setValue($value);

    $expected = $value;
    $actual = $AfterArgument->getValue();

    $this->assertEquals($expected, $actual);
  }

}

?>
