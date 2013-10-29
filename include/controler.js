var app = angular.module('lamadmin', []);




function userListCtrl($scope, $http) {

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

}



