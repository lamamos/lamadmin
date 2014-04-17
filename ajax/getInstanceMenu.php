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

$response = "<ul class=\"menu\">";

foreach($config->getModules() as $module){
    
    $response .= "<li><a href\"javascript:void(0)\">".$module->getName()."</a>";
    
    if(count($module->getSubModules()) > 0){
        $response .= "<ul>";
        foreach($module->getSubModules() as $subModule){
            
            $response .= getSubModuleMenu($subModule, $module->getName());
        }
        $response .= "</ul>";
    }
    
    $response .= "</li>";

}

$response .= "</ul>";

echo $response;








function getSubModuleMenu($subModule, $moduleName){
    
    $menu = "";
    $menu .= "<li><a href\"javascript:void(0)\">".$subModule->getName()."</a>";
    
    if(count($subModule->getInstances()) > 0){
        $menu .= "<ul>";
        foreach($subModule->getInstances() as $instance){
            
            $instanceName = $moduleName."::".$subModule->getName()."::".$instance->getName();
            $menu .= "<li><a href\"javascript:void(0)\" onclick=\"formatInstanceMenu('".$instanceName."');\">".$instance->getName()."</a></li>";
        }
        $menu .= "</ul>";
    }

    $menu .= "</li>";
    
    return $menu;
}



?>
