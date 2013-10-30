

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


				if($("#mainPannel #tabs").length > 0){   //if we have a tabs element
					
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
				}
			})

			.error(function(data, status, headers, config) {

				alert("error when getting the liste of the list of submodules");
			})
		;
	}
}
