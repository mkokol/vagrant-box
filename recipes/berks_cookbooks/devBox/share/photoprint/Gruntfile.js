module.exports = function (grunt) {
    'use strict';

    require('time-grunt')(grunt);

    var fs = require('fs');
    var ini = require('ini');
    var config = ini.parse(fs.readFileSync('./application/configs/application.ini', 'utf-8'));
    var v = config.production.version;

    var conf = {
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            options: {
                spawn: false
            },
            js: {
                files: [
                    {
                        src: [
                            'public/scripts/lib/jquery.min.js',
                            'public/scripts/lib/jquery.form.js',
                            'public/scripts/lib/jquery.url.js',
                            'public/scripts/lib/common.js',
                            'public/scripts/lib/m-box.js'
                        ],
                        dest: 'public/scripts/main.js'
                    }
                ]
            }
        },
        uglify: {
            options: {
                mangle: false,
                livereload: true,
                spawn: false
            },
            js: {
                files: {
                    'public/widgets/featureList/code.min.js': ['public/widgets/featureList/code.js'],
                    'public/widgets/jscrollpane/code.min.js': ['public/widgets/jscrollpane/code.js'],
                    'public/widgets/social/code.min.js': ['public/widgets/social/code.js'],
                    'public/widgets/tablesorter/code.min.js': ['public/widgets/tablesorter/code.js'],
                    'public/widgets/texteditor/code.min.js': ['public/widgets/texteditor/code.js']
                }
            }
        },
        sass: {
            compile: {
                options: {
                    style: 'compressed',
                    spawn: false
                },
                files: {
                    'public/widgets/featureList/style.css': 'public/widgets/featureList/style.scss',
                    'public/widgets/jscrollpane/style.css': 'public/widgets/jscrollpane/style.scss',
                    'public/widgets/social/style.css': 'public/widgets/social/style.scss',
                    'public/widgets/tablesorter/style.css': 'public/widgets/tablesorter/style.scss',
                    'public/widgets/texteditor/style.css': 'public/widgets/texteditor/style.scss'
                }
            }
        },
        watch: {
            options: {
                dateFormat: function(time) {
                    grunt.log.writeln('The watch finished in ' + time + 'ms at' + (new Date()).toString());
                    grunt.log.writeln('Waiting for more changes...');
                },
                spawn: false
            },
            gruntfile: {
                files: 'Gruntfile.js',
                tasks: ['build']
            },
            src: {
                files: ['public/**/*.js', 'public/**/*.scss'],
                tasks: ['default']
            }
        }
    };

    conf["uglify"]["js"]["files"]["public/scripts/main.min." + v + ".js"] = ['public/scripts/main.js'];
    conf["sass"]["compile"]["files"]["public/stylesheets/main." + v + ".css"] = "public/stylesheets/main.scss";

    grunt.initConfig(conf);

    grunt.registerTask('build', function() {
        grunt.log.write("Start Grunt");

        grunt.task.run('concat');
        grunt.task.run('sass');
        grunt.task.run('uglify');
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['build']);
};
