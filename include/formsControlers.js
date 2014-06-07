/*
 Copyright (C) 2013-2014 Clément Roblot

This file is part of lamadmin.

Lamadmin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Lamadmin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Lamadmin.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * Controler of the forms of the instances
 *
 * @class formCtrl
 */
function formCtrl($scope, $rootScope, $http) {

    $scope.content = "";
    $scope.moduleName = "";
    $scope.subModuleName = "";
    $scope.instanceName = "";


    /**
     * Event called to fetch the form of an instance
     *
     * @event getFormEvent
     */
    $scope.$on('getFormEvent', function(event, args) {

        $scope.moduleName = args[1];
        $scope.subModuleName = args[2];
        $scope.instanceName = args[3];

        var donnees = $.param({
            moduleName: args[1],
            subModuleName: args[2],
            instanceName: args[3]
        });

        $http({
            method: "POST",
            url: "/ajax/getFormInstance.php",
            data: donnees,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
            .success(function(response) {

                //alert(JSON.stringify(response));
                $scope.content = response;
            })

        .error(function(data, status, headers, config) {

            alert("error when getting the liste of the form of the user");
        });
    });


    /**
     * Methode caled to empty the current form
     *
     * @method clearForm
     */
    $scope.clearForm = function() {

        $scope.content = "";
    };


    /**
     * Methode caled to submit the current form to the server (and so save it to the Rexify file)
     *
     * @method submit
     */
    $scope.submit = function() {


        //alert(JSON.stringify($scope.content));

        var donnees = $.param({
            moduleName: $scope.moduleName,
            subModuleName: $scope.subModuleName,
            instanceName: $scope.instanceName,
            values: $scope.content
        });

        $http({
            method: "POST",
            url: "/ajax/setFormInstance.php",
            data: donnees,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
            .success(function(response) {

                //alert(response);

                if ($scope.moduleName == "user") {

                    $rootScope.$broadcast("updateUsersList", []);
                    angular.element($("#sideBar")).scope().selectedLine = "";
                    angular.element($("#mainPannel")).scope().loadHome();
                } else {
                    $rootScope.$broadcast("updateCurrentTab", [$scope.subModuleName]);
                    $scope.clearForm();
                }
            })

        .error(function(data, status, headers, config) {

            alert("error when setting the form");
        });
    };


    /**
     * Methode caled to delete the form (and also in the Rexify file)
     *
     * @method delete
     */
    $scope.delete = function() {

        var donnees = $.param({
            moduleName: $scope.moduleName,
            subModuleName: $scope.subModuleName,
            instanceName: $scope.instanceName
        });

        $http({
            method: "POST",
            url: "/ajax/deleteInstance.php",
            data: donnees,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
            .success(function(response) {


                if ($scope.moduleName == "user") {

                    angular.element($("#mainPannel")).scope().loadHome();
                    $rootScope.$broadcast("updateUsersList", []);

                } else {

                    $rootScope.$broadcast("updateCurrentTab", [$scope.subModuleName]);
                    $scope.clearForm();
                }
            })

        .error(function(data, status, headers, config) {

            alert("error when getting the liste of the form of the user");
        });
    };

    /**
     * Methode caled in array arguments to add a new slot in the array
     *
     * @method addNewElement
     * @param {Array} array The array on which we clicked
     */
    $scope.addNewElement = function(array) {

        template = angular.fromJson(array.subType);
        array.value.push(template);
    };

    /**
     * Methode caled to remove one element from an array in a form
     *
     * @method deleteItem
     * @param {String} arrayIndex The number of the array we clicked on
     * @param {String} itemIndex The number of the argument in the array we want to delete
     */
    $scope.deleteItem = function(arrayIndex, itemIndex) {

        $scope.content[arrayIndex].value.splice(itemIndex, 1);
    };

    /**
     * Methode caled to test if the form is empty or not
     *
     * @method formNotEmpty
     * @return {Bool} True if empty, false if not.
     */
    $scope.formNotEmpty = function() {

        if ($scope.content.length) return true;
        else return false;
    };

}
