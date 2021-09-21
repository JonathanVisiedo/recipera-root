const gulp = require('gulp');
const del = require('del');
const sourcemaps = require('gulp-sourcemaps');

const sass = require('gulp-sass');

const babel = require('gulp-babel');
const concat = require('gulp-concat');
const minify = require("gulp-babel-minify");


const clean = () => {
    return del([
        './public/assets/css/master.min.css',
        './public/assets/js/master.min.js'
    ]);
}

const stylesheet = (done) => {
    gulp.src('./Assets/sass/master.scss')
    .pipe(sourcemaps.init())
    .pipe(concat('master.min.css'))
    .pipe(sass().on('error', sass.logError))
    .pipe(sass.sync({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./public/assets/css/'))
    return done();
}

const javascript = (done) => {
    gulp.src('./Assets/js/**/*.js')
    .pipe(sourcemaps.init())
    .pipe(babel({
        presets: ['@babel/preset-env']
    }))
    .pipe(minify({
        mangle: {
          keepClassName: true
        }
      }))
    .pipe(concat('master.min.js'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./public/assets/js/'))
    return done();
}

// watcher
const watchSCSS = () => gulp.watch('./Assets/sass/**/*.scss', gulp.series(['stylesheet']))
const watchJS = () => gulp.watch('./Assets/js/**/*.js', gulp.series(['javascript']))


// build 
exports.clean = clean
exports.stylesheet = stylesheet
exports.javascript = javascript
exports.watchSCSS = watchSCSS
exports.watchJS = watchJS
exports.watcher = gulp.parallel([watchJS, watchSCSS])
exports.default = gulp.series([clean, stylesheet, javascript])