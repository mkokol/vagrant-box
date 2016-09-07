# install ngings configuration

node["nginx_config"]["hosts"].each do |vhost|
  Chef::Log.info("Add new directory: #{vhost}")
  directory "/var/www/#{vhost['server_name']}" do
    owner "vagrant"
    group "vagrant"
    mode "0755"
    action :create
    recursive true
  end

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

service :nginx do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end
