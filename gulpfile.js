var gulp = require('gulp');
var sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var sourcemaps = require('gulp-sourcemaps');
var browserSync = require('browser-sync').create();

gulp.task('sass', function(){
  return gulp.src('sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}).on('error', function(err) {
            console.error(err.message);
            browserSync.notify(err.message, 3000); // Display error in the browser
            this.emit('end'); // Prevent gulp from catching the error and exiting the watch process
        }))
    .pipe(autoprefixer('last 2 versions'))
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest('css'))
    .pipe(browserSync.stream());
});

gulp.task('compress', function () {
  gulp
    .src("scripts/js/*.js")
    .pipe(sourcemaps.init())
    .pipe(uglify().on("error", function(err) {
        console.error(err.message);
        browserSync.notify(err.message, 3000); // Display error in the browser
        this.emit("end"); // Prevent gulp from catching the error and exiting the watch process
      }))
    .pipe(sourcemaps.write("./maps"))
    .pipe(rename({ suffix: "-min" }))
    .pipe(gulp.dest("scripts/minified/"));
});


gulp.task('browserSync', function() {
    browserSync.init({
        proxy: 'katephoenix'
    });
});

gulp.task('watch',['browserSync', 'sass', 'compress'], function(){
    gulp.watch('sass/*.scss', ['sass']);
    gulp.watch('scripts/js/*.js', ['compress']);
    gulp.watch('perch/templates/**/*.*', browserSync.reload);
    gulp.watch('*.php', browserSync.reload);
    gulp.watch('*.html', browserSync.reload);
    gulp.watch('scripts/minified/*.js', browserSync.reload);
})