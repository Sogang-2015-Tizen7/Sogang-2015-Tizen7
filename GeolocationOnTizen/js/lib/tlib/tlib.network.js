/*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * This module is responsible wifi and cellular network detection on Tizen platform.
 *
 * @author Tomasz Scislo <<ahref='mailto:t.scislo@samsung.com'>t.scislo@samsung.com</a>>
 *
 *
 * **************************************************************************************
 *
 * Copyright (c) 2012 Samsung Electronics All Rights Reserved.
 *
 ******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/**
 * @version 0.0.3 Version compatible with Tizen SDK 2.1 Nectarine final
 */
"use strict";
if (typeof tlib.network === "undefined") {
    tlib.network = function() {
        try {
            var deviceCapabilities = tizen.systeminfo.getCapabilities();
        } catch (e) {
            console.error("Unable to get tizen.systeminfo.getCapabilities");
        }
        return {
            /**
             * Checks if WiFi network connection is available
             *
             * @param callback {Function} - This callback is invoked with one
             *        boolean parameter which indicates if WiFi network
             *        connection is available
             */
            isWifiConnection : function(callback) {
                console.info('isWifiConnection');
                var errMsg = "Unable to determine WiFi status";
                if (typeof tizen !== "undefined" && tizen.systeminfo
                        && deviceCapabilities.wifi) {
                    tizen.systeminfo
                            .getPropertyValue(
                                    'WIFI_NETWORK',
                                    function(wifi) {
                                        try {
                                            console.info(JSON.stringify(wifi));
                                            if ((wifi.status === "ON" && wifi.ssid)
                                                    || (typeof wifi.status === "boolean" && wifi.status)) {
                                                console
                                                        .info("WiFi network connection enabled");
                                                callback(true);
                                            } else {
                                                console
                                                        .info("WiFi network connection disabled");
                                                callback(false);
                                            }
                                        } catch (exc) {
                                            console.error(errMsg + 'a');
                                            callback(false);
                                        }
                                    }, function() {
                                        console.error(errMsg + 'b');
                                        callback(false);
                                    });
                } else {
                    console.error(errMsg);
                    callback(false);
                }
            },

            /**
             * Checks if cellular network connection is available
             *
             * @param callback {Function} - This callback is invoked with one
             *        boolean parameter which indicates if cellular network
             *        connection is available
             */
            isCellularNetworkInternetConnection : function(callback) {
                console.info('isCellularNetworkInternetConnection');
                var errMsg = "Unable to determine cellular status";
                if (typeof tizen !== "undefined" && tizen.systeminfo) {
                    tizen.systeminfo
                            .getPropertyValue(
                                    'CELLULAR_NETWORK',
                                    function(cellular) {
                                        console.log("CELLULAR_NETWORK");
                                        try {
                                            console.info("cellular: " + JSON
                                                    .stringify(cellular));
                                            if ((cellular.status === "ON" || (typeof cellular.status === "boolean" && cellular.status))
                                                    && cellular.ipAddress) {
                                                console
                                                        .info("Cellular network connection enabled");
                                                callback(true);
                                            } else {
                                                console
                                                        .info("Cellular network connection disabled");
                                                callback(false);
                                            }
                                        } catch (exc) {
                                            console.error(errMsg);
                                            callback(false);
                                        }
                                    }, function() {
                                        console.error(errMsg);
                                        callback(false);
                                    });
                } else {
                    console.error(errMsg);
                    callback(false);
                }
            },

            /**
             *
             * Method to check if either WiFi or Cellular Internet connection
             * available
             *
             * @param callback {Function} - This callback is invoked with one
             *        boolean parameter which indicates if network connection is
             *        available
             */
            isInternetConnection : function(callback) {
                var that = this;
                console.info('isInternetConnection');
                if (typeof callback !== "function") {
                    console.err("Invalid callback for isInternetConnection");
                    return false;
                }
                var innerCallback = function(isConnection) {
                    /**
                     * If there is WiFi connection invoke callback(true)
                     * immediately If not try with cellular network
                     */
                    if (isConnection) {
                        callback(true);
                    } else {
                        that.isCellularNetworkInternetConnection(callback);
                    }
                };

                this.isWifiConnection(innerCallback);
            }

        };

    }();
} else {
    console.error("Unable to create tlib.network module");
}