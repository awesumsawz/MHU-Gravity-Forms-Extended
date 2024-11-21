const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const jsonlint = require('gulp-jsonlint');
const jsonmin = require('gulp-jsonmin');

// Compile SCSS files to CSS
gulp.task('styles', function () {
  return gulp.src('src/scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('dist/css'));
});

// Minify and concatenate JS files
gulp.task('scripts', function () {
  return gulp.src('src/js/**/*.js')
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(gulp.dest('dist/js'));
});

// Validate and minify JSON files
gulp.task('json', function () {
  return gulp.src('src/json/**/*.json')
    .pipe(jsonlint())
    .pipe(jsonlint.reporter())
    .pipe(jsonmin())
    .pipe(gulp.dest('dist/json'));
});

// Watch for changes in SCSS, JS, and JSON files
gulp.task('watch', function () {
  gulp.watch('src/scss/**/*.scss', gulp.series('styles'));
  gulp.watch('src/js/**/*.js', gulp.series('scripts'));
  gulp.watch('src/json/**/*.json', gulp.series('json'));
});

// Default task
gulp.task('default', gulp.series('styles', 'scripts', 'json', 'watch'));