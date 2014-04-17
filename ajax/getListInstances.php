<!--
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
-->

<?php
include_once("../include/configObject.php");
include_once("../include/createConfig.php");

$config = new Configuration();


if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] != NULL) ){
        
    $module = $config->getModule($_POST['moduleName']);
    $subject = $module->getSubModule($_POST['subModuleName']);
}else{

    $subject = $config->getModule($_POST['moduleName']);
}


$response = "[";
if($subject->getInstances() != NULL){
  foreach($subject->getInstances() as $instance){

	  //$response .= $instance->getName().",";
	  $response .= "{\"name\":\"".$instance->getName()."\"},";
  }

  $response = substr($response, 0, -1);
}
$response .= "]";

echo $response;

?>
