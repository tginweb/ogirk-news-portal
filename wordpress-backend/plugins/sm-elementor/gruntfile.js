module.exports = function(grunt) {


    dist_js_merge = {
        '<%= dirs.dist %>/js/common.js':                     ['assets-src/js/common/*.js'],
        '<%= dirs.dist %>/js/editor.js':                     ['assets-src/js/editor/module.js', 'assets-src/js/editor/*.js'],
        '<%= dirs.dist %>/js/frontend.js':                   ['assets-src/js/frontend/*.js'],
        '<%= dirs.dist %>/js/query-widget.js':               ['assets-src/js/widget/query/*.js']
    };

    extensions = [

    ];

    config = {

        dirs: {
            src:    'assets-src',
            dist:   'assets'
        },

        copy: {
            core_js: {
                files: [
                    //{cwd: '<%= dirs.src %>/js/backend', src: ['*.js'],  dest: '<%= dirs.dist %>/js/backend',  expand: true},
                    //{cwd: '<%= dirs.src %>/js/frontend', src: ['*.js'],  dest: '<%= dirs.dist %>/js/frontend',  expand: true}
                ]
            }
        },

        concat: {
            core_js: {
                options: {
                    separator: ';'
                },
                files: dist_js_merge
            },

            extensions_js: {
                options: { separator: ';'}, files: {}
            }
        },


        sass: {
            core: {
                files: [
                    {cwd: '<%= dirs.src %>/scss', src: ['*.scss'],  dest: '<%= dirs.dist %>/css', ext: '.css', expand: true}
                ]
            }
        },

        uglify: {
            core: {
                files: [
                    {cwd: '<%= dirs.dist %>/js', src: ['**/*.js', '!*.min.js'],  dest: '<%= dirs.dist %>/js', ext: '.min.js', expand: true}
                ]
            },
            extensions: {files: []}
        },

        cssmin: {
            core: {
                files: [
                    {cwd: '<%= dirs.dist %>/css', src: ['**/*.css', '!*.min.css'],  dest: '<%= dirs.dist %>/css', ext: '.min.css', expand: true}
                ]
            },
            extensions: {files: []}
        },


        watch: {
            core_css: {
                files: ['<%= dirs.src %>/**/*.scss'],
                tasks: ['sass:core','cssmin:core']
            },
            core_js: {
                files: ['<%= dirs.src %>/**/*.js'],
                tasks: ['copy:core_js','concat:core_js','uglify:core']
            }
        }
    };


    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['copy','concat','uglify','sass','cssmin']);

};