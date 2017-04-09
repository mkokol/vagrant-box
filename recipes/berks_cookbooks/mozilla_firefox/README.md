# Mozilla Firefox Cookbook

[![Cookbook Version](http://img.shields.io/cookbook/v/mozilla_firefox.svg?style=flat-square)][cookbook]
[![linux](http://img.shields.io/travis/dhoer/chef-mozilla_firefox/master.svg?label=linux&style=flat-square)][linux]
[![osx](http://img.shields.io/travis/dhoer/chef-mozilla_firefox/macosx.svg?label=macosx&style=flat-square)][osx]
[![win](https://img.shields.io/appveyor/ci/dhoer/chef-mozilla-firefox/master.svg?label=windows&style=flat-square)][win]

[cookbook]: https://supermarket.chef.io/cookbooks/mozilla_firefox
[linux]: https://travis-ci.org/dhoer/chef-mozilla_firefox/branches
[osx]: https://travis-ci.org/dhoer/chef-mozilla_firefox/branches
[win]: https://ci.appveyor.com/project/dhoer/chef-mozilla-firefox 

This cookbook installs Firefox browser. Mac OS X, Ubuntu, and Windows download directly from 
[Mozilla](https://download-installer.cdn.mozilla.net/pub/firefox/releases/latest/README.txt) where you can specify 
version (e.g., `latest`, `latest-esr`, `latest-beta`, `42.0`, `38.4.0esr`, or `43.0b4`) and language with 
`latest-esr` and `en-US` being the defaults. CentOS, Red Hat, Ubuntu and Debian platforms default to using the package manager.
 
A `firefox_version` method is also available to retrieve the default version installed.

## Requirements

Chef 12.6+

### Platforms
* CentOS/Red Hat
* Debian/Ubuntu
* Mac OS X
* Windows

### Cookbooks
* dmg

## Usage

Include default recipe in a cookbook or a run list to install Firefox browser.

The following example retrieves the default installed version by using `firefox_version` method:

```ruby
v = firefox_version
```

**Tip:** use `allow_any_instance_of` to stub firefox_version method when testing with rspec:

```ruby
allow_any_instance_of(Chef::Recipe).to receive(:firefox_version).and_return('42.0')
```

### Attributes
* `node['mozilla_firefox']['version']` - Install `latest`, `latest-esr`, `latest-beta`, or specific version 
e.g., `42.0`, `38.4.0esr`, or `43.0b4`. Ignored on CentOS, Red Hat and Debian platforms when `use_package_manager` is true. 
Default is `latest-esr`.
* `node['mozilla_firefox']['lang']` - Language desired. Ignored on CentOS, Red Hat and Debian platforms when `use_package_manager` 
is true.  Default is `en-US`.
* `node['mozilla_firefox']['force_32bit']` - Install 32-bit browser on 64-bit machines. Ignored on Mac OS X and package 
installs. Default `false`.
* `node['mozilla_firefox']['use_package_manager']` - Install using apt or yum package manager. CentOS, Red Hat, Ubuntu and Debian platforms only. 
Default is `true`.
* `node['mozilla_firefox']['packages']` - Dependency packages for non-package installs. 
Linux platform only. Default values depend on Linux platform.


# Resources

Use mozilla_firefox resource to install multiple versions of firefox on the same server.  Note that firefox_version
method should not be used when multiple firefox versions are installed.

## mozilla_firefox

### Attributes
* `version` - Install `latest`, `latest-esr`, `latest-beta`, or specific version e.g., `42.0`, `38.4.0esr`, or `43.0b4`. 
Ignored on CentOS, Red Hat and Debian platforms when `use_package_manager` is true. 
* `checksum` - SHA256 Checksum of the file. Not required.
* `lang` - Language desired. Ignored on CentOS, Red Hat and Debian platforms when `use_package_manager` is `true`.  Default is `en-US`.
* `force_32bit` -  Install 32-bit browser on 64-bit machines. Ignored on Mac OS X and package installs. Default `false`.
* `path` - Path to install Firefox. Linux: `/opt/firefox/#{version}_#{language}`, Windows: 
`#{ENV['SYSTEMDRIVE']}\\Program Files\\Mozilla Firefox\\firefox.exe` when nil. Default `nil`.
* `use_package_manager` - Install using apt or yum package manager. CentOS/Red Hat and Debian platforms only. Default is `true`.
* `link` - Create the specfied symlink (Linux non-package installs only). This can be an array to create multiple symlinks to the same 
instance, or a string for a single symlink. Default `nil`.
* `packages` - Dependency packages for non-package installs. CentOS, Red Hat and Debian platforms only. Default values depend 
on Linux platform.
* `windows_ini_source` - Template source. Default `windows.ini.erb`.
* `windows_ini_content` -  Template content. Default `InstallDirectoryPath: :path`.
* `windows_ini_cookbook` - Template cookbook. Default `mozilla_firefox`.

## Getting Help
* Ask specific questions on [Stack Overflow](http://stackoverflow.com/questions/tagged/chef+firefox).
* Report bugs and discuss potential features in [Github issues](https://github.com/dhoer/chef-mozilla_firefox/issues).

## Contributing

Please refer to [CONTRIBUTING](https://github.com/dhoer/chef-mozilla_firefox/blob/master/CONTRIBUTING.md).

## License

MIT - see the accompanying [LICENSE](https://github.com/dhoer/chef-mozilla_firefox/blob/master/LICENSE.md) 
file for details.
