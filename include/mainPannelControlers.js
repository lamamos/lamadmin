

function mainPannelCtrl($scope, $http, $sce, $compile){

	$scope.needTabs = false;
	//may be useless
	$scope.unicTabContent = "<div ng-controller=\"formCtrl\">salut</div>";

	$scope.activeModule = "";


	$scope.loadHome = function(){

		$scope.needTabs = false;
		$scope.activeModule = "home";
		angular.element($("#unicTab")).scope().loadHome();
	}


	$scope.loadUser = function(name){

		$scope.needTabs = false;
		$scope.activeModule = "user";
		angular.element($("#unicTab")).scope().loadInstance("config", "user", "", name);
	}


	$scope.loadModul = function(name){


		alert("kikooo");

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







