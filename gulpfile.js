const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cssnano = require('gulp-cssnano');
const rename = require('gulp-rename');
const wpPot = require('gulp-wp-pot');

const themeName = 'webentwicklerin';

// Try to load local deploy configuration (if exists)
let hasLocalDeploy = false;
try {
    require('./gulpfile.local.js');
    hasLocalDeploy = true;
} catch (e) {
    // Local deploy file doesn't exist, that's okay
}

gulp.task('potfile', function () {
    return gulp.src('./**/*.php')
        .pipe(wpPot({ domain: themeName, package: themeName }))
        .pipe(gulp.dest(`./languages/${themeName}.pot`));
});

/**
 * Compile Sass to CSS with minified version
 * @param {string} src - Source SCSS file
 * @param {string} outputName - Output CSS filename (without extension)
 * @returns {Stream} Gulp stream
 */

function compileSass(src, outputName) {
    return gulp.src(src)
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(rename(`${outputName}.css`))
        .pipe(gulp.dest('./assets/css'))
        .pipe(cssnano({ zindex: false }))
        .pipe(rename({ basename: outputName, suffix: '.min' }))
        .pipe(gulp.dest('./assets/css'));
}

gulp.task('sass', function () {
    return compileSass('./scss/style.scss', 'style');
});

gulp.task('sass-editor', function () {
    return compileSass('./scss-editor/editor-style.scss', 'editor-style');
});

gulp.task('watch', function () {
    gulp.watch('./scss/**/*.scss', gulp.series('sass'));
    gulp.watch('./scss-editor/editor-style.scss', gulp.series('sass-editor'));
});

gulp.task('build', gulp.series(
    'sass',
    'sass-editor',
    'potfile'
));

// Deploy task is defined in gulpfile.local.js (if it exists)
// This allows local deployment without committing system-specific paths

gulp.task('default', function (done) {
    const tasks = ['build'];
    if (hasLocalDeploy) {
        tasks.push('deploy');
    }
    tasks.push('watch');
    return gulp.series(...tasks)(done);
});
