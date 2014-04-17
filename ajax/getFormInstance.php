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

$module = $config->getModule($_POST['moduleName']);


if($_POST['instanceName'] == "Add new"){    //if we are creating a new instance
    
    if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] != NULL) )$subModule = $module->getSubModule($_POST['subModuleName']);
    else $subModule = $module;
    
	$response = $subModule->toJson();

    
}else{  //if we are editing an existing instance
    
    if( (isset($_POST['subModuleName'])) && ($_POST['subModuleName'] == "general") ){   //if we are editing mainModule with on ly one instance
    
        $subModule = $module;	//This line may be useless
        $instance = $module->getInstances()[0];
        
    }else if( (!isset($_POST['subModuleName'])) || ($_POST['subModuleName'] == "") ){  //if we are editing a mainModule with multiple instances (user for exemple)
        
        $subModule = $module;	//This line may be useless
        $instance = $module->getInstance($_POST['instanceName']);
        
    }else{  //if we are editing a subModule
        
        $subModule = $module->getSubModule($_POST['subModuleName']);
        $instance = $subModule->getInstance($_POST['instanceName']);
    }
    
	$response = $instance->toJson();
}

echo $response;
return;

?>
