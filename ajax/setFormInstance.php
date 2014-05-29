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

include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();


$module = $config->getModule($_POST['moduleName']);


//get the right subModule
if( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "general") || ($_POST['subModuleName'] == "") ){$subModule = $module;}
else{$subModule = $module->getSubModule($_POST['subModuleName']);}

//get the right instance
if($_POST['instanceName'] == "Add new"){    //we are adding a new subModule instance
    
    $instance = new Instance("new_subModule", NULL, $subModule);
    $subModule->addInstance($instance);
    
}elseif( $_POST['subModuleName'] == "general" ){

    $instance = $subModule->getInstances()[0];
    
}else{    //if we are editing a submodul
    
    $instance = $subModule->getInstance($_POST['instanceName']);
}

//$response = "on a ".count($_POST['values'])." arguments :\n\n";

foreach($_POST['values'] as $field){

  //$response .= $field['title']." = ".$field['value'].",\n";


  $instance->setArgument($field['title'], $field['value']);
}


//echo $response;

$config->writeConfigFile($config);

?>
