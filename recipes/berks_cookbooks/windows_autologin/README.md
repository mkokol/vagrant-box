# Windows Autologin Cookbook

[![Cookbook Version](http://img.shields.io/cookbook/v/windows_autologin.svg?style=flat-square)][cookbook]
[![Build Status](https://img.shields.io/appveyor/ci/dhoer/chef-windows-autologin/master.svg?style=flat-square)][win]

[cookbook]: https://supermarket.chef.io/cookbooks/windows_autologin
[win]: https://ci.appveyor.com/project/dhoer/chef-windows-autologin

Enables/disables automatic logon using Windows 
[AutoAdminLogon](https://technet.microsoft.com/en-us/library/cc939702.aspx).
 
Automatic logon uses username (domain can be included, e.g., 
domain\username) and password stored in the registry to log users on 
to the computer when the system starts. The Log On to Windows dialog 
box is not displayed.

Use count to limit the Number of Automatic Logins. Once the limit has 
been reached the auto logon feature will be disabled. 

**WARNING:** Automatic logon allows other users to start your computer 
and to log on using your account, password is stored 
unencrypted under windows registry 
`HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon` when 
enabled, and Chef outputs the password when updating autologin registry.   
                                                  
## Requirements

- Chef 11.6+ (includes a built-in registry_key resource) 

### Platforms

- Windows

## Usage

Requires Administrator privileges. 

Enable automatic login for user

```ruby
windows_autologin 'enable autologin' do
  username 'username'
  password my_secret
  action :enable
end
```

Disable automatic login and remove password and count entry

```ruby
windows_autologin 'username' do
  action :disable
end
```

### Actions

- `enable` - Enables autologin.
- `disable` - Disables autologin.

### Attributes

* `username` -  The username to autologin as. Defaults to resource 
block name. Note that username can include domain.
* `password` - Required to enable. Default: `nil`.
* `count` - Number of Automatic Logins. Once the limit has been reached 
the auto logon feature will be disabled. Default: `0`.
* `confidential` - Ensure that sensitive resource data is not logged by 
the chef-client. Default: `true`.

## ChefSpec Matchers

This cookbook includes custom [ChefSpec](https://github.com/sethvargo/chefspec) matchers you can use to test 
your own cookbooks.

Example Matcher Usage

```ruby
expect(chef_run).to enable_windows_autologin('username').with(
  password: 'password'
)
```
      
Cookbook Matchers

- enable_windows_autologin(resource_name)
- disable_windows_autologin(resource_name)

## Getting Help

- Ask specific questions on [Stack Overflow](http://stackoverflow.com/questions/tagged/windows+autologin).
- Report bugs and discuss potential features in
[Github issues](https://github.com/dhoer/chef-windows_autologin/issues).

## Contributing

Please refer to [CONTRIBUTING](https://github.com/dhoer/chef-windows_autologin/blob/master/CONTRIBUTING.md).

## License

MIT - see the accompanying [LICENSE](https://github.com/dhoer/chef-windows_autologin/blob/master/LICENSE.md) file
for details.
