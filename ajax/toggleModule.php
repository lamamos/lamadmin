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

$module = $config->getModule($_POST['moduleToggled']);

if($module->isActivated()){
    
    $module->clearInstances();
    foreach($module->getSubModules() as $submodule)$submodule->clearInstances();
}else{
    
    $module->addInstance(new Instance($_POST['moduleToggled'], NULL, $module));
}

$config->writeConfigFile($config);

?>
