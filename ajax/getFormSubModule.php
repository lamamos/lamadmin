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


//we have two type of requests : general for the mainModule configuration, and subModule for the config of the submodule or the instances
if($_POST['subModuleName'] == "general"){	//we need to generate the config of the mainModule

    if($module->isActivated()){
        
        //We are considering the the Mainmodule may only be loaded instanciated once.
        $instance = $module->getInstances()[0];
        $response = $instance->toForm();
    }


}else{		//we need to generate the config of the submodule or it's instances

	$subModule = $module->getSubModule($_POST['subModuleName']);

	$response = "<select name=\"select".$_POST['subModuleName']."\" class=\"instanceSelector\" size=\"10\">";

	foreach($subModule->getInstances() as $instance){

		$response .= "<option>".$instance->getName()."</option>";
	}
	$response .= "<option>Add new</option>";
	$response .= "</select>";
}


echo $response;


?>
