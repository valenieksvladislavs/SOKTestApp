const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const rename = require("gulp-rename");

gulp.task('scss', function (done) {
  return gulp.src(['src/scss/**/*.scss'])
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public/css'))
    .on('end', done);
});

gulp.task('js', function (done) {
  return gulp.src(['src/js/**/*.js'])
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public/js'))
    .on('end', done);
});

gulp.task('copy-assets', function (done) {
  gulp.src(['node_modules/bootstrap/dist/js/bootstrap.min.js', 'node_modules/bootstrap/dist/js/bootstrap.min.js.map'])
    .pipe(gulp.dest('public/js'));
  gulp.src(['node_modules/bootstrap/dist/css/bootstrap.min.css', 'node_modules/bootstrap/dist/css/bootstrap.min.css.map'])
    .pipe(gulp.dest('public/css'));
  gulp.src(['node_modules/jquery/dist/jquery.min.js', 'node_modules/jquery/dist/jquery.min.map'])
    .pipe(gulp.dest('public/js'))
  gulp.src(['node_modules/jquery-validation/dist/jquery.validate.min.js'])
    .pipe(sourcemaps.init())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('public/js'))
  gulp.src('node_modules/tinymce/**')
    .pipe(gulp.dest('public/tinymce'))
    .on('end', done);
});

gulp.task('default', gulp.parallel('scss', 'js', 'copy-assets'));
