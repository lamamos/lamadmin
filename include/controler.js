var app = angular.module('lamadmin', []);


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

		displayUser(user.name);
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




		//Ne pas faire comme cela. Créer un nouvel element dans le DOM qui aura son propre controler
		//qui se chargera d'aller chercher les données sur le serveur.
		var donnees = $.param({name: module.name});

		$http({
			method: "POST",
			url: "/ajax/getListSubModule.php",
			data: donnees,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		})
			.success(function(response){

				//$scope.userList = response;

				var tabs= response.split(",");                 
				
				if($("#mainPannel #tabs").length > 0){   //if we have a tabs element
				    
			        $("#tabs li").remove(); //we delete them
			        appearTabs(tabs);   //we make the new one appear
				}else{   //if we don't have any tabs
				    
				    //we create some new one
				    $("#mainPannel").prepend("<ul id=\"tabs\"></ul>\n");
				    $("#tabs").addClass("pt-page-moveFromTop"); //we make the tabs background appear smoothly
				    appearTabs(tabs);
				}

			    $("#mainPannel #forms").remove();   //we delete every thing in the mainPannel
			    $("#mainPannel").append("<div id=\"forms\"></div>"); //we recrate the tabs and forms
			    for(i=0; i<tabs.length; i++){
	
			        $("#forms").append("<div id=\""+tabs[i]+"\"></div>");
			    }
			    //we have to launche the subModuleForm event by hand (don't know why)
			    changeTab("general");


				function appearTabs(tabs){
				    
				    for(i=0; i<tabs.length; i++){
		
				        $("#tabs").append("<li id=\"#"+tabs[i]+"\"><a href=\"#"+tabs[i]+"\">"+tabs[i]+"</a></li>");
				        $("#forms").append("<div id=\""+tabs[i]+"\"></div>");
				    }
				    $("#tabs li").addClass("pt-page-moveFromTop");
				    $("#mainPannel").tabs("refresh");
				}


				/*if($("#mainPannel #tabs").length > 0){   //if we have a tabs element
				    
				    $("#tabs li").addClass("pt-page-moveToBottom"); //we make the tab deseapear
				    //when the tabs have desapeared
				    $("#tabs li").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
				        
				        $("#tabs li").remove(); //we delete them
				        appearTabs(tabs);   //we make the new one appear
				    });
				}else{   //if we don't have any tabs
				    
				    //we create some new one
				    $("#mainPannel").prepend("<ul id=\"tabs\"></ul>\n");
				    $("#tabs").addClass("pt-page-moveFromTop"); //we make the tabs background appear smoothly
				    appearTabs(tabs);
				}
				
				$("#forms").addClass("pt-page-moveToBottom");
				$("#forms").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
				    
				    $("#mainPannel #forms").remove();   //we delete every thing in the mainPannel
				    $("#mainPannel").append("<div id=\"forms\"></div>"); //we recrate the tabs and forms
				    for(i=0; i<tabs.length; i++){
		
				        $("#forms").append("<div id=\""+tabs[i]+"\"></div>");
				    }
				    //we have to launche the subModuleForm event by hand (don't know why)
				    changeTab("general");
				});
		
		            
				
				
				function appearTabs(tabs){
				    
				    for(i=0; i<tabs.length; i++){
		
				        $("#tabs").append("<li id=\"#"+tabs[i]+"\"><a href=\"#"+tabs[i]+"\">"+tabs[i]+"</a></li>");
				        $("#forms").append("<div id=\""+tabs[i]+"\"></div>");
				    }
				    $("#tabs li").addClass("pt-page-moveFromTop");
				    $("#mainPannel").tabs("refresh");
				}*/
		
		         


			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the submodules");
			})
		;

    }

}





