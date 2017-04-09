# Changelog

## 5.0.0 2017-03-24

- Rename sensitive attribute to confidential to be Chef 13 compatible

## 4.0.0 2017-02-03

- Replace recipe with resource to allow for suppressing sensitive info

## 3.0.0 2015-09-13

- Merge remove recipe into default recipe

## 2.0.0 2015-05-21

- Convert cookbook from LWRPs to Recipes
- Default restart_loginwindow to false

## 1.1.2 2015-05-20

- Fix Foodcritic false positive on sensitive resource

## 1.1.1 2015-05-20

- Update Chef requirements

## 1.1.0 2015-05-20

- Suppress password output on enable action
- Fix disable action fails when autoLoginUse attribute is missing from com.apple.loginwindow.plist

## 1.0.0 2015-05-18

- Initial release
