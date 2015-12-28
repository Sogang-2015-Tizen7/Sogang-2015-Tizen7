/****************************************************************************************************************************************************
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * This module is responsible for registering to receive deviceorientation events & getting the alpha, beta, gamma angles of deviceorientation
 *
 * @author Zaneta Szymanska <<ahref='mailto:z.szymanska@samsung.com'>z.szymanska@samsung.com</a>>
 *
 * ************************************************************************************************
 *
 * Copyright (c) 2012 Samsung Electronics All Rights Reserved.
 *
 *****************************************************************************************************************************************************/
/**
 * @version 0.0.3 Version compatible with Tizen SDK 2.1 Nectarine final
 */
"use strict";
if (typeof tlib.gyroscope === "undefined") {
    tlib.gyroscope = function() {
        var alpha = 0;
        var beta = 0;
        var gamma = 0;

        return {
            /**
             * Initializes the tizen.gyroscope module
             */
            init : function() {
                if (window.addEventListener) {
                    window.addEventListener('deviceorientation', function(e) {
                        alpha = e.alpha;
                        beta = e.beta;
                        gamma = e.gamma;
                    });

                    return true;

                } else {
                    return false;
                }
            },

            /**
             * @returns {Number} alpha Alfa angle's value
             */
            getAlpha : function() {
                return alpha;
            },

            /**
             * @returns {Number} beta Beta angle's value
             */
            getBeta : function() {
                return beta;
            },

            /**
             * @returns {Number} gamma Gamma angle's value
             */
            getGamma : function() {
                return gamma;
            }
        };
    }();
} else {
    console.error("Unable to create tlib.gyroscope module");
}