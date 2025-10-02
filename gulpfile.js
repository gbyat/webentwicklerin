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

// SVG to PHP Task - Generate icons.php from SVG files
gulp.task('generate-icons', function (done) {
    const svgDir = './src/svg/';
    const outputFile = './inc/icons.php';

    if (!fs.existsSync(svgDir)) {
        log.error('SVG directory not found:', svgDir);
        done();
        return;
    }

    const files = fs.readdirSync(svgDir).filter(file => file.endsWith('.svg'));

    if (files.length === 0) {
        log.error('No SVG files found in:', svgDir);
        done();
        return;
    }

    let phpContent = `<?php
/**
 * Generated SVG Icons
 * 
 * This file is auto-generated from src/svg/*.svg files
 * DO NOT EDIT MANUALLY - run 'gulp generate-icons' to rebuild
 * 
 * @package webentwicklerin
 * @since 2.0.0
 */

if (!function_exists('webethm_get_icons')) {
    function webethm_get_icons() {
        return [
`;

    files.forEach(file => {
        const iconName = path.basename(file, '.svg');
        let svgContent = fs.readFileSync(path.join(svgDir, file), 'utf8');

        // Clean SVG
        svgContent = svgContent
            .replace(/<\?xml.*?\?>/g, '')
            .replace(/<!DOCTYPE[^>]*>/g, '')
            .replace(/<title[^>]*>[\s\S]*?<\/title[^>]*>/gi, '')
            .replace(/<!--[\s\S]*?-->/g, '')
            .replace(/<desc[^>]*>[\s\S]*?<\/desc[^>]*>/gi, '')
            .replace(/\s+/g, ' ')
            .trim();

        // Escape for PHP
        svgContent = svgContent.replace(/'/g, "\\'");

        phpContent += `            '${iconName}' => '${svgContent}',\n`;
    });

    phpContent += `        ];
    }
}

/* Make icons available in JavaScript for the block editor */

if (!function_exists('webethm_enqueue_icons_script')) {
    function webethm_enqueue_icons_script() {
        $icons = webethm_get_icons();
        wp_add_inline_script(
            'wp-blocks',
            'window.webentwicklerinIcons = ' . wp_json_encode($icons) . ';',
            'before'
        );
    }
    add_action('enqueue_block_editor_assets', 'webethm_enqueue_icons_script');
}
`;

    fs.writeFileSync(outputFile, phpContent);
    log.info('Generated icons.php with', files.length, 'icons');
    done();
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

gulp.task('build-blocks-js', function () {
    return gulp.src('./src/blocks/icon/index.js')
        .pipe(babel({
            presets: [
                ['@wordpress/babel-preset-default', {
                    wordpress: true
                }]
            ]
        }))
        .pipe(gulp.dest('./blocks/icon'));
});

gulp.task('build-blocks-assets', function () {
    return gulp.src([
        './src/blocks/icon/block.json',
        './src/blocks/icon/editor.css',
        './src/blocks/icon/style.css',
        './src/blocks/icon/render.php'
    ])
        .pipe(gulp.dest('./blocks/icon'));
});

gulp.task('build-blocks', gulp.series('build-blocks-js', 'build-blocks-assets'));

gulp.task('watch', function () {
    gulp.watch('./scss/**/*.scss', gulp.series('sass'));
    gulp.watch('./scss-editor/editor-style.scss', gulp.series('sass-editor'));
    gulp.watch('./src/svg/**/*.svg', gulp.series('generate-icons'));
    gulp.watch('./src/blocks/**/*', gulp.series('build-blocks'));
});

gulp.task('build', gulp.series(
    'generate-icons',
    'sass',
    'sass-editor',
    'minify-js',
    'build-blocks',
    'potfile'
));

gulp.task('default', gulp.series('build', 'watch'));
