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
    <script src="include/on_off_button.js"></script>
    <body>
        <div id="sideBar">
            Users : <br>
            <?  //list the users on the system
                $module = $config->getModule("user");
                foreach($module->getInstances() as $instance){

                    echo "<div class=\"user\">".$instance->getName()."</div><br>";
                }
            ?>
            <br><br>
            System : <br>
            <?
                foreach($config->getAvalableModules() as $module){
                    if(!preg_match("/".$module->getName()."/", "user")){

                        echo "<div class=\"sideBarLine\" id=\"".$module->getName()."\">";
                        echo "<div class=\"mainModule\">".$module->getName()."</div>";
                        if($module->isActivated()){echo "<div class=\"bool-slider true\"> <div class=\"inset\"> <div class=\"control\"></div> </div> </div>";}
                        else{echo "<div class=\"bool-slider false\"> <div class=\"inset\"> <div class=\"control\"></div> </div> </div>";}                        
                        echo "</div><br>";
                    }
                }
                
            ?>
        </div>
    
        <div id="mainPannel">
            <ul id="tabs">
                <li><a href="#welkome">Welkome</a></li>
            </ul>
            <div id="forms">
        
                <div id="welkome">
                        <p>
                            Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.
                    </p>
                </div>
            </div>
        </div>    
    </body>
    
    <script type="text/javascript">

        var activeModule = "";
        var activeSubModule = "";
                
        //on page load, when make the parts of the page slide in        
        $("#sideBar").ready(function () { 
        
            $("#sideBar").addClass('magictime slideLeftRetourn');
        });
        $("#mainPannel").ready(function () { 
        
            $("#mainPannel").addClass('magictime slideUpRetourn');
        });
        
        //this function is called at te opening of the page
        $(function() {  
            $("#mainPannel").tabs({
                
                beforeActivate: function(event, ui){
                    
                    tabName = ui.newTab.attr('id').substr(1);
                    activeSubModule = tabName;
                    
                    var data = {
                        moduleName: activeModule,
                        subModuleName: tabName,
                    }
                
                    request = $.ajax({
                        url: "/ajax/getFormSubModule.php",
                        type: "POST",
                        data: data
                    });
                    
                    request.done(function(response, textStatus, jqXHR){
                    
                        //alert(response);
                        $("#forms #"+tabName).remove();
                        $("#forms").append("<div id=\""+tabName+"\">"+response+"</div>");
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
            
            });
        });
        
        $(".user").click(function(){
        
            activeModule = $(this).text();
        
            var data = {
                name: $(this).text(),
            }
        
            request = $.ajax({
                url: "/ajax/getFormUser.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){
                $("#tabs").remove();
                $("#forms").remove();
                $("#mainPannel").append("<div id=\"forms\">"+response+"</div>");
                $(".userForm").submit(function (){            
                    $.ajax({
                        type    : "POST",
                        url     : "/ajax/setFormUser.php",
                        data    : $(this).serialize(),
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
        
                alert("error when getting the form for the user");
            });
        
            request.always(function(){});
        });
        
        $(".mainModule").click(function(){ 
        
            activeModule = $(this).text();
        
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
        
                $("#tabs").remove();
                $("#forms").remove();
                $("#mainPannel").append("<ul id=\"tabs\"></ul>\n<div id=\"forms\"></div>");
        
                for(i=0; i<tabs.length; i++){
        
                    $("#tabs").append("<li id=\"#"+tabs[i]+"\"><a href=\"#"+tabs[i]+"\">"+tabs[i]+"</a></li>");
                    $("#forms").append("<div id=\""+tabs[i]+"\"></div>");
                }
        
                $("#mainPannel").tabs("refresh");
            });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error when getting the liste of the tabs");
            });
        
            request.always(function(){});
            
        });
        
        $("#mainPannel").on("change", ".instanceSelector", function(){
        
            $(".instanceSelector option:selected").each(function(){	//they should be only one element here
        
                instanceName = $(this).text();
        
                if(instanceName == "Add new")return;
            
                var data = {
                    moduleName: activeModule,
                    subModuleName: activeSubModule,
                    instanceName: instanceName,
                }
        
                request = $.ajax({
                    url: "/ajax/getFormInstance.php",
                    type: "POST",
                    data: data
                });
        
                request.done(function(response, textStatus, jqXHR){
        
                    //alert(response);
                    $("#forms #"+activeSubModule+" #instanceForm").remove();
                    $("#forms #"+activeSubModule).append(response);
                    $(".instanceForm").submit(function (){       

                        var data = $(this).serialize();
                        data += "&moduleName="+activeModule;
                        data += "&subModuleName="+activeSubModule;
                        data += "&instanceName="+instanceName;

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
        
                    alert("error when getting the form of the instance");
                });
        
                request.always(function(){});
        
            });
        });
        
        $(".bool-slider").click(function(){
            
            if (!$(this).hasClass('disabled')) {
                    if ($(this).hasClass('true')) {
                        $(this).addClass('false').removeClass('true');
                    } else {
                        $(this).addClass('true').removeClass('false');
                    }
            }
            


            
            var data = {
                moduleToggled: $(this).parent().attr('id'),
            }
        
            request = $.ajax({
                url: "/ajax/toggleModule.php",
                type: "POST",
                data: data
            });
        
            request.done(function(response, textStatus, jqXHR){ });
        
            request.fail(function(jqXHR, textStatus, errorThrown){
        
                alert("error chile changing the state of the module.");
            });
        
            request.always(function(){});
        });
        
        
    </script>

</html>