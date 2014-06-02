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


/**
* Controler of the main pannel of the page (central space).
*
* @class mainPannelCtrl
*/
function mainPannelCtrl($scope, $http, $sce, $compile){

	$scope.needTabs = false;
	//may be useless
	$scope.unicTabContent = "<div ng-controller=\"formCtrl\">salut</div>";

	$scope.activeModule = "";


  /**
   * Methode displaying the home page (with the instruction on how tu use lamamos)
   *
   * @method loadHome
   */
	$scope.loadHome = function(){

		$scope.needTabs = false;
		$scope.activeModule = "home";
		angular.element($("#unicTab")).scope().loadHome();
	}

  /**
   * Methode displaying the configuration of a user
   *
   * @method loadUser
   * @param {String} name The name of the user we want to see the configuration
   */
	$scope.loadUser = function(name){

		$scope.needTabs = false;
		$scope.activeModule = "user";
		angular.element($("#unicTab")).scope().loadInstance("config", "user", "", name);
	}

  /**
   * Methode displaying the configuration of a module
   *
   * @method loadModul
   * @param {String} name The name of the module we want to see the configuration
   */
	$scope.loadModul = function(name){

		var donnees = $.param({name: name});

		$http({
			method: "POST",
			url: "/ajax/getListSubModule.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				$scope.needTabs = true;
				$scope.activeModule = name;
				angular.element($("#tabs")).scope().clearTabs();

				var tabs= response.split(",");

				for(i=0; i<tabs.length; i++){

					angular.element($("#tabs")).scope().addTab(tabs[i], "");
				}

			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the list of submodules");
			})
		;
	}
}


function tabsControler($scope, $http, $sce){

	$scope.tabs = [];
	$scope.activeSubModule = "";
	$scope.instancesList = [];

	$scope.clearTabs = function(){

		$scope.tabs = [];
	}

	$scope.addTab = function(title, content){

		$scope.tabs.push({"title": title, "content": content});
	}


	$scope.$on('updateCurrentTab', function(event, args){

    $scope.changeTab();
  });


	$scope.changeTab = function(tab){

		if(tab)$scope.activeSubModule = tab.title;

		if($scope.activeSubModule == "general"){

			//$scope.activeModule is defined in the parent controller : mainPannelCtrl
			//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
			setTimeout(function (){
				$scope.$broadcast('getFormEvent', ["config", $scope.activeModule, $scope.activeSubModule, ""]);
			}, 10);

		}else{


			var donnees =$.param({
				moduleName: activeModule,
				subModuleName: $scope.activeSubModule,
			});

			$http({
				method: "POST",
				url: "/ajax/getListInstances.php",
				data: donnees,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
				.success(function(response){

					$scope.instancesList = response;
				})

				.error(function(data, status, headers, config) {

					alert("error when getting the liste of the list of the submodul instances");
				})
			;
		}

	}


  $scope.add = function(){

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', ["config", $scope.activeModule, $scope.activeSubModule, "Add new"]);
		}, 10);
  }


	$scope.click = function(instance){

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', ["config", $scope.activeModule, $scope.activeSubModule, instance.name]);
		}, 10);
	}
}



function unicTabCtrl($scope, $http){

	$scope.page = "home";
	$scope.home = "Home page";


	$scope.loadHome = function(){

		activePage = "home";
		activeModule = "";
		activeSubModule = "";
		activeInstance = "";

		/*For now we use static content*/
		$scope.page = "home";

		/*To use for latter for dynamic home page*/
		/*$http({
			method: "POST",
			url: "/ajax/getHome.php",
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				$scope.page = "home";
				$scope.home = response;
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the home page");
			})
		;*/
	}


	$scope.loadInstance = function(activePage, activeModule, activeSubModule, activeInstance){

		$scope.page = "form";

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', [activePage, activeModule, activeSubModule, activeInstance]);
		}, 10);
	}
}







