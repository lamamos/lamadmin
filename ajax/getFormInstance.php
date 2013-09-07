<?
/*
Copyright (c) 2013 Clement Roblot


This file is part of puppadmin.

puppadmin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

puppadmin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Generateur de photo d'identite. If not, see <http://www.gnu.org/licenses/>.
*/

include_once("../include/configObject.php");
include_once("../include/createConfig.php");

//TODO : don't regenerate the config, use the one witch is registred in the session variables
$config = new Configuration();

$module = $config->getModule($_POST['moduleName']);
$subModule = $module->getSubModule($_POST['subModuleName']);
$instance = $subModule->getInstance($_POST['instanceName']);


$response = "salut il y a : ".count($instance->getArguments())." arguments.<br>";
$response .= "<form onsubmit=\"return 0\" method=\"post\">";

foreach($instance->getArguments() as $argument){

// First name: <input type="text" name="firstname"><br>

	$response .= $argument[0]." : <input type=\"text\" name=\"".$argument[0]."\" value=\"".$argument[1]."\"><br>";

}
$response .= "<input type=\"submit\" value=\"Save\">";
$response .= "</form>";

echo $response;


?>
