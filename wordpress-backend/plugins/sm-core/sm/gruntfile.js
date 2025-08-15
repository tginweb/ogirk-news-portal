module.exports = function(grunt) {

    extensions = [
        //'sm-vc',
        //'sm-acf',
        //'sm-twig',
        //'sm-elementor'
    ];

    config = {

        dirs: {
            src:    'assets-src',
            dist:   'assets'
        },

        copy: {
            core_js: {
                files: [
                    {cwd: '<%= dirs.src %>/js/backend', src: ['*.js'],  dest: '<%= dirs.dist %>/js/backend',  expand: true},
                    {cwd: '<%= dirs.src %>/js/frontend', src: ['*.js'],  dest: '<%= dirs.dist %>/js/frontend',  expand: true}
                ]
            }
        },

        concat: {
            core_js: {
                options: {
                    separator: ';'
                },
                files: {
                    '<%= dirs.dist %>/js/core.js':   [
                        '<%= dirs.src %>/js/core/lib/*.js',
                        '<%= dirs.src %>/js/core/util/*.js',
                        '<%= dirs.src %>/js/core/jquery-fns.js',
                        '<%= dirs.src %>/js/core/sm.js',
                        '<%= dirs.src %>/js/core/sm-events.js',
                        '<%= dirs.src %>/js/core/sm-processor.js',
                        '<%= dirs.src %>/js/core/sm-components.js',
                        '<%= dirs.src %>/js/core/sm-form.js'
                    ],
                    '<%= dirs.dist %>/js/frontend.js': ['<%= dirs.src %>/js/merge/frontend/**/*.js', '<%= dirs.src %>/js/merge/frontend.js'],
                    '<%= dirs.dist %>/js/backend.js':  [
                        '<%= dirs.src %>/js/merge/backend/sm-com-manage-entity.js',
                        '<%= dirs.src %>/js/merge/backend/*.js',
                        '<%= dirs.src %>/js/merge/backend.js'
                    ]
                }
            },

            extensions_js: {
                options: {
                    separator: ';'
                },
                files: {

                }
            }
        },

        less: {
            core: {
                options: {
                    relativeUrls: true
                },
                files: [
                    {cwd: '<%= dirs.src %>/less', src: ['*.less'],  dest: '<%= dirs.dist %>/css', ext: '.css', expand: true},
                    {cwd: '<%= dirs.src %>/less/common', src: ['*.less'],  dest: '<%= dirs.dist %>/css/common', ext: '.css', expand: true},
                    {cwd: '<%= dirs.src %>/less/backend', src: ['*.less'],  dest: '<%= dirs.dist %>/css/backend', ext: '.css', expand: true},
                    {cwd: '<%= dirs.src %>/less/frontend', src: ['*.less'],  dest: '<%= dirs.dist %>/css/frontend', ext: '.css', expand: true}
                ]
            },
            extensions: {files: []}
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
                files: ['<%= dirs.src %>/**/*.less', '<%= dirs.src %>/**/*.scss'],
                tasks: ['less:core','cssmin:core']
            },
            core_js: {
                files: ['<%= dirs.src %>/**/*.js'],
                tasks: ['copy:core_js','concat:core_js','uglify:core']
            },
            exts_css: {
                files: ['extensions/**/assets-src/**/*.less', 'extensions/**/assets-src/**/*.scss'],
                tasks: ['less:extensions','cssmin:extensions']
            },
            exts_js: {
                files: ['extensions/**/assets-src/**/*.js'],
                tasks: ['concat:extensions_js','uglify:extensions']
            }
        }
    };

    for (var i=0; i < extensions.length; i++)
    {
        ext = extensions[i];

        config.concat.extensions_js.files['extensions/'+ext+'/assets/js/common.js']   = ['extensions/'+ext+'/assets-src/js/common.js',   'extensions/'+ext+'/assets-src/js/common/**/*.js'];
        config.concat.extensions_js.files['extensions/'+ext+'/assets/js/frontend.js'] = ['extensions/'+ext+'/assets-src/js/frontend.js', 'extensions/'+ext+'/assets-src/js/frontend/**/*.js'];
        config.concat.extensions_js.files['extensions/'+ext+'/assets/js/backend.js']  = ['extensions/'+ext+'/assets-src/js/backend.js',  'extensions/'+ext+'/assets-src/js/backend/**/*.js'];

        config.uglify.extensions.files.push({
            expand: true,
            cwd:  'extensions/'+ext+'/assets/js',
            src:  ['*.js', '!*.min.js'],
            dest: 'extensions/'+ext+'/assets/js',
            ext:  '.min.js'
        });

        config.less.extensions.files.push({
            expand: true,
            cwd:  'extensions/'+ext+'/assets-src/less',
            src:  ['*.less'],
            dest: 'extensions/'+ext+'/assets/css',
            ext:  '.css'
        });

        config.cssmin.extensions.files.push({
            expand: true,
            cwd:  'extensions/'+ext+'/assets/css',
            src:  ['*.css', '!*.min.css'],
            dest: 'extensions/'+ext+'/assets/css',
            ext:  '.min.css'
        });

    }

    grunt.initConfig(config);

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['copy','concat','uglify','less','cssmin']);

};