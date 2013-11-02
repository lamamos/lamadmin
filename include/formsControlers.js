


function formCtrl($scope, $http){

	/*$scope.content = [
		{"content_type" : "text", "title" : "test3", "value" : ""},

		{"content_type" : "number", "title" : "test4", "value" : "5", "nickname" : "roger"},

		{"content_type" : "array", "title" : "test5", "subType" : "text", "stuffs" : [

			{"content_type" : "text", "title" : "test5-1", "value" : "kikooooo"},
			{"content_type" : "array", "title" : "test5-2", "stuffs" : [

					{"content_type" : "text", "title" : "test5-2-1", "value" : "kikooooo42"},
					{"content_type" : "text", "title" : "test5-2-2", "value" : "kikoooo42"}
				]


			}
		]},

		{"content_type" : "hash", "title" : "test6", "subType" : "text", "stuffs" : {

				"folderName":{"content_type" : "text", "title" : "folderName6-1", "value" : "folder3"},
				"rule":{"content_type" : "number", "title" : "rule6-2", "value" : "600", "nickname" : "roberto ki roxx"}
			}
		}

	];*/

	$scope.content = "";

	$scope.moduleName = "";
	$scope.subModuleName = "";
	$scope.instanceName = "";


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

				//alert(JSON.stringify(response));
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
			})

			.error(function(data, status, headers, config) {

				alert("error when setting the form");
			})
		;

/*
		$(".instanceForm").submit(function (){

			var data = $(this).serialize();
			data += "&moduleName="+activeModule;

			$.ajax({
				type : "POST",
				url : "/ajax/setFormInstance.php",
				data : data,

				success : function(data) {
					refresh();
					//opts.onSuccess.call(FORM[0], data);
				},
				error : function() {
					alert("Error when commitin the modif on this module.");
					//opts.onError.call(FORM[0]);
				}
			});
		});
*/


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
				//TODO: update the list of the users, get it from the server, or juste delete the right one in the list localy
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the form of the user");
			})
		;
	}




}


