/*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 *
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * This custom logger module allows to set log level and write on default browser console.
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
if (typeof tlib.logger === "undefined") {
    tlib.logger = function(options) {
        var types = {
            ERROR : {
                name : "error",
                level : 0
            },
            WARNING : {
                name : "warn",
                level : 1
            },
            INFO : {
                name : "info",
                level : 2
            },
            NONE : {
                name : "none",
                level : -1
            }
        };

        if (typeof options === "undefined") {
            options = {
                logLevel : types.INFO.level
            };
        }

        var log = function(type, args) {
            if (type.level <= options.logLevel) {
                if (typeof console !== 'undefined') {
                    console[type.name](args);
                }
            }
        };

        return {
            err : function() {
                for (var i = 0; i < arguments.length; i++) {
                    log(types.ERROR, arguments[i]);
                }
            },
            warn : function() {
                for (var i = 0; i < arguments.length; i++) {
                    log(types.WARNING, arguments[i]);
                }
            },
            info : function() {
                for (var i = 0; i < arguments.length; i++) {
                    log(types.INFO, arguments[i]);
                }
            },
            changeLogLevel : function(level) {
                if (typeof level !== "number") {
                    console.error("Level is not a number!");
                }
                options.logLevel = level;
            }
        };
    }({
        // default log level set to types.INFO
        logLevel : 2
    });
} else {
    console.error("Unable to create tlib.logger module");
}