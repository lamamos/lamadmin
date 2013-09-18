<?
//set the garbage coloctor timeout for our session
ini_set('session.gc_maxlifetime', 3600*24*7);
session_start();

include_once("include/configObject.php");
include_once("include/createConfig.php");

$config = new Configuration();

?>

<html>
    <head>

    </head>
    <link rel="stylesheet" href="include/jquery-ui.css">
    <link rel="stylesheet" href="include/style.css">
    <link rel="stylesheet" href="include/magic.css">
    <link rel="stylesheet" href="include/animate.css">
    <link rel="stylesheet" href="include/on_off_button.css">
    <script type="text/javascript" src="include/jquery-2.0.3.min.js"></script>
    <script src="include/jquery-ui.js"></script>
    <body>
        <div id="sideBar">
            <div id="logo">Home</div><br>
            <div class="sectionTitle">Users : </div><br>
            <div id="listUsers"></div>
            <br>
            <div class="sectionTitle" id="addUser">Add user</div>
            <br><br>
            <div class="sectionTitle">Services : </div><br>
            <div id="listServices"></div>
            <br><br>
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
                
        //on page load, when make the parts of the page slide in        
        $("#sideBar").ready(function () { 
        
            $("#sideBar").addClass('magictime slideLeftRetourn');
        });
        $("#mainPannel").ready(function () { 
        
            $("#mainPannel").addClass('magictime slideUpRetourn');
        });
        
        //this function is called at te opening of the page
        $(function() {
            
            displayListUsers();
            displayListServices();
            displayHome();
            
            $("#mainPannel").tabs({
                beforeActivate: function(event, ui){
                    
                    tabName = ui.newTab.attr('id').substr(1);
                    changeTab(tabName);
                }
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
        
            request.fail(function(jqXHR, textStatus, errorThrown){alert("error when getting the form for the user");});
        
            request.always(function(){
            
                if(activeModule=="user"){
                
                    displayHome();
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
                });
                
                
                $(".instanceForm").submit(function (){       
    
                    var data = $(this).serialize();
                    data += "&moduleName="+activeModule;
    
                    $.ajax({
                        type    : "POST",
                        url     : "/ajax/setFormInstance.php",
                        data    : data,
                        success : function(data) {
                            //alert("done");
                            //opts.onSuccess.call(FORM[0], data);
                        },
                        error   : function() {
                            //opts.onError.call(FORM[0]);
                        }
                    });
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

            var data = {
                name: name,
            }
                    
            request = $.ajax({
                url: "/ajax/getFormUser.php",
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
                    $(".userForm").submit(function (){  //we make it a proper user form with an ajax handle
                        $.ajax({
                            type    : "POST",
                            url     : "/ajax/setFormUser.php",
                            data    : $(this).serialize(),
                            success : function(data) {},
                            error   : function() {}
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
        
        function displayListServices(){
        
            request = $.ajax({
                url: "/ajax/getListServices.php",
                type: "POST"
            });
        
            request.done(function(response, textStatus, jqXHR){
        
                var services= response.split(",");                 

                $("#listServices div").remove();
            
                for(i=0; i<services.length; i++){
                    //var service = services[i];
                    var service= services[i].split(";");
                    var name = service[0];
                    var activated = service[1];
                    
                    var element = "<div class=\"sideBarLine\" id=\""+name+"\">";
                        element += "<div class=\"mainModule\">"+name+"</div>";
                        if(activated == "1"){element += "<div class=\"bool-slider true\"> <div class=\"inset\"> <div class=\"control\"></div> </div> </div>";}
                        else{element += "<div class=\"bool-slider false\"> <div class=\"inset\"> <div class=\"control\"></div> </div> </div>";}
                    
                    element += "<br></div>";
        
                    $("#listServices").append(element);
                }
                                      
            });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when getting the liste of the services");
            });
        
            request.always(function(){});
        }
        
        function displayListUsers(){
            
            request = $.ajax({
                url: "/ajax/getListUsers.php",
                type: "POST"
            });
        
            request.done(function(response, textStatus, jqXHR){
        
                var users= response.split(",");     

                $("#listUsers div").remove();
            
                for(i=0; i<users.length; i++){

                    var user = users[i];
                    var element = "<div class=\"user\">"+user+"<br></div>";
                    $("#listUsers").append(element);
                }
                                      
            });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when getting the liste of the services");
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
            
            //refresh the sideBarre
            displayListServices();
            displayListUsers();
            
            redefineComportements();
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
            
            $(".user").click(function(){
             
                activePage = "config";
                activeModule = "user";
                activeSubModule = "";
                activeInstance = $(this).text();
                
                $("#sideBar div").removeClass('moduleSelected');
                $(this).addClass('moduleSelected');
                
                displayUser($(this).text());
            });
            
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
                
            });
            
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
                                            
                            $(".deleteInstance").click(function(){deleteInstance();});
                            
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
                
                //alert("kikoo");
                activePage = "config";
                activeModule = "user";
                activeSubModule = "";
                activeInstance = "Add new";
                
                $("#sideBar div").removeClass('moduleSelected');
                $(this).addClass('moduleSelected');
                
                displayUser("Add new");
                
            });
     
        }
        
        $(".test").click(function(){
            
            refresh();
        });
    </script>

</html>
