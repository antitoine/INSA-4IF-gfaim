var gulp = require('gulp');
var less = require('gulp-less');
var minifyCSS = require('gulp-minify-css');
var rename = require('gulp-rename');
var plumber = require('gulp-plumber'); // for debug
var plugins = require('gulp-load-plugins')(); // import require gulp plugins dependencies
var bowerFiles = require('main-bower-files'); //load all bower components
var browserSync = require('browser-sync');

var distDir = './dist/';
var paths = {
  less: ['./src/assets/less/main.less'],
  lessFiles: './src/assets/less/**.less',
  html: ['./src/**/*.html'],
  img: ['./src/assets/img/**/*'],
  scripts: ['./src/app/app.config.js', './src/app/app.service.js', './src/app/app.module.js', './src/app/shared/services/*.js', './src/app/*.*.js', './src/app/**/*.js'],
  distProd: './dist',
  distScriptsProd: './dist/scripts'
};

// JAVASCRIPT pipes inspired from http://paislee.io/a-healthy-gulp-setup-for-angularjs-projects/
var pipes = {};

pipes.orderedVendorScripts = function () {
  return plugins.order(['jquery.js', 'angular.js']);
};

pipes.orderedAppScripts = function () {
  return gulp.src(paths.scripts);
};

pipes.minifiedFileName = function () {
  return plugins.rename(function (path) {
    path.extname = '.min' + path.extname;
  });
};

pipes.builtAppScriptsProd = function () {
  return pipes.orderedAppScripts()
    //.pipe(plugins.sourcemaps.init())
    .pipe(plugins.concat('app.min.js'))
    //.pipe(plugins.uglify())
    //.pipe(plugins.sourcemaps.write())
    .pipe(gulp.dest(paths.distScriptsProd));
};

pipes.builtAppScriptsDev = function () {
  return pipes.orderedAppScripts()
    .pipe(plugins.concat('app.min.js'))
    .pipe(gulp.dest(paths.distScriptsProd));
};

pipes.compileLess = function () {
  return gulp.src(paths.less)
    .pipe(plumber())
    .pipe(less()) // Compile LESS
    .pipe(gulp.dest(distDir + 'css/'))
    .pipe(minifyCSS()) // Minify CSS
    .pipe(rename({extname: '.min.css'}))
    .pipe(gulp.dest(distDir + 'css/'))
    .pipe(browserSync.stream());
};

pipes.builtVendorScriptsProd = function () {
  return gulp.src(bowerFiles(['**/*.js'],
    {
      paths: {
        bowerDirectory: 'src/assets/libs/bower_components',
        bowerrc: './.bowerrc',
        bowerJson: './bower.json'
      }
    }
  )).pipe(pipes.orderedVendorScripts())
    .pipe(plugins.concat('vendor.min.js'))
    //.pipe(plugins.uglify())
    .pipe(gulp.dest(paths.distScriptsProd));
};

pipes.builtJsVendors = function () {
  return pipes.builtVendorScriptsProd();
};

// ---- TASKS ---
gulp.task('less', pipes.compileLess);

gulp.task('move-html', function () {
  gulp.src(paths.html, {base: './src/'})
    .pipe(plugins.htmlmin({collapseWhitespace: true}))
    .pipe(gulp.dest(distDir))
});

gulp.task('moveImg', function () {
  gulp.src(paths.img)
    .pipe(gulp.dest(distDir + 'assets/img/'))
});

gulp.task('build-js-vendors', pipes.builtJsVendors);

gulp.task('build-js-app', function () {
  pipes.builtAppScriptsDev();
  // this line does noting but browser-sync wait for a stream
  return pipes.builtAppScriptsDev;
});

gulp.task('watch-less', function () {
  gulp.watch(paths.lessFiles, ['less']).on('change', browserSync.reload);
});

gulp.task('watch-html', function () {
  gulp.watch(paths.html, ['move-html']).on('change', browserSync.reload);
});

gulp.task('watch-img', function () {
  gulp.watch(paths.img, ['moveImg']).on('change', browserSync.reload);
});

gulp.task('watch-js', ['build-js-app'], browserSync.reload);

gulp.task('serve', ['build-js-app'], function () {

  // Serve files from the root of this project
  browserSync({
    server: {
      baseDir: distDir
    },
    ghostMode: {
      clicks: true,
      scroll: true,
      links: true,
      forms: true
    },
    browser: [ "chromium-browser"
    ]
  });

  // browserSync.reload in the following task start the server
  gulp.watch(paths.scripts, ['watch-js']);
});

// ------------------- builds all the environment ------------------------------------
gulp.task('default', ['build-js-vendors', 'less', 'move-html', 'moveImg',  'watch-less', 'watch-html', 'watch-img', 'serve']);
