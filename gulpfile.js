const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const path = require('path');

const srcDir = './assets/src/scss';
const distDir = './assets/dist/css';

gulp.task('scss', () => {
  return gulp
    .src(path.join(srcDir, '/*/index.scss'))
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(rename(path =>
    {
      path.dirname = path.dirname.split( '\\' ).reverse()[0]

      path.extname = '.min' + path.extname
    }))
    .pipe(gulp.dest(distDir));
});

gulp.task('watch', () => gulp.watch(
  srcDir + '/**/*.scss',
  done => gulp.series(['scss'])(done)
))