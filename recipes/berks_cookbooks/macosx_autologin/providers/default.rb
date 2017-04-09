use_inline_resources

def whyrun_supported?
  true
end

action :enable do
  cookbook_file 'autologin.pl' do
    path "#{Chef::Config[:file_cache_path]}/autologin.pl"
    cookbook 'macosx_autologin'
    mode '0755'
    action :create
  end

  restart_loginwindow = new_resource.restart_loginwindow ? 1 : 0

  execute 'enable automatic login' do # ~FC009
    command "sudo #{Chef::Config[:file_cache_path]}/autologin.pl "\
      "#{new_resource.username} #{new_resource.password} #{restart_loginwindow}"
    sensitive new_resource.confidential
  end
end

action :disable do
  execute 'delete autoLoginUser from com.apple.loginwindow' do
    command 'sudo defaults delete /Library/Preferences/com.apple.loginwindow "autoLoginUser"'
    returns [0, 1]
  end

  execute 'delete /etc/kcpassword' do
    command 'sudo rm -f /etc/kcpassword'
  end

  execute 'restart loginwindow' do # ~FC021
    command 'sudo killall loginwindow'
    only_if { new_resource.restart_loginwindow }
  end
end
