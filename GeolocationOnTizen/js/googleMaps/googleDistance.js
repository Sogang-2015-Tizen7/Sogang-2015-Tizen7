/*******************************************************************************
 * @author Tomasz Scislo <<ahref='mailto:t.scislo@samsung.com'>t.scislo@samsung.com</a>>
 * @author Lukasz Jagodzinski <<ahref='mailto:l.jagodzinsk@samsung.com'>l.jagodzinsk@samsung.com</a>>
 * Copyright (c) 2013 Samsung Electronics All Rights Reserved.
 ******************************************************************************/

var googleDistance = function (locations, options) {
    var service, destLocations, initialDist;

    service = new google.maps.DistanceMatrixService();
    destLocations = locations;
    initialDist = 1000000000;
    options = (typeof options !== "undefined") ? options : {
        travelMode: google.maps.TravelMode.DRIVING
    };
    return {
        /**
         * Method sorts the locations array ascending from the location nearest to orginLocation
         * orginLocation {Object} JSON with lan and lng of orgin location
         * successCB {Function} callback function to invoke for successful
         * location acquisition, two parameters passed: first is JSON with sorted locations array from the place nearest the orgin location
         * errorCB {Function} callback
         * function to invoke for unsuccessful operation
         */
        getDescendingOrder: function (orginLocation, successCB, errorCB) {
            var origin, destParsed, i;

            origin = orginLocation;
            destParsed = [];

            for (i = 0; i < destLocations.length; i++) {
                destParsed[i] = new google.maps.LatLng(destLocations[i].geolocation.lat, destLocations[i].geolocation.lng);
            }

            service.getDistanceMatrix({
                origins: [origin],
                destinations: destParsed,
                travelMode: options.travelMode,
                avoidHighways: false,
                avoidTolls: false
            }, function (response, status) {
                var locationsCopy, sorted, i;

                if (status === google.maps.DistanceMatrixStatus.OK) {
                    if (response.rows.length === 0) {
                        (typeof errorCB === "function") ? errorCB() : console.error("Unable to get location in descending order!");
                        return false;
                    }
                    response = response.rows[0].elements;
                    locationsCopy = locations.slice();
                    for (i = 0; i < locationsCopy.length; i++) {
                        if (response[i].status === "OK") {
                            locationsCopy[i].distance = response[i].distance;
                        } else {
                            locationsCopy = locationsCopy.splice(1, i);
                        }
                    }
                    sorted = locationsCopy.sort(function (a, b) {
                        return a.distance.value - b.distance.value;
                    });
                    if (sorted.length === 0) {
                        (typeof errorCB === "function") ? errorCB() : console.error("Unable to get location in descending order!");
                    } else {
                        successCB(sorted, orginLocation);
                    }
                } else {
                    (typeof errorCB === "function") ? errorCB() : console.error("Unable to get location in descending order!");
                }
            });
        },

        /**
         * Gets nearest location from orginLocation to one of locations in
         * locations array
         * orginLocation {Object} JSON with lan and lng of orgin location
         * successCB {Function} callback function to invoke for successful
         * location acquisition, two parameters passed: first is JSON with found
         * location, second JSON with orgin location
         * errorCB {Function} callback
         * function to invoke for unsuccessful l location acquisition
         */
        getNearest: function (orginLocation, successCB, errorCB) {
            var origin, destParsed, minDistance, i, callback;

            origin      = orginLocation;
            minDistance= initialDist;
            destParsed  = [];

            for (i = 0; i < destLocations.length; i++) {
                destParsed[i] = new google.maps.LatLng(destLocations[i].geolocation.lat, destLocations[i].geolocation.lng);
            }

            callback = function (response, status) {
                var foundId, i;

                if (status === google.maps.DistanceMatrixStatus.OK) {
                    if (response.rows.length === 0) {
                        if (typeof errorCB === "function") {
                            errorCB();
                            console.error("Unable to determine nearest location");
                        }

                        return false;
                    }

                    response = response.rows[0].elements;
                    foundId = 0;

                    for (i = 0; i < response.length; i++) {
                        if (response[i].status === "OK" && response[i].distance.value < minDistance) {
                            minDistance = response[i].distance.value;
                            foundId = i;
                        }
                    }

                    if (initialDist === minDistance) {
                        if (typeof errorCB === "function") {
                            errorCB();
                        } else {
                            console.error("Unable to determine nearest location");
                        }
                    } else {
                        successCB(destLocations[foundId], orginLocation);
                    }
                } else {
                    if (typeof errorCB === "function") {
                        errorCB();
                    } else {
                        console.error("Unable to determine nearest location");
                    }
                }
            };

            service.getDistanceMatrix({
                origins: [ origin ],
                destinations: destParsed,
                travelMode: options.travelMode,
                avoidHighways: false,
                avoidTolls: false
            }, callback);
        }
    };
};