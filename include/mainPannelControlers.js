

function mainPannelCtrl($scope, $http, $sce){

	$scope.needTabs = false;
	$scope.unicTabContent = "kikooo85";

	$scope.loadHome = function(){


		activePage = "home";
		activeModule = "";
		activeSubModule = "";
		activeInstance = "";


/*            $("#sideBar .user").removeClass("moduleSelected");
            $("#sideBar .sideBarLine").removeClass("moduleSelected");
*/

		$http({
			method: "POST",
			url: "/ajax/getHome.php",
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				$scope.needTabs = false;
				$scope.unicTabContent = $sce.trustAsHtml(response);
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the list of submodules");
			})
		;


            /*activePage = "home";
            activeModule = "";
            activeSubModule = "";
            activeInstance = "";
              
            $("#sideBar .user").removeClass("moduleSelected");
            $("#sideBar .sideBarLine").removeClass("moduleSelected");
            
            request = $.ajax({
                url: "/ajax/getHome.php",
                type: "POST"
            });
        
            request.done(function(response, textStatus, jqXHR){
                
                leftToDeseaper = 0;
                
                //if there are some tabs to remove
                if($("#mainPannel #tabs").length > 0){
                    leftToDeseaper++;
                    $("#mainPannel #tabs").addClass("pt-page-moveToTop");
                    $("#mainPannel #tabs").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                        
                        $("#mainPannel #tabs").remove();
                        leftToDeseaper--;
                        if(leftToDeseaper == 0){appearHome();}
                    });
                }
                
                //if there are some forms to remove
                if($("#mainPannel div").length > 0){
                    leftToDeseaper++;
                    $("#mainPannel div").addClass("pt-page-moveToBottom");
                    $("#mainPannel div").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                        
                        $("#mainPannel div").remove();
                        leftToDeseaper--;
                        if(leftToDeseaper == 0){appearHome();}
                    });
                }
                
                //if the page is already empty
                if(leftToDeseaper == 0){appearHome();}
                
                
                function appearHome(){
                    
                    $("#mainPannel").append("<div id=\"forms\">"+response+"</div>");
                    $("#mainPannel #forms").addClass("pt-page-moveFromTop");
                    $("#mainPannel #forms").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                        
                            $("#mainPannel #forms").addClass("pt-page-moveFromTop");
                    });

                }
            });*/

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



