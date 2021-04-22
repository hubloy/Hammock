/*global require*/

/**
 * When grunt command does not execute try these steps:
 *
 * - delete folder 'node_modules' and run command in console:
 *   $ npm install
 *
 * - Run test-command in console, to find syntax errors in script:
 *   $ grunt hello
 */

module.exports = function( grunt ) {
	// Show elapsed time at the end.
	require( 'time-grunt' )(grunt);

	// Load all grunt tasks.
	require( 'load-grunt-tasks' )(grunt);

	var buildtime = new Date().toISOString();

	var conf = {
		//Js folders
		js_folder: 'assets/js/',
		src_js_folder: '_src/js/',

		// Folder that contains the CSS files.
		css_folder: 'assets/css/',
		src_css_folder: '_src/scss/',

		// Concatenate those JS files into a single file (target: [source, source, ...]).
		js_files_concat: {
			'{js}/hammock-admin.js': [
				'_src/js/admin/_helpers.js',
				'_src/js/admin/_base.js',
				'_src/js/admin/_membership.js',
				'_src/js/admin/_addons.js'
			],
			'{js}/hammock-front.js': [
				'_src/js/front/_helpers.js',
				'_src/js/front/_base.js',
			]
		},

		// SASS files to process. Resulting CSS files will be minified as well.
		css_files_compile: {
			'{css}/hammock-admin.css':   '_src/scss/hammock-admin.scss',
			'{css}/hammock-front.css':   '_src/scss/hammock-front.scss'
		},

		// BUILD branches.
		plugin_branches: {
			include_files: [
				'**',
				'!**/_src/**',
				'!**/_src/sass/**',
				'!**/_src/js/**',
				'!**/_src/react/**',
				'!**/img/src/**',
				'!**/node_modules/**',
				'!**/tests/**',
				'!**/release/*.zip',
				'!release/*.zip',
				'!**/release/**',
				'!**/Gruntfile.js',
				'!**/package.json',
				'!**/package-lock.json',
				'!**/build/**',
				'!_src/**',
				'!node_modules/**',
				'!.sass-cache/**',
				'!release/**',
				'!Gruntfile.js',
				'!package.json',
				'!package-lock.json',
				'!build/**',
				'!tests/**',
				'!.git/**',
				'!.git',
				'!**/.svn/**',
				'!.log',
				'!docs/phpdoc-**',
				'!vendor/**',
				'!webpack.config.js',
				'!**/webpack.config.js'
			]
		},

		// Regex patterns to exclude from transation.
		translation: {
			ignore_files: [
				'node_modules/.*',
				'(^.php)',      // Ignore non-php files.
				'lib/.*',       // External libraries.
				'release/.*',   // Temp release files.
				'tests/.*',     // Unit testing.
				'docs/.*',      // API Documentation.
			],
			pot_dir: 'languages/', // With trailing slash.
			textdomain: 'hammock',
		},

		plugin_dir: 'hammock/',
		plugin_file: 'hammock.php',
	};
	// -------------------------------------------------------------------------
	var key, ind, newkey, newval;
	for ( key in conf.js_files_concat ) {
		newkey = key.replace( '{js}', conf.js_folder );
		newval = conf.js_files_concat[key];
		delete conf.js_files_concat[key];
		for ( ind in newval ) { newval[ind] = newval[ind].replace( '{js}', conf.js_folder ); }
		conf.js_files_concat[newkey] = newval;
	}
	for ( key in conf.css_files_compile ) {
		newkey = key.replace( '{css}', conf.css_folder );
		newval = conf.css_files_compile[key].replace( '{css}', conf.css_folder );
		delete conf.css_files_compile[key];
		conf.css_files_compile[newkey] = newval;
	}
	// -------------------------------------------------------------------------

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),

		// JS - Concat .js source files into a single .js file.
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n'
			},
			scripts: {
				files: conf.js_files_concat
			}
		},

		// JS - Validate .js source code.
		jshint: {
			all: [
				'Gruntfile.js',
				conf.src_js_folder + '/**/*.js',
			],
			options: {
				curly:   true,
				eqeqeq:  true,
				immed:   true,
				latedef: true,
				newcap:  true,
				noarg:   true,
				sub:     true,
				undef:   true,
				boss:    true,
				eqnull:  true,
				globals: {
					exports: true,
					module:  false
				},
				esversion: 6
			}
		},

		// JS - Uglyfies the source code of .js files (to make files smaller).
		uglify: {
			all: {
				files: [{
					expand: true,
					src: ['*.js', '!*.min.js'],
					cwd: conf.js_folder,
					dest: conf.js_folder,
					ext: '.min.js',
					extDot: 'last'
				}],
				options: {
					banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n',
					mangle: {
						reserved: ['jQuery']
					}
				}
			}
		},

		// TEST - Run the PHPUnit tests.
		phpunit: {
			classes: {
				dir: ''
			},
			options: {
				bin: 'phpunit',
				bootstrap: 'tests/php/bootstrap.php',
				testsuite: 'default',
				configuration: 'tests/php/phpunit.xml',
				colors: true,
				//tap: true,
				//testdox: true,
				//stopOnError: true,
				staticBackup: false,
				noGlobalsBackup: false
			}
		},

		// CSS - Compile a .scss file into a normal .css file.
		sass:   {
			all: {
				options: {
					'sourcemap=none': true, // 'sourcemap': 'none' does not work...
					unixNewlines: true,
					style: 'expanded'
				},
				files: conf.css_files_compile
			}
		},

		// CSS - Automaticaly create prefixed attributes in css file if needed.
		//       e.g. add `-webkit-border-radius` if `border-radius` is used.
		autoprefixer: {
			options: {
				browsers: ['last 2 version', 'ie 8', 'ie 9'],
				diff: false
			},
			single_file: {
				files: [{
					expand: true,
					src: ['*.css', '!*.min.css'],
					cwd: conf.css_folder,
					dest: conf.css_folder,
					ext: '.css',
					extDot: 'last'
				}]
			}
		},


		// CSS - Required for CSS-autoprefixer and maybe some SCSS function.
		compass: {
			options: {
			},
			server: {
				options: {
					debugInfo: true
				}
			}
		},

		// CSS - Minify all .css files.
		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n'
			},
			minify: {
				expand: true,
				src: ['*.css', '!*.min.css'],
				cwd: conf.css_folder,
				dest: conf.css_folder,
				ext: '.min.css',
				extDot: 'last'
			}
		},

		// WATCH - Watch filesystem for changes during development.
		watch:  {
			sass: {
				files: [
					conf.src_css_folder + '**/*.scss'
				],
				tasks: ['sass', 'autoprefixer','cssmin'],
				options: {
					debounceDelay: 500
				}
			},

			scripts: {
				files: [
					conf.src_js_folder + '**/*.js',
				],
				tasks: ['jshint', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		},

		// BUILD - Remove previous build version and temp files.
		clean: {
			temp: {
				src: [
					'**/*.tmp',
					'**/.afpDeleted*',
					'**/.DS_Store'
				],
				dot: true,
				filter: 'isFile'
			}
		},

		// BUILD - Copy all plugin files to the release subdirectory.
		copy: {
            files: {
                src: conf.plugin_branches.include_files,
                dest: 'release/<%= pkg.name %>-<%= pkg.version %>/'
            }
		},

		// BUILD - Create a zip-version of the plugin.
		compress: {
            files: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.name %>-<%= pkg.version %>/',
                src: [ '**/*' ],
                dest: conf.plugin_dir
            }
		},

		// BUILD - update the translation index .po file.
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: conf.translation.pot_dir,
					exclude: conf.translation.ignore_files,
					mainFile: conf.plugin_file,
					potFilename: conf.translation.textdomain + '.pot',
					potHeaders: {
						poedit: true, // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					type: 'wp-plugin' // wp-plugin or wp-theme
				}
			}
		},

		// DOCS - Execute custom command to build the phpdocs for API
		exec: {
			phpdoc: {
				command: 'phpdoc -d ./app -t ./docs'
			}
		}

	} );

	// Test task.
	grunt.registerTask( 'hello', 'Test if grunt is working', function() {
		grunt.log.subhead( 'Hi there :)' );
		grunt.log.writeln( 'Looks like grunt is installed!' );
	});

	// Plugin build tasks
	grunt.registerTask( 'build', 'Run all tasks.', function(target) {


		// First run unit tests.
		//grunt.task.run( 'phpunit' );

		// Run the default tasks (js/css/php validation).
		grunt.task.run( 'default' );

		// Generate all translation files (same for pro and free).
		grunt.task.run( 'makepot' );

		// Update the integrated API documentation.
		//grunt.task.run( 'docs' );

		grunt.task.run( 'clean' );
		grunt.task.run( 'copy' );
		grunt.task.run( 'compress' );
		
	});

	// Development tasks.
	grunt.registerTask( 'default', ['clean:temp', 'jshint', 'concat', 'uglify', 'sass', 'autoprefixer', 'cssmin'] );
	grunt.registerTask( 'test', ['phpunit', 'jshint'] );
	grunt.registerTask( 'docs', ['exec:phpdoc'] );

	grunt.task.run( 'clear' );
	grunt.util.linefeed = '\n';
};