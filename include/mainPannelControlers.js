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
	};

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
	};

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
	};
}


/**
* Controler of the tabs in the main pannel when displaying a multi tabs module/user/page
*
* @class tabsControler
*/
function tabsControler($scope, $http, $sce){

	$scope.tabs = [];
	$scope.activeSubModule = "";
	$scope.instancesList = [];

  /**
   * Methode removing all the tabs
   *
   * @method clearTabs
   */
	$scope.clearTabs = function(){

		$scope.tabs = [];
	};


  /**
   * Methode adding a tab to the ones displayed
   *
   * @method addTab
   * @param {String} title The name of the tab to add
   * @param {String} content The content of the tab (as html code)
   */
	$scope.addTab = function(title, content){

		$scope.tabs.push({"title": title, "content": content});
	};


  /**
   * Event called to update the current tab content
   *
   * @event updateCurrentTab
   */
  $scope.$on('updateCurrentTab', function(event, args){

    $scope.changeTab();
  });


  /**
   * Methode displaying the configuration of a module
   *
   * @method changeTab
   * @param {Tab} tab The tab to display (JSON containing {name, content})
   */
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

	};

  /**
   * Methode caled when we click on a "add new" button in a submodule configuration. This methode
   * is going to get the form for a new instance of the current submodule
   *
   * @method add
   */
  $scope.add = function(){

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', ["config", $scope.activeModule, $scope.activeSubModule, "Add new"]);
		}, 10);
  };

  /**
   * Methode caled when we click on an instance of a submodule configuration. This methode
   * is going to get the form of the instance selected
   *
   * @method click
   * @param {Instance} instance The instance on which we clicked (we only use the name parameter of this object)
   */
	$scope.click = function(instance){

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', ["config", $scope.activeModule, $scope.activeSubModule, instance.name]);
		}, 10);
	};
}


/**
* Controler of the tabs in the main pannel when displaying a single tab interface (like the home page or a user)
*
* @class unicTabCtrl
*/
function unicTabCtrl($scope, $http){

	$scope.page = "home";
	$scope.home = "Home page";


  /**
   * Methode caled when we click on the logo or at the loading of the page to load the home page
   *
   * @method loadHome
   */
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
	};

  /**
   * Methode caled to load the form of a module that don't needs any tabs (like a user)
   *
   * @method loadInstance
   * @param {String} activePage The current par of the interface we are in (config/home)
   * @param {String} activeModule The current module we are in
   * @param {String} activeSubModule The current submodule we are in
   * @param {String} activeInstance The current instance we are in
   */
	$scope.loadInstance = function(activePage, activeModule, activeSubModule, activeInstance){

		$scope.page = "form";

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', [activePage, activeModule, activeSubModule, activeInstance]);
		}, 10);
	};
}







