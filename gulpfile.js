const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');

// Compile SCSS files to CSS
gulp.task('styles', function() {
  return gulp.src('src/scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('dist/css'));
});

// Minify and concatenate JS files
gulp.task('scripts', function() {
  return gulp.src('src/js/**/*.js')
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(gulp.dest('dist/js'));
});

// Watch for changes in SCSS and JS files
gulp.task('watch', function() {
  gulp.watch('src/scss/**/*.scss', gulp.series('styles'));
  gulp.watch('src/js/**/*.js', gulp.series('scripts'));
});

// Default task
gulp.task('default', gulp.series('styles', 'scripts', 'watch'));