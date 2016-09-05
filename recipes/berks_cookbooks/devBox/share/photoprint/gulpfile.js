'use strict';
let fs = require('fs'),
    path = require('path'),
    ini = require('ini'),
    gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    merge = require('merge-stream'),
    lazy = require('lazypipe'),
    gutil = require('gulp-util');

const ROOT_PATH = __dirname,
    CONFIG_PATH = path.resolve(ROOT_PATH, 'application', 'configs'),
    INI_PATH = path.resolve(CONFIG_PATH, 'application.ini'),
    ASSETS_PATH = path.resolve(CONFIG_PATH, 'assets.json');


let config = ini.parse(fs.readFileSync(INI_PATH, 'utf-8')),
    version = config.production.version,
    paths = require(ASSETS_PATH);


let sass = lazy()
    .pipe(plugins.sass, {precision: 10});

let cssmin = lazy()
    .pipe(plugins.cssnano, {safe: true});

let uglify = lazy()
    .pipe(plugins.uglify);

gulp.task('default', () => {
    let assets = merge();

    for (let dest in paths) {
        if (!paths.hasOwnProperty(dest)) {
            continue;
        }

        let concat = lazy()
            .pipe(plugins.concat, {path: dest});

        let versioning = lazy()
            .pipe(plugins.rename, {suffix: (dest.match(/js$/) ? '.min.' : '.') + version})
            .pipe(gulp.dest, ROOT_PATH);

        let task = gulp.src(paths[dest])
            .pipe(plugins.if(['**.scss'], sass()).on('error', plugins.sass.logError))
            .pipe(plugins.if(['**.css'], cssmin()))
            .pipe(plugins.if(['**.js'], uglify()))
            .pipe(concat())
            .pipe(gulp.dest(ROOT_PATH))
            .pipe(plugins.if((file) => plugins.match(file, ['**/main.+(js|css)']), versioning()))
            .on('end', function(){ gutil.log(dest); });

        assets.add(task);
    }

    return assets;
});

gulp.task('watch', ['default'], function () {
    let assets = ['public/**/*.scss'];

    for (let path in paths) {
        if (!paths.hasOwnProperty(path) || !path.match(/\.js$/)) {
            continue;
        }
        for (let i = 0, l = paths[path].length; i < l; i++) {
            assets.push(paths[path][i]);
        }
    }

    return gulp.watch(assets, ['default']);
});