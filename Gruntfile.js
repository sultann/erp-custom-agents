module.exports = function(grunt) {
    var pkg = grunt.file.readJSON('package.json');
    var bannerTemplate = '/**\n' +
        ' * <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
        ' * <%= pkg.homepage %>\n' +
        ' *\n' +
        ' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
        ' * Licensed GPLv2+\n' +
        ' */\n';

    var compactBannerTemplate = '/**\n' +
        ' * <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> | <%= pkg.homepage %> | Copyright (c) <%= grunt.template.today("yyyy") %>; | Licensed GPLv2+\n' +
        ' */\n';

    // Project configuration
    grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    concat: {
        options: {
            stripBanners: true,
            banner: bannerTemplate
        },
            erp_custom_agents: {
                src: [
                    'assets/js/src/erp-custom-agents.js'
                ],
                    dest: 'assets/js/erp-custom-agents.js'
            }
        },
       jshint: {
        all: [
            'Gruntfile.js',
            'assets/js/src/**/*.js',
            'assets/js/test/**/*.js'
        ],
            options: {
            curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                unused: true,
                undef: true,
                boss: true,
                eqnull: true,
                globals: {
                exports: true,
                    module: false
            },
            predef: ['document', 'window']
        }
    },

    uglify: {
        all: {
            files: {
                'assets/js/erp-custom-agents.min.js': ['assets/js/erp-custom-agents.js']
            },
            options: {
                banner: compactBannerTemplate,
                    mangle: {
                    except: ['jQuery']
                }
            }
        }
    },
      test: {
        files: ['assets/js/test/**/*.js']
    },

        
        sass:   {
            all: {
                files: {
                    'assets/css/erp-custom-agents.css': 'assets/css/sass/erp-custom-agents.scss'
                }
            }
        },

        
        cssmin: {
            options: {
                banner: bannerTemplate
            },
            minify: {
                expand: true,
                
                cwd: 'assets/css/',
                src: ['erp-custom-agents.css'],
                
                dest: 'assets/css/',
                ext: '.min.css'
            }
        },

        watch:  {
             options: {
                livereload: true,
            },
            
            sass: {
                files: ['assets/css/sass/*.scss'],
                tasks: ['sass', 'cssmin'],
                options: {
                    debounceDelay: 500
                }
            },
            
            scripts: {
                files: ['assets/js/src/**/*.js', 'assets/js/vendor/**/*.js'],
                tasks: ['jshint', 'concat', 'uglify'],
                options: {
                    debounceDelay: 500
                }
            }
        },

        /**
         * check WP Coding standards
         * https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
         */
        phpcs: {
            application: {
                dir: [
                    '**/*.php',
                    '!**/node_modules/**'
                ]
            },
            options: {
                bin: '~/phpcs/scripts/phpcs',
                standard: 'WordPress'
            }
        },
           // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['build/.*', 'node_modules/*', 'assets/*'],
                        domainPath: '/i18n/languages/', // Where to save the POT file.
                        potFilename: 'erp-custom-agents.pot', // Name of the POT file.
                        type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
                        potHeaders: {
                        'report-msgid-bugs-to': 'http://pluginever.com/support/',
                            'language-team': 'LANGUAGE <support@pluginever.com>'
                    }
                }
            }
        },
            // Clean up build directory
        clean: {
            main: ['build/']
        },
          copy: {
        main: {
            src: [
                '**',
                '!node_modules/**',
                '!.codekit-cache/**',
                '!.idea/**',
                '!build/**',
                '!bin/**',
                '!.git/**',
                '!Gruntfile.js',
                '!package.json',
                '!composer.json',
                '!composer.lock',
                '!debug.log',
                '!phpunit.xml',
                '!.gitignore',
                '!.gitmodules',
                '!npm-debug.log',
                '!plugin-deploy.sh',
                '!export.sh',
                '!config.codekit',
                '!nbproject/*',
                '!tests/**',
                '!README.md',
                '!CONTRIBUTING.md',
                '!**/*~',
                '!.csscomb.json',
                '!.editorconfig',
                '!.jshintrc',
                '!.tmp',
                '!assets/src/**',
            ],
                dest: 'build/'
        }
    },
      compress: {
        main: {
            options: {
                mode: 'zip',
                    archive: './build/erp-custom-agents' + pkg.version + '.zip'
            },
            expand: true,
                cwd: 'build/',
                src: ['**/*'],
                dest: 'erp-custom-agents'
        }
    }   



});

// Load other tasks
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks('grunt-contrib-compress');
    
    grunt.loadNpmTasks('grunt-contrib-sass');
    
    grunt.loadNpmTasks('grunt-contrib-watch');
    
    grunt.loadNpmTasks('grunt-phpcs');
    

    // Default task.
    
    grunt.registerTask( 'default', ['jshint', 'concat', 'uglify', 'sass', 'cssmin'] );
    
   

    grunt.registerTask('release', ['makepot', 'zip']);

    grunt.registerTask('zip', ['clean', 'copy', 'compress']);

    grunt.util.linefeed = '\n';
};