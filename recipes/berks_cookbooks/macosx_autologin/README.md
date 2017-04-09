# Mac OS X Autologin Cookbook

[![Cookbook Version](http://img.shields.io/cookbook/v/macosx_autologin.svg?style=flat-square)][cookbook]
[![Build Status](http://img.shields.io/travis/dhoer/chef-macosx_autologin.svg?style=flat-square)][travis]

[cookbook]: https://supermarket.chef.io/cookbooks/macosx_autologin
[travis]: https://travis-ci.org/dhoer/chef-macosx_autologin

Enables/disables automatic login for user on boot using a modified version of 
Gavin Brock's [kcpassword](http://www.brock-family.org/gavin/perl/kcpassword.html). 
                                                  
## Requirements

- Chef 11.14+ 

### Platforms

- Mac OS X 10.7+

## Usage

Requires super-user privileges. 

Enable automatic login for user and display login screen

```ruby
macosx_autologin 'username' do
  password 'password
  restart_loginwindow true
  action :enable
end
```

Disable automatic login and display login screen

```ruby
macosx_autologin 'username' do
  restart_loginwindow true
  action :disable
end
```

### Actions

- `enable` - Enables autologin.
- `disable` - Disables autologin.

### Attributes

- `username` - Username to login as. Required when enabled. Defaults 
to resource block name. 
- `password` - Password of username. Required when enabled.
- `restart_loginwindow` - Display login screen. Default `false`.

## ChefSpec Matchers

This cookbook includes custom [ChefSpec](https://github.com/sethvargo/chefspec) matchers you can use to test 
your own cookbooks.

Example Matcher Usage

```ruby
expect(chef_run).to enable_macosx_autologin('username').with(
  password: 'password'
)
```
      
Cookbook Matchers

- enable_macosx_autologin(resource_name)
- disable_macosx_autologin(resource_name)

## Getting Help

- Ask specific questions on [Stack Overflow](http://stackoverflow.com/questions/tagged/osx+autologin).
- Report bugs and discuss potential features in
[Github issues](https://github.com/dhoer/chef-macosx_autologin/issues).

## Contributing

Please refer to [CONTRIBUTING](https://github.com/dhoer/chef-macosx_autologin/blob/master/CONTRIBUTING.md).

## License

MIT - see the accompanying [LICENSE](https://github.com/dhoer/chef-macosx_autologin/blob/master/LICENSE.md) file
for details.
