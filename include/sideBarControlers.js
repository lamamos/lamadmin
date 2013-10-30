var app = angular.module('lamadmin', ['ui.bootstrap']);


function sideBarCtrl($scope){

	$scope.selectedLine = "";

	$scope.getClass = function(module){

		if($scope.selectedLine == module.name)return "moduleSelected";
		else return "";
	}
}




function userListCtrl($scope, $http){

	$scope.userList = [];

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

	$scope.click = function(user) {

		activePage = "config";
		activeModule = "user";
		activeSubModule = "";
		activeInstance = user.name;

		$scope.$parent.selectedLine = user.name;

		angular.element($("#mainPannel")).scope().loadUser(user.name);
    }
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





