"use strict";

module.exports = function (grunt) {

    // We need to include the core Moodle grunt file too, otherwise we can't run tasks like "amd".
    require("grunt-load-gruntfile")(grunt);
    grunt.loadGruntfile("../../Gruntfile.js");

    // Load all grunt tasks.
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.loadNpmTasks("grunt-contrib-jshint");

    /**
     * Function to generate the destination for the uglify task
     * (e.g. build/file.min.js). This function will be passed to
     * the rename property of files array when building dynamically:
     * http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
     *
     * @param {String} destPath the current destination
     * @param {String} srcPath the  matched src path
     * @return {String} The rewritten destination path.
     */
    var uglifyRename = function(destPath, srcPath) {
        destPath = srcPath.replace('src', 'build');
        destPath = destPath.replace('.js', '.min.js');
        return destPath;
    };

    grunt.initConfig({
        watch: {
            less: {
                // If any .less file changes in directory "less" then run the "less" task.
                files: "less/*.less",
                tasks: ["less"]
            },
            uglify: {
                files: 'amd/src/*.js',
                tasks: ["uglify", "jshint"]
            }
        },
        less: {
            // Production config is also available.
            development: {
                options: {
                    // Specifies directories to scan for @import directives when parsing.
                    // Default value is the directory of the source, which is probably what you want.
                    paths: ["less/"],
                    compress: true
                },
                files: {
                    "styles.css": "less/styles.less"
                }
            }
        },
        uglify: {
            amd: {
                files: [{src: 'amd/src/*.js', expand: true, rename: uglifyRename}],
                options: {report: 'none'}
            }
        },
        jshint: {
            files: "amd/src/*.js",
        }
    });
    // The default task (running "grunt" in console).
    grunt.registerTask("default", ["watch"]);
};