

function mainPannelCtrl($scope, $http){


	$scope.loadModul = function(name){


		var donnees = $.param({name: name});

		$http({
			method: "POST",
			url: "/ajax/getListSubModule.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				var tabs= response.split(",");

				angular.element($("#tabs")).scope().clearTabs();

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

	$scope.clearTabs = function(){

		$scope.tabs = [];
	}

	$scope.addTab = function(title, content){

		$scope.tabs.push({"title": title, "content": content});
	}




	$scope.changeTab = function(tab){

            activeSubModule = tab.title;

            var donnees =$.param({
                moduleName: activeModule,
                subModuleName: tab.title,
            });

			$http({
				method: "POST",
				url: "/ajax/getFormSubModule.php",
				data: donnees,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
				.success(function(response){

					tab.content = $sce.trustAsHtml(response);
				})

				.error(function(data, status, headers, config) {

					alert("error when getting the liste of the list of the submodul instances");
				})
			;

	}


}



