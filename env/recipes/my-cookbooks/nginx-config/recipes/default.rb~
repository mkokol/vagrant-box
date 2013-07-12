package "nginx-config"

# здесь мы создаём папку
directory node["nginx-config"]["path"] do
  owner "root"
  group "root"
  mode "0755"
  action :create
  recursive true
end

# а здесь создаём VirtualHost, используя наши переменные (атрибуты)
web_app 'nginx-config' do
  template 'vhost.erb'
  docroot node['nginx-config']['path']
  server_name node['nginx-config']['server_name']
  server_email node['nginx-config']['server_email']
end
