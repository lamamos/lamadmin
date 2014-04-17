<?php
//set the garbage coloctor timeout for our session
ini_set('session.gc_maxlifetime', 3600*24*7);
session_start();



require_once("include/FirePHPCore/FirePHP.class.php");

ob_start();

/*
$var = array('i'=>10, 'j'=>20);

$firephp = FirePHP::getInstance(true);
$firephp->log($var, 'Iterators');
*/



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
    <script type="text/javascript" src="include/sideBarControlers.js"></script>
    <script type="text/javascript" src="include/mainPannelControlers.js"></script>
    <script type="text/javascript" src="include/formsControlers.js"></script>
    <script type="text/javascript" src="include/angular-translate.min.js"></script>
    <script type="text/javascript" src="include/translation.js"></script>
    <script type="text/javascript" src="include/ui-bootstrap-tpls-0.6.0.min.js"></script>

    <body>
        <div id="sideBar" class="magictime slideLeftRetourn" ng-controller="sideBarCtrl">
          <div style="direction:ltr;"> <!-- Just a trick to get the scroll bar on the left -->
            <div><img id="logo" ng-click="displayHome()" src="include/images/logo3_empty.png"></div><br>
            <div>
              {{'CHOOSE_LANGUAGE' | translate}}
              <img src="include/images/flags/GB.png" ng-click="changeLanguage('en')"/>
              <img src="include/images/flags/FR.png" ng-click="changeLanguage('fr')"/>
            </div>
            <div class="sectionTitle">{{'USERS' | translate}}</div><br>
            <div id="listUsers" ng-controller="userListCtrl">
				      <div class="user" ng-class="getClass('User', user.name)" ng-repeat="user in userList" ng-click="click(user)">{{user.name}}</div>
          	</div>
            <div class="sectionTitle" ng-class="getClass('Add new')" id="addUser" ng-click="addUser()">{{'ADD_USER' | translate}}</div>
            <br>
            <br><br>
            <div class="sectionTitle">{{'SERVICES' | translate}}</div><br>
            <div id="listServices" ng-controller="serviceListCtrl">
				      <div class="sideBarLine" id="{{module.name}}" ng-click="click(module)" ng-class="getClass('Service', module.name)" ng-repeat="module in moduleList">
					      <div class="mainModule">{{module.name}}</div>
					      <div class="bool-slider" ng-class="{true : module.activated, false : !module.activated}" ng-click="clickBoolean(module)"><div class="inset"><div class="control"></div></div></div>
				      </div>
			      </div>
            <br><br><br><br><br>
            <div class="sectionTitle test">Refresh</div>
          </div>
        </div>

        <div id="mainPannel" class="magictime slideUpRetourn" ng-controller="mainPannelCtrl">

		<div ng-controller="tabsControler">
			<div id="tabs" ng-show="needTabs">
				<tabset>
					<tab class="tab-anim" ng-repeat="tab in tabs" select="changeTab(tab)" heading="{{tab.title}}" active="tab.active" disabled="tab.disabled">

						<!--<div id="forms" ng-bind-html="tab.content"></div>-->
						<div ng-include="'tab_template.html'"></div>
					</tab>
				</tabset>
			</div>
		</div>

		<div id="unicTab" ng-hide="needTabs" ng-controller="unicTabCtrl">
			<div ng-include="'unic_tab_template.html'"></div>
		</div>

        </div> 
    </body>


	<script type="text/ng-template" id="tab_template.html">
		<div ng-switch on="activeSubModule">
			<div ng-switch-when="general" ng-controller="formCtrl">
				<form id="container" ng-if="formNotEmpty()" ng-submit="submit()">
					<div ng-repeat="item in content" ng-include="'form_template.html'"></div>
					<input type="submit" value="Save">
				</form>
			</div>
			<div ng-switch-default>
				<select class="instanceSelector" size="10" multiple="no"> 
					<option ng-repeat="instance in instancesList" ng-click="click(instance)">{{instance.name}}</option>
					<option ng-click="add()">Add new</option>
				</select>
				<div class="instanceFormDiv" ng-controller="formCtrl">
					<form id="container" ng-if="formNotEmpty()" ng-submit="submit()">
						<div ng-repeat="item in content" ng-include="'form_template.html'"></div>
						<input type="submit" value="Save">
						<input type="button" ng-click="delete()" value="Delete">
					</form>
				</div>
			</div>
		</div>
	</script>



	<script type="text/ng-template" id="unic_tab_template.html">
		<div ng-switch on="page">
			<div ng-switch-when="home"><p>{{home}}</p></div>
			<div ng-switch-when="form">
				<form id="container" ng-submit="submit()" ng-controller="formCtrl">
					<div ng-repeat="item in content" ng-include="'form_template.html'"></div>
					<input type="submit" value="Save">
					<input type="button" ng-click="delete()" value="Delete">
				</form>
			</div>
			<div ng-switch-default>an error accured</div>
		</div>
	</script>


	<script type="text/ng-template" id="form_template.html">
		<div ng-switch on="item.content_type">
			<div ng-switch-when="string">
				<span ng-if="item.title">{{item.title}} : </span>
				<input name={{item.title}} type="text" ng-model="item.value">
			</div>
			<div ng-switch-when="number">
				<span ng-if="item.title">{{item.title}} : </span>
				<input name={{item.title}} type="text" ng-model="item.value">
			</div>
			<div ng-switch-when="bool">
				<span ng-if="item.title">{{item.title}} : </span>
				<div class="bool-slider" style="position: relative; display: inline-block; left: 10px;" 
					ng-class="{true : item.value, false : !item.value}"
					ng-click="item.value = !item.value">
						<div class="inset"><div class="control"></div></div>
				</div>
			</div>
			<div ng-switch-when="array">
				<span ng-if="item.title">{{item.title}} : </span>
				<div style="display: inline-block;">
					<div style="display: inline-block;" ng-repeat="item in item.value">
						<div style="display: inline-block;" ng-include="'form_template.html'"></div>
						<input type="button" ng-click="deleteItem($parent.$index, $index)" value="-"></input>
					</div>
					<input type="button" ng-click="addNewElement(item)" value="+"></input>
				</div>
			</div>
			<div ng-switch-when="hash">
				<span ng-if="item.title">{{item.title}} : </span>
				<div style="display: inline-block; border: 1px #000000 solid;">
					<div style="display: inline-block;" ng-repeat="item in item.value">
						<div style="display: inline-block;" ng-include="'form_template.html'"></div>
					</div>
				</div>
			</div>
			<div ng-switch-default>
        <!--Unknown field. Type : {{item.content_type}}; Name : {{item.name}}; Value : {{item.value}}-->
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

          $translate.use('en');
        });
        
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
          //alert(userList);
        });
    </script>

</html>
