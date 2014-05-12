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

var app = angular.module('lamadmin', ['ui.bootstrap', 'pascalprecht.translate']);

/**
* Controler of the side barre of the application.
*
* @class sideBarCtrl
*/
function sideBarCtrl($scope, $translate){

	$scope.selectedLine = "";
	$scope.selectedType = "";

  /**
   * Methode returning the class of a line of the side bare. If it needs to be overlayed
   * becose it is selected of not.
   *
   * @method getClass
   * @param {String} type The type of the line (User or Service)
   * @param {String} name The name of the line (name of the Service or the User)
   * @return {String} Class to add to the line of the side bar.
   */
	$scope.getClass = function(type, name){

		if( ($scope.selectedLine == name) && ($scope.selectedType == type) )return "moduleSelected";
		else return "";
	}

	/**
	 * Methode loading the home page (called when click on the logo in top left corner)
	 *
	 * @method displayHome
	 */
	$scope.displayHome = function(){

		$scope.selectedLine = "";
		angular.element($("#mainPannel")).scope().loadHome();
	}

	/**
	 * Methode who gets a blank form to create anew user
	 *
	 * @method addUser
	 */
  $scope.addUser = function(){

    activePage = "config";
    activeModule = "user";
    activeSubModule = "";
    activeInstance = "Add new";

    $scope.selectedLine = "Add new";
    $scope.selectedType = "User";

    angular.element($("#mainPannel")).scope().loadUser("Add new");
  }

  /**
   * Methode called when clicking on a flag to change the language of the application
   *
   * @method changeLanguage
   */
	$scope.changeLanguage = function(language){

		$translate.use(language);
	}
}






function userListCtrl($scope, $http){

	$scope.userList = [];

	$scope.update = function(){

		var donnees = $.param({moduleName: "user"});

		$http({
			method: "POST",
			url: "/ajax/getListInstances.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				$scope.userList = response;
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the users");
			})
		;
	}


	$scope.click = function(user) {

		activePage = "config";
		activeModule = "user";
		activeSubModule = "";
		activeInstance = user.name;

		$scope.$parent.selectedLine = user.name;
		$scope.$parent.selectedType = "User";

		angular.element($("#mainPannel")).scope().loadUser(user.name);
    }


	$scope.$on('updateUsersList', function(event, args){

			$scope.update();
	});



	$scope.update();
}








function serviceListCtrl($scope, $http) {

	$scope.moduleList = [];

	$http({
		method: "POST",
		url: "/ajax/getListServices.php",
		headers: {'Content-Type': 'application/x-www-form-urlencoded'}
	})
		.success(function(response){

			$scope.moduleList = response;
		})

		.error(function(data, status, headers, config) {

			alert("error when getting the liste of the services");
		})
	;

	$scope.clickBoolean = function(module){

		module.activated = !module.activated;

		var donnees = $.param({moduleToggled: module.name});

		$http({
			method: "POST",
			url: "/ajax/toggleModule.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){})

			.error(function(data, status, headers, config) {

				alert("error while changing the state of the module");
			})
		;

		if( (activeModule == module.name) && (module.activated == false) ){

			$scope.$parent.selectedLine = "";
		  $scope.$parent.selectedType = "";
    	angular.element($("#mainPannel")).scope().loadHome();
		}

	}


	$scope.click = function(module) {

		if(!module.activated)return;

		activePage = "config";
		activeModule = module.name;
		activeSubModule = "";
		activeInstance = "";

		$scope.$parent.selectedLine = module.name;
		$scope.$parent.selectedType = "Service";

		angular.element($("#mainPannel")).scope().loadModul(module.name);
    }

}





