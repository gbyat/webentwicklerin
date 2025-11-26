const gulp = require('gulp');
const clean = require('gulp-clean');
const cleanCSS = require('gulp-clean-css');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cssnano = require('gulp-cssnano');
const rename = require('gulp-rename');
const concat = require('gulp-concat');
const wpPot = require('gulp-wp-pot');
const log = require('fancy-log');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const babel = require('gulp-babel');

const web_path = "C:\\inetpub\\wwwroot\\wp_webentwicklerin\\wp-content\\themes\\";
const thisname = 'webentwicklerin';
const themedir = web_path + thisname;
const storage = "/home/www/wp-webentwicklerin/wp-content/themes/" + thisname;

var globs = [
    './assets/**/*',
    './inc/**/*',
    './parts/**/*',
    './patterns/**/*',
    './templates/**/*',
    './languages/**/*',
    './styles/**/*',
    './blocks/**/*',
    'functions.php',
    'screenshot.png',
    'theme.json',
    'style.css'
];

// Entferne versehentlich erzeugte Einzel-CSS aus assets/css (nur style/editor-style bleiben)
gulp.task('clean-css-chunks', function () {
    return gulp.src([
        './assets/css/00-*.css',
        './assets/css/01-*.css',
        './assets/css/02-*.css',
        './assets/css/03-*.css',
        './assets/css/04-*.css',
        './assets/css/05-*.css',
        './assets/css/10-*.css'
    ], { allowEmpty: true })
        .pipe(clean({ read: false, force: true }));
});


gulp.task('potfile', function () {
    var translatePath = './languages/';
    return gulp.src('./**/*.php')
        .pipe(wpPot({ domain: thisname, package: thisname }))
        .pipe(gulp.dest(translatePath + '/' + thisname + '.pot'));
});

gulp.task('sass', function () {
    return gulp.src('./scss/style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(rename('style.css'))
        .pipe(gulp.dest('./assets/css'))
        .pipe(cssnano({ zindex: false }))
        .pipe(rename({ basename: 'style', suffix: '.min' }))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('sass-editor', function () {
    return gulp.src('./scss-editor/editor-style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(rename('editor-style.css'))
        .pipe(gulp.dest('./assets/css'))
        .pipe(cssnano({ zindex: false }))
        .pipe(rename({ basename: 'editor-style', suffix: '.min' }))
        .pipe(gulp.dest('./assets/css'));
});


gulp.task('minify-js', function () {
    return gulp.src('./assets/js/theme-scripts.js')
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('./assets/js'));
});

gulp.task('watch', function () {
    gulp.watch('./scss/**/*.scss', gulp.series('sass'));
    gulp.watch('./scss-editor/editor-style.scss', gulp.series('sass-editor'));
});

gulp.task('build', gulp.series(
    'clean-css-chunks',
    'sass',
    'sass-editor',
    'minify-js',
    'potfile'
));

gulp.task('default', gulp.series('build', 'watch'));
