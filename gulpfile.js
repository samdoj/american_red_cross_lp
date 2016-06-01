// ///////////////////////////////////////////////
// CONFIG OBJECT
// ///////////////////////////////////////////////

// jsConcatFiles => list of javascript files (in order) to concatenate
// buildFilesFoldersRemove => list of files to remove when running final build

var config = {
	jsConcatFiles: [
		'./app/js/main.js',
		'./app/js/form.js',
		'./app/js/smooth_scroll.js',
		'./app/js/accordion.js'
	],
	// tweenmaxBundle: [
	// 	'./app/js/CSSPlugin.min.js',
	// 	'./app/js/EasePack.min.js',
	// 	'./app/js/TweenLite.min.js',
	// 	'./app/js/TimelineMax.min.js'
	// ],
	buildFilesFoldersRemove:[
		'build/scss/', 
		'build/js/!(*.min.js)',
		'build/bower.json',
		'build/bower_components/',
		'build/maps/'
	]
};


// ///////////////////////////////////////////////
// REQUIRED TASKS
// ///////////////////////////////////////////////

// gulp build
// bulp build:serve

var gulp 			= require('gulp'),
	sass 			= require('gulp-sass'),
	sourcemaps 		= require('gulp-sourcemaps'),
	autoprefixer 	= require('gulp-autoprefixer'),
	browserSync 	= require('browser-sync'),
	reload 			= browserSync.reload,
	concat 			= require('gulp-concat'),
	uglify 			= require('gulp-uglify'),
	rename 			= require('gulp-rename'),
	del 			= require('del'),
	imagemin 		= require('gulp-imagemin'),
	php 			= require('gulp-connect-php')
;


// ///////////////////////////////////////////////
// LOG ERRORS
// ///////////////////////////////////////////////

function errorlog(err){
	console.log(err.message);
	this.emit('end');
}


// ///////////////////////////////////////////////
// SCRIPT TASKS
// ///////////////////////////////////////////////

gulp.task('scripts', function() {
  return gulp.src(config.jsConcatFiles)
	.pipe(sourcemaps.init())
		.pipe(concat('temp.js'))
		.pipe(uglify())
		.on('error', errorlog)
		.pipe(rename('app.min.js'))		
    .pipe(sourcemaps.write('../maps'))
    .pipe(gulp.dest('./app/js/'))
    .pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// STYLE TASKS
// ///////////////////////////////////////////////

gulp.task('styles', function() {
	gulp.src('./app/scss/style.scss')
		.pipe(sourcemaps.init())
			.pipe(sass({outputStyle: 'expanded'}))
			.on('error', errorlog)
			.pipe(autoprefixer({
	            browsers: ['last 3 versions'],
	            cascade: false
	        }))	
		.pipe(sourcemaps.write('../maps'))
		.pipe(gulp.dest('./app/css'))
		.pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// HTML TASKS
// ///////////////////////////////////////////////

gulp.task('html', function(){
    gulp.src('./app/**/*.html')
    .pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// PHP TASKS
// ///////////////////////////////////////////////

gulp.task('php-server', function() {
    php.server({ base: './app/', port: 8010, keepalive: true});
});

gulp.task('php', function() {
	gulp.src('./app/**/*.php')
	.pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// INCLUDE FILE TASKS
// ///////////////////////////////////////////////

gulp.task('inc', function(){
    gulp.src('./app/**/*.inc')
    .pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// BROWSER-SYNC TASKS
// ///////////////////////////////////////////////

gulp.task('browser-sync',['php-server'], function() {
    browserSync({
        proxy: '127.0.0.1:8010',
        port: 8888,
        open: true,
        notify: false
    });
});


// ///////////////////////////////////////////////
// Run build server for testing final app
// ///////////////////////////////////////////////

gulp.task('build:serve',['php-server']);


// ///////////////////////////////////////////////
// IMAGE COMPRESSION TASKS
// ///////////////////////////////////////////////

gulp.task('image-min', function() {
	gulp.src('/app/media/img/*')
		.pipe(imagemin({
			progressive: true,
			optimizationLevel: 4
		}))	
		.on('error', errorlog)
		.pipe(gulp.dest('./app/media/img'))
		.pipe(reload({stream:true}));
});


// ///////////////////////////////////////////////
// BUILD TASKS
// ///////////////////////////////////////////////


// Clean out all files and folders from build folder

gulp.task('build:cleanfolder', function (cb) {
	del([
		'build/**'
	], cb);
});

// Create build directory of all files

gulp.task('build:copy', ['build:cleanfolder'], function(){
    return gulp.src('app/**/*/')
    .pipe(gulp.dest('build/'));
});

// Remove unwanted build files
// List all unwanted files

gulp.task('build:remove', ['build:copy'], function (cb) {
	del(config.buildFilesFoldersRemove, cb);
});

gulp.task('build', ['image-min', 'build:copy', 'build:remove']);


// ///////////////////////////////////////////////
// WATCH TASKS
// ///////////////////////////////////////////////

gulp.task ('watch', function(){
	gulp.watch('./app/scss/**/*.scss', ['styles']);
	gulp.watch('./app/js/**/*.js', ['scripts']);
  	gulp.watch('./app/**/*.html', ['html']);
  	gulp.watch('./app/**/*.php', ['php']);
  	gulp.watch('./app/**/*.inc', ['inc']);
});

gulp.task('default', ['scripts', 'styles', 'html', 'php', 'inc', 'browser-sync', 'watch']);