# install ngings configuration

node["nginx_config"]["folders"].each do |folder|
  Chef::Log.info("Add new directory: #{folder}")

  # create root directory for project
  directory folder["path"] do
    owner "vagrant"
    group "vagrant"
    mode "0755"
    action :create
    recursive true
  end
end

node["nginx_config"]["hosts"].each do |vhost|
  Chef::Log.info("Add new virtual host: #{vhost}")

  # create VirtualHost for nginx
  template "/etc/nginx/sites-available/#{vhost['server_name']}.conf" do
    source "vhost.erb"
    owner "root"
    group "root"
    mode "0644"
    variables({ :vhost => vhost })
  end

  # create simlink in sites-enabled for VirtualHost
  link "/etc/nginx/sites-enabled/#{vhost['server_name']}.conf" do
    to "/etc/nginx/sites-available/#{vhost['server_name']}.conf"
    action :create
  end
end



%W{nginx}.each do |s|
  service s do
    action :restart
  end
end