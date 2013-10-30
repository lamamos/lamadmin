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
    <link rel="stylesheet" href="include/jquery-ui.css">
    <link rel="stylesheet" href="include/style.css">
    <link rel="stylesheet" href="include/magic.css">
    <link rel="stylesheet" href="include/animate.css">
    <link rel="stylesheet" href="include/on_off_button.css">
    <script type="text/javascript" src="include/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="include/jquery-ui.js"></script>
    <script type="text/javascript" src="include/angular.min.js"></script>
    <script type="text/javascript" src="include/angular-animate.js"></script>
    <script type="text/javascript" src="include/controler.js"></script>
    <body>
        <div id="sideBar">
            <div id="logo">Home</div><br>
            <div class="sectionTitle">Users : </div><br>
            <div id="listUsers" ng-controller="userListCtrl">
				<div class="user" ng-class="getClass(user)" ng-repeat="user in userList" ng-click="click(user)">{{user.name}}</div>
	    	</div>
            <br>
            <div class="sectionTitle" id="addUser">Add user</div>
            <br><br>
            <div class="sectionTitle">Services : </div><br>
            <div id="listServices" ng-controller="serviceListCtrl">
				<div class="sideBarLine" id="{{module.name}}" ng-repeat="module in moduleList">
					<div class="mainModule" ng-click="click(module)">{{module.name}}</div>
					<div class="bool-slider" ng-class="{true : module.activated, false : !module.activated}"><div class="inset"><div class="control"></div></div></div>
				</div>
			</div>
            <br><br><br><br><br>
            <div class="sectionTitle test">Refresh</div>
        </div>
    
        <div id="mainPannel">
            <div id="forms">
                
                
            </div>
        </div>    
    </body>
    
    <script type="text/javascript">

        var activePage = "";
        var activeModule = "";
        var activeSubModule = "";
        var activeInstance = "";
        
        var activeAfterInput;
                
        //on page load, when make the parts of the page slide in        
        $("#sideBar").ready(function () { 
        
            $("#sideBar").addClass('magictime slideLeftRetourn');
        });
        $("#mainPannel").ready(function () { 
        
            $("#mainPannel").addClass('magictime slideUpRetourn');
        });
        
        //this function is called at te opening of the page
        $(function() {
            
            displayHome();
            
            $("#mainPannel").tabs({
                beforeActivate: function(event, ui){
                    
                    tabName = ui.newTab.attr('id').substr(1);
                    changeTab(tabName);
                }
            });
            
            $("#menu").menu();
            
            $(document).ajaxComplete(function(){
                
                //function called when a jaxrequest is done
                //alert("kikoo");   
            });
            
            redefineComportements();
        });
        
        $("#sideBar #logo").click(function(){
            
            displayHome();
        });
        
        function deleteInstance(){
            
            var data = {
                moduleName: activeModule,
                subModuleName: activeSubModule,
                instanceName: activeInstance,
            }
        
            request = $.ajax({
                url: "/ajax/deleteInstance.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){});
        
            request.fail(function(jqXHR, textStatus, errorThrown){alert("error when deleting the user");});
        
            request.always(function(){
            
                if(activeModule=="user"){
                
                    activePage = "home";
                    activeModule = "";
                    activeSubModule = "";
                    activeInstance = "";
                    refresh();
                }else{
                    
                    changeTab(activeSubModule);
                }
                
            });
        }
        
        function changeTab(tabName){
    
            try{if(tabName){oldTabName = tabName;}}catch(e){oldTabName = "general";}

            activeSubModule = tabName;
            
            var data = {
                moduleName: activeModule,
                subModuleName: tabName,
            }
            
            //alert(data);
                    
            request = $.ajax({
                url: "/ajax/getFormSubModule.php",
                type: "POST",
                data: data
            });
            
            request.done(function(response, textStatus, jqXHR){
                
                //alert("reçu");
                //This line is useless as far as we commented the line 14380 in jquery-ui.js
                //Now jquery don't hide the old tab, we do it with this animation.
                //$("#forms #"+oldTabName+"_old").show();
                $("#forms div").addClass("pt-page-flipOutLeft");
                $("#forms div").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
    
                    //alert("delete");
                    $("#forms div").removeClass("pt-page-flipOutLeft");
                    $("#forms div").remove();
                    $("#forms").append("<div id=\""+tabName+"\" class=\"subModuleForm\">"+response+"</div>");
                    $("#forms #"+tabName).addClass("pt-page-flipInRight");
                    $("#forms #"+tabName).one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
    
                            $("#forms #"+tabName).removeClass("pt-page-flipInRight");
                    });
                    
                    
                    $(".instanceForm").submit(function (){       
        
                        var data = $(this).serialize();
                        data += "&moduleName="+activeModule;
        
                        $.ajax({
                            type    : "POST",
                            url     : "/ajax/setFormInstance.php",
                            data    : data,
                            success : function(data) {
                                refresh();
                                //opts.onSuccess.call(FORM[0], data);
                            },
                            error   : function() {
                                alert("Error when commitin the modif on this module.");
                                //opts.onError.call(FORM[0]);
                            }
                        });
                    });
                    
                    $(".addElementToArray").click(function(){addElementToArray($(this));});
                    $(".removeElementFromArray").click(function(){removeElementFromArray($(this));});
                    
                });            
            });
            
            request.fail(function(jqXHR, textStatus, errorThrown){
            
                alert("error when getting the list of the submodule");
            });
            
            request.always(function(){});
        }
        
        function displayHome(){
            
            activePage = "home";
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
            });
        }
        
        function displayUser(name){

            activePage = "config";
            activeModule = "user";
            activeSubModule = "";
            activeInstance = name;
            
            var data = {
                moduleName: "user",
                instanceName: name
            }
                    
            request = $.ajax({
                url: "/ajax/getFormInstance.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){
            
                if($("#mainPannel #tabs").length > 0){   //if we have a tabs element
                    
                    $("#tabs").addClass("pt-page-moveToTop"); //we make the tab deseapear
                    $("#tabs li").addClass("pt-page-moveToTop"); //we make the tab deseapear
                    //when the tabs have desapeared
                    $("#tabs li").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                        
                        $("#tabs").remove(); //we delete them
                        $("#tabs li").remove(); //we delete them                        
                    });
                }
                
                //we make the forms deseapear
                $("#mainPannel #forms").addClass("pt-page-moveToBottom");
                //when they are not anymore on the screen
                $("#mainPannel #forms").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
                
                    $("#mainPannel #forms").remove();   //we delete them
                    $("#mainPannel").append("<div id=\"forms\">"+response+"</div>");    //we add the user form
                    $("#mainPannel #forms").addClass("pt-page-moveFromTop");    //we make it appeare
                    $(".instanceForm").submit(function (){       
        
                        var data = $(this).serialize();
                        data += "&moduleName=user&instanceName="+activeInstance;
        
                        $.ajax({
                            type    : "POST",
                            url     : "/ajax/setFormInstance.php",
                            data    : data,
                            success : function(data) {
                                refresh();
                                //opts.onSuccess.call(FORM[0], data);
                            },
                            error   : function() {
                                alert("Error when commitin the modif on this module.");
                                //opts.onError.call(FORM[0]);
                            }
                        });
                    });
                    $(".deleteInstance").click(function(){deleteInstance();});
                });
            });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when getting the form for the user");
            });
        
            request.always(function(){});
        }
        
        function refresh(){

            if(activePage == "home"){displayHome();}
            else if(activePage == "config"){
                if(activeModule == "user"){
                    
                    displayUser(activeInstance);
                }else{
                    
                    changeTab(activeSubModule);
                }
            }
            
            redefineComportements();
        }
        
        function addElementToArray(clickedElement){             
        
            var data = {
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
        
            request.always(function(){});
        }
        
        function removeElementFromArray(clickedElement){            
            
            elementName = clickedElement.attr('name').substring(7);
            
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
        
            request.always(function(){});
        }
        
        function redefineComportements(){

            //we need to put a timeout, if we call the function right away, the DOM is not yet constructed
            //and it's useless
            setTimeout(function(){reredefineComportements();}, 500);
            //$(document).ready(function(){reredefineComportements();});
        }
        
        function reredefineComportements(){
            
            $(".bool-slider").click(function(){
                                
                if (!$(this).hasClass('disabled')) {
                        if ($(this).hasClass('true')) {
                            $(this).addClass('false').removeClass('true');
                        } else {
                            $(this).addClass('true').removeClass('false');
                        }
                }
                
                var moduleChanged = $(this).parent().attr('id');
                var newState = "undefined";
                if($(this).hasClass('true'))newState = "on";else newState = "off";
                
                var data = {
                    moduleToggled: moduleChanged,
                }
            
                request = $.ajax({
                    url: "/ajax/toggleModule.php",
                    type: "POST",
                    data: data
                });
            
                request.done(function(response, textStatus, jqXHR){
                
                    if( (activeModule == moduleChanged) && (newState == "off") ){
                        
                        displayHome();
                        //the fact of not having a space between the selector in the next line is normal, we selecte an element by it's class and ID
                        $("#"+moduleChanged+".sideBarLine").removeClass("moduleSelected");
                    }
                
                });
            
                request.fail(function(jqXHR, textStatus, errorThrown){
            
                    alert("error chile changing the state of the module.");
                });
            
                request.always(function(){});
            });
            /*
            $(".mainModule").click(function(){ 
            
                if($(this).parent().find(".bool-slider").hasClass("false")){return;}
                
                $("#sideBar div").removeClass('moduleSelected');
                $(this).parent().addClass('moduleSelected');
                
                activePage = "config";
                activeModule = $(this).text();
                activeSubModule = "";
                activeInstance = "";
                        
                var data = {
                    name: $(this).text(),
                }
            
                request = $.ajax({
                    url: "/ajax/getListSubModule.php",
                    type: "POST",
                    data: data
                });
            
                request.done(function(response, textStatus, jqXHR){
            
                    var tabs= response.split(",");                 
                    
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
    
             
                                      
                });
            
                request.fail(function(jqXHR, textStatus, errorThrown){
            
                    alert("error when getting the liste of the tabs");
                });
            
                request.always(function(){});
                
            });*/
            
            $("#mainPannel").on("change", ".instanceSelector", function(){
            
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
                                    success : function(data) {changeTab(activeSubModule);},
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
            });
            
            $("#addUser").click(function(){
                
                activePage = "config";
                activeModule = "user";
                activeSubModule = "";
                activeInstance = "Add new";
                
                $("#sideBar div").removeClass('moduleSelected');
                $(this).addClass('moduleSelected');
                
                displayUser("Add new");
                
            });
     
        }
        
        function displayInstanceMenu(source){
                        
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
            });
        }
        
        function formatInstanceMenu(choise){
            
            activeAfterInput.val(choise);
            $(".menu").remove();
        }
        
        $(".test").click(function(){
            
            refresh();
        });
    </script>

</html>
