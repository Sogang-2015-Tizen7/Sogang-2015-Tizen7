/*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * Tizen application life cycle handler
 *
 * @author Tomasz Scislo <<ahref='mailto:t.scislo@samsung.com'>t.scislo@samsung.com</a>>
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
if (typeof tlib.visibility === "undefined") {
    tlib.visibility = function() {

        var states = {
            HIDDEN : "hidden",
            VISIBLE : "visible",
            PRERENDER : "prerender",
            UNLOADED : "unloaded"
        };

        var visibilityChanged = function() {
            switch (document.webkitVisibilityState) {
            case states.PRERENDER:
            case states.UNLOADED:
                break;
            case states.HIDDEN:
                onApplicationHidden();
                break;
            case states.VISIBLE:
                onApplicationVisible();
                break;
            default:
                break;
            }
        };

        var onApplicationVisible = function() {
        };

        var onApplicationHidden = function() {
        };

        document.addEventListener("webkitvisibilitychange", visibilityChanged);

        return {

            /**
             * Method to register onApplicationVisible event
             *
             * @param callback {Function} - method to be called each time
             *        application goes to foreground
             */
            onApplicationVisible : function(callback) {
                onApplicationVisible = callback;
            },

            /**
             * Method to register onApplicationHidden event
             *
             * @param callback {Function} - method to be called each time
             *        application goes to background
             */
            onApplicationHidden : function(callback) {
                onApplicationHidden = callback;
            }
        };
    }();
} else {
    console.error("Unable to create tlib.visibility module");
}