<?php
//set the garbage coloctor timeout for our session
ini_set('session.gc_maxlifetime', 3600*24*7);
session_start();

include_once("include/configObject.php");
include_once("include/createConfig.php");

$config = new Configuration();

?>

<html ng-app="lamadmin">
    <head>

    </head>
    <script type="text/javascript" src="include/angular.min.js"></script>
    <link rel="stylesheet" href="include/bootstrap.min.css">
    <link rel="stylesheet" href="include/style.css">
    <link rel="stylesheet" href="include/magic.css">
    <link rel="stylesheet" href="include/animate.css">
    <link rel="stylesheet" href="include/on_off_button.css">
    <script type="text/javascript" src="include/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="include/angular-animate.min.js"></script>
    <script type="text/javascript" src="include/sideBarControlers.js"></script>
    <script type="text/javascript" src="include/mainPannelControlers.js"></script>
    <script type="text/javascript" src="include/formsControlers.js"></script>
    <script type="text/javascript" src="include/ui-bootstrap-tpls-0.6.0.min.js"></script>
    <body>
        <div id="sideBar" class="magictime slideLeftRetourn" ng-controller="sideBarCtrl">
            <div id="logo" ng-click="displayHome()">Home</div><br>
            <div class="sectionTitle">Users : </div><br>
            <div id="listUsers" ng-controller="userListCtrl">
				<div class="user" ng-class="getClass(user.name)" ng-repeat="user in userList" ng-click="click(user)">{{user.name}}</div>
	    	</div>
            <br>
            <div class="sectionTitle" id="addUser" ng-class="getClass('Add new')" ng-click="addUser()">Add user</div>
            <br><br>
            <div class="sectionTitle">Services : </div><br>
            <div id="listServices" ng-controller="serviceListCtrl">
				<div class="sideBarLine" id="{{module.name}}" ng-class="getClass(module.name)" ng-repeat="module in moduleList">
					<div class="mainModule" ng-click="click(module)">{{module.name}}</div>
					<div class="bool-slider" ng-class="{true : module.activated, false : !module.activated}" ng-click="clickBoolean(module)"><div class="inset"><div class="control"></div></div></div>
				</div>
			</div>
            <br><br><br><br><br>
            <div class="sectionTitle test">Refresh</div>
        </div>
    
        <div id="mainPannel" class="magictime slideUpRetourn" ng-controller="mainPannelCtrl">

		<div ng-controller="tabsControler">
			<div id="tabs" ng-show="needTabs">
				<tabset>
					<tab class="tab-anim" ng-repeat="tab in tabs" select="changeTab(tab)" heading="{{tab.title}}" active="tab.active" disabled="tab.disabled">

						<div id="forms" ng-bind-html="tab.content"></div>
					</tab>
				</tabset>
			</div>
		</div>

		<div id="unicTab" ng-hide="needTabs" ng-controller="unicTabCtrl">
			<div ng-include="'unic_tab_template.html'"></div>
		</div>

        </div> 
    </body>


	<script type="text/ng-template" id="unic_tab_template.html">
		<div ng-switch on="page">
			<div ng-switch-when="home"><p>{{home}}</p></div>
			<div ng-switch-when="form">
				<form id="container" ng-submit="submit()" ng-controller="formCtrl">
					<div ng-include="'form_template.html'"></div>
					<input type="submit" value="Save">
					<input type="button" ng-click="delete()" value="Delete">
				</form>
			</div>
			<div ng-switch-default>an error accured</div>
		</div>
	</script>


	<script type="text/ng-template" id="form_template.html">
		<div ng-repeat="item in content" ng-switch on="item.content_type">
			<div ng-switch-when="text">
				{{item.title}}:<input name={{item.title}} type="text" ng-model="item.value">
			</div>
			<div ng-switch-when="number">
				{{item.title}}:<input name={{item.title}} type="number" ng-model="item.value">
			</div>
			<div ng-switch-when="array">
				{{item.title}}:<div ng-repeat="item in item.stuffs" ng-include="'tree_item_renderer.html'"></div>
			</div>
			<div ng-switch-when="hash">
				{{item.title}}:<div ng-repeat="item in item.stuffs" ng-include="'tree_item_renderer.html'"></div>
			</div>
			<div ng-switch-default>
				default : {{item.title}}
			</div>
		</div>
	</script>


    <script type="text/javascript">

        var activePage = "";
        var activeModule = "";
        var activeSubModule = "";
        var activeInstance = "";
        
        var activeAfterInput;
        
        //this function is called at te opening of the page
        $(function() {
            
            angular.element($("#mainPannel")).scope().loadHome();
            
            redefineComportements();
        });
        
        
        function refresh(){

           /* if(activePage == "home"){displayHome();}
            else if(activePage == "config"){
                if(activeModule == "user"){
                    
                    displayUser(activeInstance);
                }else{
                    
                    changeTab(activeSubModule);
                }
            }
            
            redefineComportements();*/
        }
        
        function addElementToArray(clickedElement){             
        
            /*var data = {
                moduleName: activeModule,
                subModuleName: activeSubModule,
                instanceName: activeInstance,
                arrayName: clickedElement.attr('name'),
            }
        
            request = $.ajax({
                url: "/ajax/addElementToArray.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){

                clickedElement.before(response);
                                  
            });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when adding an element to the array");
            });
        
            request.always(function(){});*/
        }
        
        function removeElementFromArray(clickedElement){            
            
            /*elementName = clickedElement.attr('name').substring(7);
            
            //we remove the input in the form            
            $("input[name='"+elementName+"']").remove();
            //we remove the remove button
            clickedElement.remove();
            
            
            arrayName = elementName.match(/.*\[/);
            arrayName = arrayName[0].substr(0, arrayName[0].length-1);  //we remove the last [
            
            argNum = elementName.match(/\[\d+\]/);
            argNum = argNum[0].substr(1, arrayName[0].length); //we remove the first [ and the last [
                        
            var data = {
                moduleName: activeModule,
                subModuleName: activeSubModule,
                instanceName: activeInstance,
                arrayName: arrayName,
                argmentNumber : argNum,
            }
        
            request = $.ajax({
                url: "/ajax/removeElementFromArray.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){});
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when deletting an element to the array");
            });
        
            request.always(function(){});*/
        }
        
        function redefineComportements(){

            //we need to put a timeout, if we call the function right away, the DOM is not yet constructed
            //and it's useless
            //setTimeout(function(){reredefineComportements();}, 500);
        }
        
        function reredefineComportements(){
           
            /*$("#mainPannel").on("change", ".instanceSelector", function(){
            
                $(".instanceSelector option:selected").each(function(){	//they should be only one element here
            
                    activeInstance = $(this).text();
                    
                    var data = {
                        moduleName: activeModule,
                        subModuleName: activeSubModule,
                        instanceName: activeInstance,
                    }
            
                    request = $.ajax({
                        url: "/ajax/getFormInstance.php",
                        type: "POST",
                        data: data
                    });
            
                    request.done(function(response, textStatus, jqXHR){
                        
                        //if there is an instance forme
                        if($("#forms #"+activeSubModule+" .instanceForm").length > 0){
                            
                            $("#forms #"+activeSubModule+" .instanceForm").addClass("pt-page-moveToBottom");
                            $("#forms").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                                
                                $("#forms #"+activeSubModule+" .instanceForm").remove();
                                displayInstanceForm(response);
                            });
                        }else{
                            
                            displayInstanceForm(response);
                        }
                        
                        
                        
                        function displayInstanceForm(response){
                            
                            $("#forms #"+activeSubModule).append(response);
                            $(".instanceForm").addClass("pt-page-moveFromBottom");
                            
                            $(".instanceForm").submit(function (){       
        
                                var data = $(this).serialize();
                                data += "&moduleName="+activeModule;
                                data += "&subModuleName="+activeSubModule;
                                data += "&instanceName="+activeInstance;
                                
                                $.ajax({
                                    type    : "POST",
                                    url     : "/ajax/setFormInstance.php",
                                    data    : data,
                                    success : function(data) {//changeTab(activeSubModule); //changed (angular)
					},
                                    error   : function() {}
                                });
                            });
                            
                                        
                            $(".instanceMenu").click(function(){
                                
                                displayInstanceMenu($(this));
                            });
                                            
                            $(".deleteInstance").click(function(){deleteInstance();});
                            $(".addElementToArray").click(function(){addElementToArray($(this));});
                            $(".removeElementFromArray").click(function(){removeElementFromArray($(this));});

                            $(".instanceForm").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
    
                                $(".instanceForm").removeClass("pt-page-moveFromBottom");
                            });
                        }
                        
                        
                        
                    });
            
                    request.fail(function(jqXHR, textStatus, errorThrown){
            
                        alert("error when getting the form of the instance");
                    });
            
                    request.always(function(){});
            
                });
            });*/
        }
        
        function displayInstanceMenu(source){
             /*           
            activeAfterInput = source;
            
            request = $.ajax({
                url: "/ajax/getInstanceMenu.php",
                type: "POST"
            });
        
            request.done(function(response, textStatus, jqXHR){
        
                $("#mainPannel").append(response);
                $(".menu").menu();
                
                //we position this menu on the irght of the text input we just clicked
                var position = activeAfterInput.offset();
                position.left += activeAfterInput.width() + 5;
                $(".menu").offset({ top: position.top, left: position.left})
            });*/
        }
        
        function formatInstanceMenu(choise){
            
            /*activeAfterInput.val(choise);
            $(".menu").remove();*/
        }
        
        $(".test").click(function(){
            
            //refresh();
        });
    </script>

</html>
