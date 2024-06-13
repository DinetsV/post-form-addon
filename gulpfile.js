const gulp = require('gulp')
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cleanCss = require('gulp-clean-css');

gulp.task('sass', () => {
    return gulp
        .src('./assets/src/sass/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('app.min.css'))
        .pipe(cleanCss())
        .pipe(gulp.dest('./assets/build/css'))
})

gulp.task('js', function () {
    return gulp
        .src('./assets/src/js/*.js')
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/build/js'));
});

gulp.task('watch', () => {
    gulp.watch('./assets/src/sass/*.scss', gulp.series(['sass']));
    gulp.watch('./assets/src/js/*.js', gulp.series(['js']));
});

gulp.task('build', gulp.parallel('sass', 'js'));

