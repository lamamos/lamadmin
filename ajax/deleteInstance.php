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

/*
                activeModule: activeModule,
                activeSubModule: activeSubModule,
                instanceName: instanceName,*/

if($_POST['moduleName'] === "user"){
    
    $module = $config->getModule($_POST['moduleName']);    
    $module->deleteInstance($_POST['instanceName']); 
    
}else{
        
    $module = $config->getModule($_POST['moduleName']);
    
    $subModule = $module->getSubModule($_POST['subModuleName']);
    
    $subModule->deleteInstance($_POST['instanceName']);      
}



$config->writeConfigFile($config);

?>
