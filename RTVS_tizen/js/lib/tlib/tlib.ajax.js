/*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 *
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * Globaj $.ajax configuration for the whole project
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
 * REQUIRES jQuery
 *
 * @version 0.0.3 Version compatible with Tizen SDK 2.1 Nectarine final
 */
"use strict";
if (typeof tlib.ajax === "undefined") {
    tlib.ajax = function() {
        $.ajaxSetup({
            type : "GET",
            timeout : 90000,
            crossDomain : false,
            dataType : "json",
            cache : false,
            async : true,
            beforeSend : function(XMLHttpRequest) {
                $.mobile.loading('show', {
                    theme : "d"
                });
            },

            complete : function(XMLHttpRequest, status) {
                console.info("AJAX|INFO", "Complete");
                $.mobile.loading('hide');
            },

            success : function() {
                console.info("AJAX|INFO", "Success");
            },

            error : function(err) {
                console.error("AJAX|INFO", "Error", err);
                view.showPopup("Server request error");
            }
        });
    };
} else {
    console.error("Unable to create tlib.ajax module");
}