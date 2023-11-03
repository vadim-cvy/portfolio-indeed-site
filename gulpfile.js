const gulp = require('gulp');
const sass = require('gulp-sass');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const path = require('path');

const srcDir = 'assets/src/scss';
const distDir = 'assets/dist/css';

gulp.task('compile-scss', function () {
  return gulp
    .src(path.join(srcDir, '*/index.scss'))
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(distDir));
});

gulp.task('default', gulp.series('compile-scss'));

gulp.task('watch', function () {
  gulp.watch(path.join(srcDir, '**/*.scss'), gulp.series('compile-scss'));
});