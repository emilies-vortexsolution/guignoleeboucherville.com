// ## Globals
const argv         = require( 'minimist' )( process.argv.slice( 2 ) );
const autoprefixer = require( 'gulp-autoprefixer' );
const browserSync  = require( 'browser-sync' ).create();
const changed      = require( 'gulp-changed' );
const concat       = require( 'gulp-concat' );
const flatten      = require( 'gulp-flatten' );
const gulp         = require( 'gulp' );
const gulpif       = require( 'gulp-if' );
const imagemin     = require( 'gulp-imagemin' );
const jshint       = require( 'gulp-jshint' );
const lazypipe     = require( 'lazypipe' );
const less         = require( 'gulp-less' );
const merge        = require( 'merge-stream' );
const cleanCSS     = require( 'gulp-clean-css' );
const plumber      = require( 'gulp-plumber' );
const rev          = require( 'gulp-rev' );
const sass         = require( 'gulp-sass' );
const sourcemaps   = require( 'gulp-sourcemaps' );
const uglify       = require( 'gulp-uglify' );
const po2json      = require( 'gulp-po2json' );
const jpegtran     = require( 'imagemin-jpegtran' );
const gifsicle     = require( 'imagemin-gifsicle' );
const svgo         = require( 'imagemin-svgo' );
const optipng      = require( 'imagemin-optipng' );
const stripBom     = require( 'gulp-stripbom' );
const sassUnicode  = require( 'gulp-sass-unicode' );
const webp         = require( 'imagemin-webp' );
const rename       = require( 'gulp-rename' );
const babel        = require( 'gulp-babel' );



// See https://github.com/austinpray/asset-builder
const manifest = require( 'asset-builder' )( './assets/manifest.json' );

// `path` - Paths to base asset directories. With trailing slashes.
// - `path.source` - Path to the source files. Default: `assets/`
// - `path.dist` - Path to the build directory. Default: `dist/`
const path = manifest.paths;

// `config` - Store arbitrary configuration values here.
const config = manifest.config || {};

// `globs` - These ultimately end up in their respective `gulp.src`.
// - `globs.js` - Array of asset-builder JS dependency objects. Example:
//   ```
//   {type: 'js', name: 'main.js', globs: []}
//   ```
// - `globs.css` - Array of asset-builder CSS dependency objects. Example:
//   ```
//   {type: 'css', name: 'main.css', globs: []}
//   ```
// - `globs.fonts` - Array of font path globs.
// - `globs.images` - Array of image path globs.
const globs = manifest.globs;


// `project` - paths to first-party assets.
// - `project.js` - Array of first-party JS assets.
// - `project.css` - Array of first-party CSS assets.
const project = manifest.getProjectGlobs();

const isProduction = function() {
  return argv.production || argv.prod;
};

// CLI options
const enabled = {
  // Enable static asset revisioning when `--production`
  production   : isProduction(),
  // Disable source maps when `--production`
  maps         : ! isProduction(),
  // Fail styles task on error when `--production`
  failStyleTask: isProduction(),
  // Fail due to JSHint warnings only when `--production`
  failJSHint   : isProduction(),
  // Strip debug statments from javascript when `--production`
  stripJSDebug : isProduction(),
  browserSync  : argv.browser,

};

// Path to the compiled assets manifest in the dist directory
const revManifest = path.dist + 'assets.json';

// Error checking; produce an error rather than crashing (if not in `--production`).
const onError = function(err) {
  console.log( err.toString() );
  if ( ! enabled.production ) {
    return this.emit( 'end' );
  }
  throw Error( err.toString() );
};

// ## Reusable Pipelines
// See https://github.com/OverZealous/lazypipe

// ### Write to rev manifest
// If there are any revved files then write them to the rev manifest.
// See https://github.com/sindresorhus/gulp-rev
const writeToManifest = function(directory, done) {
  return lazypipe()
    .pipe( gulp.dest, path.dist + directory )
    .pipe( browserSync.stream, {match: '**/*.{js,css}'} )
    .pipe(
      rev.manifest,
      revManifest,
      {
        base: path.dist,
        merge: true
      }
    )
    .pipe( gulp.dest, path.dist )().on( 'end', done );
};

// ### CSS processing pipeline
// Example
// ```
// gulp.src(cssFiles)
//   .pipe(cssTasks('main.css')
//   .pipe(gulp.dest(path.dist + 'styles'))
// ```
const cssTasks = function(filename) {
  return lazypipe()
    .pipe(
      function() {
        return gulpif( ! enabled.failStyleTask, plumber() );
      }
    )
    .pipe(
      function() {
        return gulpif( enabled.maps, sourcemaps.init() );
      }
    )
    .pipe(
      function() {
        return gulpif( '*.less', less() );
      }
    )
    .pipe(
      function() {
        return gulpif(
          '*.scss',
          sass(
            {
              outputStyle: 'compressed', // libsass doesn't support expanded yet
              precision: 10,
              includePaths: ['.'],
              errLogToConsole: ! enabled.failStyleTask
            }
          )
        );
      }
    )
    .pipe( sassUnicode )
    .pipe( stripBom )
    .pipe( concat, filename )
    .pipe( autoprefixer )
    .pipe(
      function() {
        return gulpif( enabled.production, cleanCSS() );
      }
    )
    .pipe(
      function() {
        return gulpif( enabled.production, rev() );
      }
    )
    .pipe(
      function() {
        return gulpif(
          enabled.maps,
          sourcemaps.write(
            '.',
            {
              sourceRoot: 'assets/styles/'
            }
          )
        );
      }
    )();
};

const processStyles = function(done) {
  const merged = merge();
  manifest.forEachDependency(
    'css',
    function(dep) {
      const cssTasksInstance = cssTasks( dep.name );
      if ( ! enabled.failStyleTask) {
        cssTasksInstance.on(
          'error',
          function(err) {
            console.error( err.message );
            this.emit( 'end' );
          }
        );
      }
      merged.add(
        gulp.src( dep.globs, {base: 'styles'} )
        .pipe( plumber( {errorHandler: onError} ) )
        .pipe( cssTasksInstance )
      );
    }
  );
  return merged
    .pipe( writeToManifest( 'styles', done ) );
};

// ### JS processing pipeline
// Example
// ```
// gulp.src(jsFiles)
//   .pipe(jsTasks('main.js')
//   .pipe(gulp.dest(path.dist + 'scripts'))
// ```
const jsTasks = function(filename) {
  return lazypipe()
    .pipe(
      function() {
        return gulpif( enabled.maps, sourcemaps.init() );
      }
    )
    .pipe( concat, filename )
    .pipe(
      function(){
        return gulpif( enabled.production, uglify( { compress: { 'drop_debugger': enabled.stripJSDebug } } ).on( 'error', onError ) );
      }
    )
    .pipe(
      function() {
        return gulpif( enabled.production, rev() );
      }
    )
    .pipe(
      function() {
        return gulpif(
          enabled.maps,
          sourcemaps.write(
            '.',
            {
              sourceRoot: 'assets/scripts/'
            }
          )
        );
      }
    )();
};

const processScripts = function(done) {
  const merged = merge();
  manifest.forEachDependency(
    'js',
    function(dep) {
      merged.add(
        gulp.src( dep.globs, {base: 'scripts'} )
        .pipe( plumber( {errorHandler: onError} ) )
        .pipe( babel() )
        .pipe( jsTasks( dep.name ) )
      );
    }
  );
  return merged
    .pipe( writeToManifest( 'scripts', done ) );
};

// ## Gulp tasks
// Run `gulp -T` for a task summary


// ### Translation Json
// `gulp translation` - export .po files to .json translations
gulp.task(
  'translation',
  function() {
    return gulp.src( ['./lang/*.po'] )
    .pipe( po2json( { format: 'jed' } ) )
    .pipe( gulp.dest( './lang/' ) );
  }
);

// ### JSHint
// `gulp jshint` - Lints configuration JSON and project JS.
gulp.task(
  'jshint',
  function() {
    return gulp.src( [  'gulpfile.js' ].concat( project.js ) )
    .pipe( jshint() )
    .pipe( jshint.reporter( 'jshint-stylish' ) )
    .pipe( gulpif( enabled.failJSHint, jshint.reporter( 'fail' ) ) );
  }
);

// ### Styles
// `gulp styles` - Compiles, combines, and optimizes project CSS.
// By default this task will only log a warning if a precompiler error is
// raised. If the `--production` flag is set: this task will fail outright.
gulp.task( 'styles', gulp.series( processStyles ) );

// ### Scripts
// `gulp scripts` - Runs JSHint then compiles, combines, and optimizes project JS.
gulp.task( 'scripts', gulp.series( 'jshint', 'translation', processScripts ) );

// ### Fonts
// `gulp fonts` - Grabs all the fonts and outputs them in a flattened directory
// structure. See: https://github.com/armed/gulp-flatten
gulp.task(
  'fonts',
  function() {
    return gulp.src( globs.fonts )
    .pipe( flatten() )
    .pipe( gulp.dest( path.dist + 'fonts' ) )
    .pipe( browserSync.stream() );
  }
);

// ### Images
gulp.task(
  'images-png-svg',
  function() {
    return gulp.src( globs.images )
    .pipe(
      imagemin(
        [
        jpegtran( {progressive: true} ),
        gifsicle( {interlaced: true} ),
        optipng(),
        svgo(
          {plugins: [
            {removeUnknownsAndDefaults: false},
            {cleanupIDs: false}
            ]}
        )
        ]
      )
    )
    .pipe( gulp.dest( path.dist + 'images' ) )
    .pipe( browserSync.stream() );
  }
);

gulp.task(
  'images-webp',
  function() {

    return gulp.src( globs.images[0] + '.{jpg,png}' )
    .pipe(
      imagemin(
        [
        webp( {lossless: true } )
        ]
      )
    )
    .pipe(
      rename(
        function (path) {
          path.extname = ".webp";
        }
      )
    )
    .pipe( gulp.dest( path.dist + 'images' ) )
    .pipe( browserSync.stream() );

  }
);

// `gulp images` - Run lossless compression on all the images.
gulp.task( 'images', gulp.series( 'images-png-svg', 'images-webp' ) );

// ### Clean
// `gulp clean` - Deletes the build folder entirely.
gulp.task( 'clean', require( 'del' ).bind( null, [path.dist] ) );

// ### Watch
// `gulp watch` - Use BrowserSync to proxy your dev server and synchronize code
// changes across devices. Specify the hostname of your dev server at
// `manifest.config.devUrl`. When a modification is made to an asset, run the
// build step for that asset and inject the changes into the page.
// See: http://www.browsersync.io
gulp.task(
  'watch',
  function() {

    if (enabled.browserSync) {
      browserSync.init(
        {
          files: ['{lib,templates}/**/*.php', '*.php'],
          proxy: config.devUrl,
          snippetOptions: {
            whitelist: ['/wp-admin/admin-ajax.php'],
            blacklist: ['/wp-admin/**'],
          },
        }
      );
    }

    gulp.watch( [path.source + 'styles/**/*'], gulp.series( 'styles' ) );
    gulp.watch( [path.source + 'scripts/**/*'], gulp.series( 'jshint', 'scripts' ) );
    gulp.watch( [path.source + 'fonts/**/*'], gulp.series( 'fonts' ) );
    gulp.watch( [path.source + 'images/**/*'], gulp.series( 'images' ) );
    gulp.watch( ['assets/manifest.json'], gulp.series( 'build' ) );
  }
);

// ### Translation
gulp.task(
  'translation',
  function() {
    return gulp.src( ['./lang/**/*.po'] )
    .pipe( po2json( { format: 'jed' } ) )
    .pipe( gulp.dest( './lang/' ) );
  }
);

// ### Build
// `gulp build` - Run all the build tasks but don't clean up beforehand.
// Generally you should be running `gulp` instead of `gulp build`.
gulp.task(
  'build',
  gulp.series(
    'styles',
    'scripts',
    gulp.parallel( 'fonts', 'images' )
  )
);

// ### Gulp
// `gulp` - Run a complete build. To compile for production run `gulp --production`.
gulp.task( 'default', gulp.series( 'clean', 'build' ) );
