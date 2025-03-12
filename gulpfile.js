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
const ftp = require('vinyl-ftp');

const web_path = "C:\\inetpub\\wwwroot\\wp_webentwicklerin\\wp-content\\themes\\";
const thisname = 'webentwicklerin';
const themedir = web_path + thisname;
const storage = "/wp-content/themes/" + thisname;

var globs = [
    './assets/**/*',
    './inc/**/*',
    './parts/**/*',
    './patterns/**/*',
    './templates/**/*',
    './languages/**/*',
    './styles/**/*',
    'functions.php',
    'screenshot.png',
    'theme.json',
    'style.css'
];


gulp.task('potfile', function () {
    var translatePath = './languages/';
    return gulp.src('./**/*.php')
        .pipe(wpPot({ domain: thisname, package: thisname }))
        .pipe(gulp.dest(translatePath + '/' + thisname + '.pot'));
});

gulp.task('watch', function () {
    gulp.watch(['./scss/**/*.scss']).on(
        'change',
        gulp.series(
            'mergecss',
            'mergeeditorcss',
            'css_minify'
        )
    );
});

gulp.task('mergecss', function () {
    var srce = "./scss/**/*.scss";
    var dst = "./assets/css/";
    return gulp.src(srce)
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('style.css'))
        .pipe(autoprefixer())
        .pipe(gulp.dest(dst));
});


gulp.task('mergeeditorcss', function () {
    var srce = "./scss-editor/**/*.scss";
    var dst = "./assets/css/";
    return gulp.src(srce)
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('editor-style.css'))
        .pipe(autoprefixer())
        .pipe(gulp.dest(dst));
});


gulp.task('css_minify', function () {
    return gulp.src(['./assets/css/style.css', './assets/css/editor-style.css'])
        .pipe(rename({ suffix: ".min" }))
        .pipe(cssnano({ zindex: false }))
        .pipe(gulp.dest('./assets/css'));
});

function create_ftp_conn() {
    return ftp.create({
        host: 's137.goserver.host',
        user: 'web73f5',
        password: 'NoYunFDx3#MOTGj9d',
        parallel: 2,
        secure: true,
        log: log,
    })
}

gulp.task('upload', function () {
    const conn = create_ftp_conn();
    return gulp.src(globs, { base: '.', buffer: false })
        .pipe(conn.newer(storage))
        .pipe(conn.dest(storage));
});


/**
 * 	
    .pipe(conn.clean( storage+'/**', '.', {base: storage}))
    .pipe(conn.clean('/www/htdocs/w0156d01/dev.webentwicklerin.com/wp-content/themes/webentwicklerin/**', '.', {base: '/www/htdocs/w0156d01/dev.webentwicklerin.com/wp-content/themes/webentwicklerin'}));
  
 */


gulp.task('watch', function () {
    gulp.watch(['assets/css/**/*.css']).on(
        'change',
        gulp.series(
            'clean-shared',
            'clean-blocks',
            'minify-blocks'
        )
    );
});


gulp.task('clean-blocks', function () {
    return gulp.src('assets/css/blocks/*.min.css', {
        read: false,
        allowEmpty: true,
    })
        .pipe(clean());
});


gulp.task('minify-blocks', function () {
    return gulp.src('assets/css/blocks/*.css')
        .pipe(cssnano({ zindex: false }))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/blocks'));
});


gulp.task('deploy-locl', function () {
    return gulp.src(globs, { base: ".", buffer: false })
        .pipe(gulp.dest(themedir));
});

exports.default =
    gulp.series(
        'mergecss',
        'mergeeditorcss',
        'potfile',
        'css_minify',
        'clean-blocks',
        'minify-blocks',
        'deploy-locl'
    );

exports.main =
    gulp.series(
        'upload'
    );
// exports.svg = series(buildSvg);
//exports.fonts = series(buildwebfonts);

