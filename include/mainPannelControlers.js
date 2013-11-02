

function mainPannelCtrl($scope, $http, $sce, $compile){

	$scope.needTabs = false;
	//may be useless
	$scope.unicTabContent = "<div ng-controller=\"formCtrl\">salut</div>";

	$scope.loadHome = function(){

		$scope.needTabs = false;
		angular.element($("#unicTab")).scope().loadHome();
	}



	$scope.loadUser = function(name){

		$scope.needTabs = false;
		angular.element($("#unicTab")).scope().loadInstance("config", "user", "", name);
	}





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



function unicTabCtrl($scope, $http){

	$scope.page = "home";
	$scope.home = "Home page";


	$scope.loadHome = function(){

		activePage = "home";
		activeModule = "";
		activeSubModule = "";
		activeInstance = "";

		$http({
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
		;
	}


	$scope.loadInstance = function(activePage, activeModule, activeSubModule, activeInstance){

		$scope.page = "form";

		//TODO : this methode is awfull, need to find a way to wait for the formCtrl to be initialized
		setTimeout(function (){
			$scope.$broadcast('getFormEvent', [activePage, activeModule, activeSubModule, activeInstance]);
		}, 10);
	}
}







