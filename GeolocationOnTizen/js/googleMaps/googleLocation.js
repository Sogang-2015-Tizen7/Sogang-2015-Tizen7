/*******************************************************************************
 * @author Tomasz Scislo <<ahref='mailto:t.scislo@samsung.com'>t.scislo@samsung.com</a>>
 * @author Lukasz Jagodzinski <<ahref='mailto:l.jagodzinsk@samsung.com'>l.jagodzinsk@samsung.com</a>>
 * Copyright (c) 2013 Samsung Electronics All Rights Reserved.
 ******************************************************************************/

var googleLocation = (function ($, logger, view, network, ajax) {
    var appKey, internetConnectionCheck;

    appKey = "AIzaSyDdKjhStoKF6t0xxA_hFxYBmKrEb77b-nQ";

    /**
     * Asynch method to check the network connection
     * @private
     */
    internetConnectionCheck = function () {
        network.isInternetConnection(function (isConnection) {
            if (!isConnection) {
                view.hideLoader();
                view.showPopup("No Internet connection. Application may not work properly.");
            }
        });
    };
    return {
        /**
         * Provides initialization for the app
         */
        initialize: function () {
            var that = this;
            ajax();
            $.extend($.mobile, {
                defaultPageTransition: "flip",
                loadingMessageTextVisible: true,
                pageLoadErrorMessage: "Unable to load page",
                pageLoadErrorMessageTheme: "d",
                touchOverflowEnabled: true,
                loadingMessage: "Please wait...",
                allowCrossDomainPages: true,
                ajaxEnabled: false
            });
            logger.info("googleLocation.initialize()");
            internetConnectionCheck();
            $('#main').live('pageshow', function () {
                internetConnectionCheck();
                $(this).find("li#myLocation").bind({
                    click: function (event) {
                        event.preventDefault();
                        view.showLoader();
                        that.getCurrentLocation();
                    }
                });
            });
            $('#positionToMap').live('pageshow', function () {
                logger.info("Your Postion in Google Map View");
                internetConnectionCheck();
                if (navigator && navigator.geolocation && navigator.geolocation.getCurrentPosition) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        view.hideLoader();
                        // Currently Tizen returns coords as 0 0 and we should treat this as an error
                        if (position.coords.latitude === 0 && position.coords.longitude === 0) {
                            view.showPopup('Unable to acquire your location');
                        } else {
                        	that.createMapForGivenContainer("map_canvas_position", {
                                zoom: 17,
                                lat: position.coords.latitude,
                                lon: position.coords.longitude,
                                streetViewControl: false,
                                mapTypeId: google.maps.MapTypeId.HYBRID
                            });
                            view.showPopup('Latitude: ' + position.coords.latitude + "<br />" + 'Longitude: ' + position.coords.longitude);
                        }
                    }, function (error) {
                        view.hideLoader();
                        view.showPopup('Unable to acquire your location');
                        logger.err('GPS error occurred. Error code: ', JSON.stringify(error));
                    });
                } else {
                    view.hideLoader();
                    view.showPopup('Unable to acquire your location');
                    logger.err('No W3C Geolocation API available');
                }
            });
            $('#seoul').live('pageshow', function () {
                logger.info("South Korea Google Map View");
                internetConnectionCheck();
                that.createMapForGivenContainer("map_canvas", {
                    zoom: 6,
                    lat: 37.3359,
                    lon: 126.5840,
                    streetViewControl: false,
                    mapTypeId: google.maps.MapTypeId.HYBRID
                });
            });
            $('#searchForPlaces').live('pageshow', function () {
                internetConnectionCheck();
                $("#searchLocation").unbind().keyup(function (event) {
                    var location;
                    // Enter key event handled
                    if (event.keyCode === 13) {
                        logger.info('Searching for', $(this).val());
                        internetConnectionCheck();
                        location = encodeURIComponent($(this).val());
                        $.ajax({
                            url: 'http://maps.googleapis.com/maps/api/geocode/json?address=' + location + '&sensor=true',
                            success: function (data) {
                                var foundLocation, map, southWest, northEast, bounds;

                                logger.info(data);
                                logger.info("Status", data.status);
                                if (google.maps.GeocoderStatus.OK === data.status && data.results.length !== 0) {
                                    // We take into account only first result returned from Google Maps API
                                    foundLocation = data.results[0];
                                    logger.info(foundLocation.geometry.location.lat);
                                    logger.info(foundLocation.geometry.location.lng);
                                    try {
                                        map = that.createMapForGivenContainer("searchMap", {
                                            zoom: 6,
                                            lat: foundLocation.geometry.location.lat,
                                            lon: foundLocation.geometry.location.lng,
                                            mapTypeId: google.maps.MapTypeId.HYBRID,
                                            streetViewControl: false
                                        });
                                        southWest = new google.maps.LatLng(foundLocation.geometry.bounds.southwest.lat, foundLocation.geometry.bounds.southwest.lng);
                                        northEast = new google.maps.LatLng(foundLocation.geometry.bounds.northeast.lat, foundLocation.geometry.bounds.northeast.lng);
                                        bounds = new google.maps.LatLngBounds(southWest, northEast);
                                        map.fitBounds(bounds);
                                    } catch (e) {
                                        view.showPopup('Some errors occurred while creating a map');
                                        logger.err('Some errors occurred while creating a map');
                                    }
                                } else {
                                    view.showPopup("Unable to find this location!");
                                }
                            }
                        });
                    }
                });
            });

            $('#getDirections').live('pageshow', function () {
                var createStartMap;

                internetConnectionCheck();
                createStartMap = function () {
                    return that.createMapForGivenContainer("directionsMap", {
                        zoom: 6,
                        lat: 37.3359,
                        lon: 126.5840,
                        mapTypeId: google.maps.MapTypeId.HYBRID,
                        streetViewControl: false
                    });
                };
                createStartMap();
                $(this).find("#getDirButton").bind({
                    click: function (event) {
                        var request;

                        event.preventDefault();
                        view.showLoader();
                        internetConnectionCheck();
                        request = {
                            origin: $('#locationFrom').val(),
                            destination: $('#locationTo').val(),
                            travelMode: google.maps.TravelMode.DRIVING
                        };
                        that.calculateDirections(request, createStartMap());
                    }
                });
            });

            $('#findNearest').live('pageshow', function () {
                internetConnectionCheck();
                $.ajax({
                    url: 'WebContent/places.json',
                    success: function (data) {
                        var places, initializeCityList, distanceModule;

                        places = data.places;
                        logger.info(places);
                        initializeCityList = function (places) {
                            var i;

                            $('#cityList').html('');
                            for (i = 0; i < places.length; i++) {
                                that.createCityUI(places[i].name, "O km");
                            }
                        };
                        initializeCityList(places);
                        distanceModule = googleDistance(places);
                        $("#locationToFindNearest").unbind().keyup(function (event) {
                            // Enter key event handled
                            if (event.keyCode === 13) {
                                internetConnectionCheck();
                                logger.info($(this).val());
                                view.showLoader();
                                distanceModule.getDescendingOrder($(this).val(), function (found) {
                                    var i;

                                    logger.info(found);
                                    $('#cityList').html('');
                                    for (i = 0; i < found.length; i++) {
                                        that.createCityUI(found[i].name, found[i].distance.text);
                                    }
                                    view.hideLoader();
                                }, function () {
                                    initializeCityList(places);
                                    view.showPopup("Unable to determine distance");
                                    view.hideLoader();
                                });
                            }
                        });
                    }
                });
            });

            $('#streetView').live('pageshow', function () {
                logger.info("view");
                internetConnectionCheck();
                $("#locationToStreetView").unbind().keyup(function (event) {
                    var location;

                    // Enter key event handled
                    if (event.keyCode === 13) {
                        internetConnectionCheck();
                        location = $(this).val();
                        if ($('#streetView').find('#streetViewImg').length === 0) {
                            $('#streetView').append($('<img id="streetViewImg" alt="StreetView" />'));
                        }
                        $('#streetViewImg').attr('src', 'http://maps.googleapis.com/maps/api/streetview?size=640x640&location=' + location + '&sensor=true&key=' + appKey);
                    }
                });
            });
            view.getScreenHeight();
            view.getScreenWidth();
        },

        createCityUI: function (name, distance) {
            $('#cityList').append('<li><a href="#findNearest">' + name + '<span class="ui-li-count">' + distance + '</span></a></li> ').listview("refresh");
        },

        /**
         * Method that can be used for basic google.maps.Map creation for given container
         * @param container
         * @param options
         * @returns {Object} google.maps.Map
         */
        createMapForGivenContainer: function (container, options) {
            var mapOptions, map;

            mapOptions = {
                center: new google.maps.LatLng(options.lat, options.lon),
                zoom: options.zoom,
                mapTypeId: options.mapTypeId,
                streetViewControl: options.streetViewControl
            };
            map = new google.maps.Map(document.getElementById(container), mapOptions);
            return map;
        },

        /**
         * @param request {Object} - JSON with options for route calculation
         * @param map {Object} - map to draw the directions on
         * @returns
         */
        calculateDirections: function (request, map) {
            var directionsService, directionsDisplay;

            directionsService = new google.maps.DirectionsService();
            directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);
            directionsService.route(request, function (result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                } else {
                    view.showPopup('Unable to get directions');
                    logger.err('Unable to get directions');
                }
                view.hideLoader();
            });
        },

        /**
         * Method that can be used to get current device geolocation according to W3C Geolocation API
         * @returns
         */
        getCurrentLocation: function () {
            logger.info('getCurrentLocation');
            if (navigator && navigator.geolocation && navigator.geolocation.getCurrentPosition) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    view.hideLoader();
                    // Currently Tizen returns coords as 0 0 and we should treat this as an error
                    if (position.coords.latitude === 0 && position.coords.longitude === 0) {
                        view.showPopup('Unable to acquire your location');
                    } else {
                        view.showPopup('Latitude: ' + position.coords.latitude + "<br />" + 'Longitude: ' + position.coords.longitude);
                    }
                }, function (error) {
                    view.hideLoader();
                    view.showPopup('Unable to acquire your location');
                    logger.err('GPS error occurred. Error code: ', JSON.stringify(error));
                });
            } else {
                view.hideLoader();
                view.showPopup('Unable to acquire your location');
                logger.err('No W3C Geolocation API available');
            }
        }
    };
}($, tlib.logger, tlib.view, tlib.network, tlib.ajax));

googleLocation.initialize();