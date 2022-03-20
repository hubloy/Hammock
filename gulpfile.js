'use strict';

// Project paths
var paths = {
	src: '_src/scss/',
	dist: 'assets/css/'
};

// Load packages
var gulp = require('gulp'),
	{ watch } = require('gulp'),
	sass = require('gulp-sass')(require('sass')),
	postcss = require('gulp-postcss'),
	log = require('fancy-log'),
	concat = require('gulp-concat'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify');


// Styles task
gulp.task('styles', function() {
	return gulp.src(paths.src + '**/**/**/**/*.scss')
		.pipe(sass({outputStyle: 'compressed'})).on('error', function(err) {log(err);}).on("finish", function() {
			log('You\'re awesome! Changes are ready now.');
		})
		.pipe(postcss())
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest(paths.dist));
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
		.pipe(gulp.dest(jsDest));
});

gulp.task('front-scripts', function() {
	return gulp.src(frontJsFiles)
		.pipe(concat('hammock-front.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest(jsDest));
});


// Watch task
gulp.task('watch', function() {
	watch(paths.src + '**/**/**/**/*.scss', gulp.series('styles'));
});

gulp.task('watch-js', gulp.parallel('admin-scripts', function() {
	watch(adminJsFiles, gulp.series('admin-scripts'));
	watch(frontJsFiles, gulp.series('front-scripts'));
}) );

// Register tasks to 'gulp' command
gulp.task('default', gulp.parallel('styles', 'watch', 'admin-scripts', 'front-scripts', 'watch-js'));
gulp.task('scripts', gulp.parallel('admin-scripts', 'front-scripts'));
gulp.task('js', gulp.parallel('admin-scripts', 'watch-js'));
gulp.task('watch-all', gulp.parallel('watch', 'watch-js'));
gulp.task('build', gulp.parallel('admin-scripts', 'front-scripts', 'styles'));