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

        $a = new StringArg($name, $value);

        $expected = "<input type=\"text\" name=\"".$name."\" value=\"".$value."\"/>";
        $actual = $a->toForm();

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }


    /**
     * @dataProvider provider
     */
    public function testToJson($name, $value){

        $a = new StringArg($name, $value);

        $expected = "{\"content_type\" : \"string\", \"title\" : \"".$name."\", \"value\" : \"".$value."\"}";
        $actual = $a->toJson();

        $this->assertJsonStringEqualsJsonString($expected, $actual);
    }


}

?>
