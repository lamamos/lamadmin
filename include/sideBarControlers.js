var app = angular.module('lamadmin', ['ui.bootstrap']);


function sideBarCtrl($scope){

	$scope.selectedLine = "";

	$scope.getClass = function(name){

		if($scope.selectedLine == name)return "moduleSelected";
		else return "";
	}

	$scope.displayHome = function(){

		$scope.selectedLine = "";
		angular.element($("#mainPannel")).scope().loadHome();
	}

	$scope.addUser = function(){

		activePage = "config";
		activeModule = "user";
		activeSubModule = "";
		activeInstance = "Add new";

		$scope.selectedLine = "Add new";

		angular.element($("#mainPannel")).scope().loadUser("Add new");
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

		angular.element($("#mainPannel")).scope().loadModul(module.name);
    }

}





