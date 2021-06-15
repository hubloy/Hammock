'use strict';

// Project paths
var paths = {
	src: '_src/scss/',
	dist: 'assets/css/'
};

// Load packages
var gulp = require('gulp'),
	{ watch } = require('gulp'),
	sass = require('gulp-sass'),
	postcss = require('gulp-postcss'),
	notify = require('gulp-notify'),
	concat = require('gulp-concat'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify');


// Styles task
gulp.task('styles', function() {
	return gulp.src(paths.src + '**/**/**/**/*.scss')
		.pipe(sass({outputStyle: 'compressed'})).on('error', function(err) {notify().write(err);})
		.pipe(postcss())
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest(paths.dist))
		.pipe(notify({message: 'You\'re awesome! Changes are ready now.', onLast: true}));
});

var adminJsFiles = [
		'_src/js/admin/*.js'
	],
	frontJsFiles = [
		'_src/js/front/*.js'
	],
	jsDest = 'assets/js';

gulp.task('admin-scripts', function() {
	return gulp.src(adminJsFiles)
		.pipe(concat('hammock-admin.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(jsDest))
		.pipe(notify({message: 'Admin js is ready', onLast: true}));
});

gulp.task('front-scripts', function() {
	return gulp.src(frontJsFiles)
		.pipe(concat('hammock-front.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(jsDest))
		.pipe(notify({message: 'Front js is ready', onLast: true}));
});


// Watch task
gulp.task('watch', function() {
	watch(paths.src + '**/**/**/**/*.scss', gulp.series('styles'));
});

gulp.task('watch-js', gulp.parallel('admin-scripts', function() {
	watch(jsFiles, gulp.series('admin-scripts'));
}) );

// Register tasks to 'gulp' command
gulp.task('default', gulp.parallel('styles', 'watch', 'admin-scripts', 'front-scripts', 'watch-js'));
gulp.task('js', gulp.parallel('admin-scripts', 'watch-js'));
gulp.task('build', gulp.parallel('admin-scripts', 'front-scripts', 'styles'));