var app = angular.module('lamadmin', []);




function userListCtrl($scope, $http) {

	$scope.userList = [];
	$scope.selectedUser = "";

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

		$scope.selectedUser = user.name;

		displayUser(user.name);
        }


	$scope.getClass = function(user){

		if($scope.selectedUser == user.name)return "moduleSelected";
		else return "";
	}
}








function serviceListCtrl($scope, $http) {

	$scope.moduleList = [];
	$scope.selectedService = "";


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

	/*$scope.click = function(user) {

		activePage = "config";
		activeModule = "user";
		activeSubModule = "";
		activeInstance = user.name;

		$scope.selectedUser = user.name;

		displayUser(user.name);
        }


	$scope.getClass = function(user){

		if($scope.selectedUser == user.name)return "moduleSelected";
		else return "";
	}*/
}





