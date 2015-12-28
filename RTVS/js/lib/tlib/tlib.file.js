/****************************************************************************************************************************************************
 * This module is a part of Tizen Lib (tlib) developed in SRPOL
 *
 * Using this module you can: create & resolve file (or only resolve, if created), write/append to the file, read from the file.
 *
 * @author Zaneta Szymanska <<ahref='mailto:z.szymanska@samsung.com'>z.szymanska@samsung.com</a>>
 *
 * ************************************************************************************************
 *
 * Copyright (c) 2012 Samsung Electronics All Rights Reserved.
 *
 *****************************************************************************************************************************************************/
/**
 * @version 0.0.3
 * Version compatible with Tizen SDK 2.1 Nectarine final
 *
 * REQUIRED
 * Privileges at config.xml file:
 *    <tizen:privilege name="http://tizen.org/privilege/filesystem.read"/>
 *    <tizen:privilege name="http://tizen.org/privilege/filesystem.write"/>
 */
/**
 * @param options {Object} Information about the file
 * @param options.filename {String} Name of the file
 * @param options.virtualRoot {String} Name of the virtual root we want to
 *        access
 * @param options.success {Function} Called when the directory location and the
 *        file have been resolved
 * @param options.error {Function} Called if an error or exception occurred
 */
"use strict";
tlib.file = function(options) {
    // file handle for resolved file
    var file;
    // file handle for resolved directory location
    var gDocumentsDir;

    if (typeof options === undefined || !options.filename
            || !options.virtualRoot || !options.success || !options.error) {
        console.error("Define all parameters of options Object !");
    }

    // name of the file
    var filename = options.filename;

    try {
        tizen.filesystem.resolve(options.virtualRoot, function(dir) {
            gDocumentsDir = dir;
            if (resolve()) {
                options.success();
            }
        }, function(e) {
            console.error("tizen.filesystem.resolve() error: " + e.message);
            options.error();
        }, "rw");
    } catch (exc) {
        console.error("tizen.filesystem.resolve() exception: " + exc.message);
        options.error();
    }

    /**
     * Creates file
     *
     * @return {Boolean} Indicates if file was created
     */
    var create = function() {
        try {
            gDocumentsDir.createFile(filename);
            return true;
        } catch (exc) {
            if (exc.name === "IOError") {
                console.info("File already exists");
                return true;
            } else {
                console.error("Create file exception: " + exc.message);
                return false;
            }
        }
    };

    /**
     * Resolves an existing file to file handle
     *
     * @return {Boolean} Indicates if file was resolved
     */
    var resolve = function() {
        try {
            file = gDocumentsDir.resolve(filename);
            return true;
        } catch (exc) {
            if (exc.name === "NotFoundError" || "IOError") {
                console
                        .warn('File not found, so it will be created (exc message: '
                                + exc.message + ')');

                if (create() && resolve()) {
                    return true;
                }
            } else {
                console.error('resolve() file exception: ' + exc.message);
                return false;
            }
        }
    };

    /**
     * Opens the file in write mode and writes specified string to this file
     *
     * @param {String} text Text to be written in the file
     * @param {Object} callback Contains success and error callback functions
     * @param {Function} callback.success Called when the file has been opened
     *        in write mode
     * @param {Function} callback.error Called if an error or exception occurred
     */
    this.write = function(text, callback, encoding) {
        try {
            file.openStream('w', function(fileStream) {
                try {
                    if (encoding && encoding === "base64") {
                        fileStream.writeBase64(text);
                    } else {
                        fileStream.write(text);
                    }
                    fileStream.close();
                    callback.success();
                } catch (exc) {
                    console.error("this.write write exc: " + exc.message);
                    callback.error();
                }
            }, function(e) {
                console.error("this.write openStream error: " + e.message);
                callback.error();
            });
        } catch (exc) {
            console.error('this.write exception: ' + exc.message);
            callback.error();
        }
    };

    /**
     * Opens the file in append mode and appends specified string to this file
     *
     * @param text {String} - Text to be append to the file
     * @param callback {Object} Contains success and error callback functions
     * @param callback.success {Function} Called when the file has been opened
     *        in append mode
     * @param callback.error {Function} Called if an error or exception occurred
     */
    this.append = function(text, callback, encoding) {
        try {
            file.openStream('a', function(fileStream) {
                try {
                    if (encoding && encoding === "base64") {
                        fileStream.writeBase64(text);
                    } else {
                        fileStream.write(text);
                    }
                    fileStream.close();
                    callback.success();
                } catch (exc) {
                    console.error("this.append write exc: " + exc.message);
                }
            }, function(e) {
                console.error("this.append openStream error: " + e.message);
                callback.error();
            });
        } catch (exc) {
            console.error('this.append exception: ' + exc.message);
            callback.error();
        }
    };

    /**
     * Opens the file in read mode and reads its content
     *
     * @param callback {Object} Contains success and error callback functions
     * @param callback.success {Function} Called when the file has been opened
     *        in read mode, takes one parameter: text read from the file
     * @param callback.error {Function} Called if an error or exception occurred
     */
    this.read = function(callback, encoding) {
        var textFromFile = '';

        try {
            // Opens the file in the given mode supporting the given encoding
            file.openStream('r', function(fileStream) {
                try {
                    if (encoding && encoding === "base64") {
                        textFromFile = fileStream
                                .readBase64(fileStream.bytesAvailable);
                    } else {
                        textFromFile = fileStream
                                .read(fileStream.bytesAvailable);
                    }
                    fileStream.close();
                    callback.success(textFromFile);
                } catch (exc) {
                    console.error('Read function exception: ' + exc.message);
                    callback.error();
                }
            }, function(e) {
                console.error("openStream error: " + e.message);
                callback.error();
            });
        } catch (exc) {
            console.error('file.openStream() exception: ' + exc.message);
            callback.error();
        }
    };

    /**
     * Gets name of the file
     */
    this.getFilename = function() {
        return filename;
    };

    /**
     * Gets virtual root in which the file was saved
     */
    this.getVirtualRoot = function() {
        return options.virtualRoot;
    };
};