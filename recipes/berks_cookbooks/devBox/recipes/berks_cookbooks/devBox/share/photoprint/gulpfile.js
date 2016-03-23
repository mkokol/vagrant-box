'use strict';
let fs = require('fs'),
    path = require('path'),
    ini = require('ini'),
    gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    merge = require('merge-stream'),
    lazy = require('lazypipe');


const ROOT_PATH = __dirname,
    CONFIG_PATH = path.resolve(ROOT_PATH, 'application', 'configs'),
    INI_PATH = path.resolve(CONFIG_PATH, 'application.ini'),
    ASSETS_PATH = path.resolve(CONFIG_PATH, 'assets.json');


let config = ini.parse(fs.readFileSync(INI_PATH, 'utf-8')),
    version = config.production.version,
    paths = require(ASSETS_PATH);


let sass = lazy()
    .pipe(plugins.debug, {title: 'sass in'})
    .pipe(plugins.sass, {precision: 10})
    .pipe(plugins.debug, {title: 'sass out'});

let cssmin = lazy()
    .pipe(plugins.debug, {title: 'cssmin in'})
    .pipe(plugins.cssnano, {safe: true})
    .pipe(plugins.debug, {title: 'cssmin out'});

let uglify = lazy()
    .pipe(plugins.debug, {title: 'uglify in'})
    .pipe(plugins.uglify)
    .pipe(plugins.debug, {title: 'uglify out'});

gulp.task('default', () => {
    let assets = merge();

    for (let dest in paths) {
        if (!paths.hasOwnProperty(dest)) {
            continue;
        }

        let concat = lazy()
            .pipe(plugins.debug, {title: 'concat in'})
            .pipe(plugins.concat, {path: dest})
            .pipe(plugins.debug, {title: 'concat out'});

        let versioning = lazy()
            .pipe(plugins.debug, {title: 'versioning in'})
            .pipe(plugins.rename, {suffix: (dest.match(/js$/) ? '.min.' : '.') + version})
            .pipe(plugins.debug, {title: 'versioning out'})
            .pipe(gulp.dest, ROOT_PATH);

        let task = gulp.src(paths[dest])
                       .pipe(plugins.debug({title: 'src out'}))
                       .pipe(plugins.if(['**.scss'], sass()).on('error', plugins.sass.logError))
                       .pipe(plugins.if(['**.css'], cssmin()))
                       .pipe(plugins.if(['**.js'], uglify()))
                       .pipe(concat())
                       .pipe(gulp.dest(ROOT_PATH))
                       .pipe(plugins.if((file) => plugins.match(file, ['**/main.+(js|css)']), versioning()));

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