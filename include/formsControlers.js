


function formCtrl($scope, $rootScope, $http){

	/*$scope.content = [
		{"content_type" : "string", "title" : "test3", "value" : ""},

		{"content_type" : "number", "title" : "test4", "value" : "5", "nickname" : "roger"},

		{"content_type" : "array", "title" : "test5", "subType" : "string", "value" : [

			{"content_type" : "string", "title" : "test5-1", "value" : "kikooooo"},
			{"content_type" : "array", "title" : "test5-2", "value" : [

					{"content_type" : "string", "title" : "test5-2-1", "value" : "kikooooo42"},
					{"content_type" : "string", "title" : "test5-2-2", "value" : "kikoooo42"}
				]


			}
		]},

		{"content_type" : "hash", "title" : "test6", "subType" : "string", "value" : {

				"folderName":{"content_type" : "string", "title" : "folderName6-1", "value" : "folder3"},
				"rule":{"content_type" : "number", "title" : "rule6-2", "value" : "600", "nickname" : "roberto ki roxx"}
			}
		}

	];*/

	$scope.content = "";

	$scope.moduleName = "";
	$scope.subModuleName = "";
	$scope.instanceName = "";

	$scope.currentArray = "";


	$scope.$on('getFormEvent', function(event, args){

		$scope.moduleName = args[1];
		$scope.subModuleName = args[2];
		$scope.instanceName = args[3];

		var donnees = $.param({
				moduleName: args[1],
				subModuleName: args[2],
				instanceName: args[3]
		});

		$http({
			method: "POST",
			url: "/ajax/getFormInstance.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				$scope.content = response;
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the form of the user");
			})
		;
	});


	$scope.submit = function(){

		var donnees = $.param({
			moduleName: $scope.moduleName,
			subModuleName: $scope.subModuleName,
			instanceName: $scope.instanceName,
			values: $scope.content
		});

		$http({
			method: "POST",
			url: "/ajax/setFormInstance.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				alert(response);
				if($scope.moduleName == "user")$rootScope.$broadcast("updateUsersList", []);
			})

			.error(function(data, status, headers, config) {

				alert("error when setting the form");
			})
		;
	}



	$scope.delete = function(){

		var donnees = $.param({
				moduleName: $scope.moduleName,
				subModuleName: $scope.subModuleName,
				instanceName: $scope.instanceName
		});

		$http({
			method: "POST",
			url: "/ajax/deleteInstance.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				angular.element($("#mainPannel")).scope().loadHome();
				if($scope.moduleName == "user")$rootScope.$broadcast("updateUsersList", []);
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the form of the user");
			})
		;
	}


	$scope.setArray = function(array){

		$scope.currentArray = array;
	}

	$scope.addNewElement = function(item){

		template = angular.fromJson(item.subType);
		item.value.push(template);
	}

	$scope.deleteItem = function(itemIndex){

		$scope.currentArray.splice(itemIndex, 1);
	}

}


