#package "apt"


Chef::Log.info("Add new virtual host: #{node['nginx_config']['server_name']}")

# create root directory for project
directory node["nginx_config"]["www_path"] do
  owner "vagrant"
  group "vagrant"
  mode "0755"
  action :create
  recursive true
end

# create VirtualHost for nginx
template "/etc/nginx/sites-available/#{node['nginx_config']['server_name']}.conf" do
  source "vhost.erb"
  owner "root"
  group "root"
  mode "0644"
end

# create simlink in sites-enabled
link "/etc/nginx/sites-enabled/#{node['nginx_config']['server_name']}.conf" do
  to "/etc/nginx/sites-available/#{node['nginx_config']['server_name']}.conf"
  action :create
end

%W{php5-fpm nginx}.each do |s|
  service s do
    action :restart
  end
end
