Photoprint

# DB #
to run it you should run in a root of project: 
```bash
$ php vendor/phing/phing/bin/phing.php
```
config for updating db now is separately

(temporary): db-patches/properties/dev.ini

Mysql: db.user = root db.password = 1


# Builder #
## For installation ##
```bash
$ npm install
```
Also see: https://github.com/garris/BackstopJS#if-you-dont-already-have-a-global-phantomjs-install-httpphantomjsorgdownloadhtml


```bash
$ pwd
/var/www/photoprint/
```

### Capture reference screenshots: ###
```bash
$ npm run capture
```
or
```bash
$  gulp reference --cwd ./node_modules/backstopjs
```

### Run comparison: ###
```bash
$ npm run test
```
or
```bash
$  gulp test --cwd ./node_modules/backstopjs --color
```

### Run watcher: ###
```bash
$ npm run watch
```
or
```bash
$ gulp watch
```

### Run single: ###
```bash
$ npm start
```
or
```bash
$ gulp
```