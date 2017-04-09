Chef::Log.info("Install kibana and elasticsearch")

execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

package "apt-transport-https" do
  action :install
end

apt_repository "elk-repo" do
  uri "https://artifacts.elastic.co/packages/5.x/apt"
  components [:main]
  distribution 'stable'
  action :add
  key 'https://packages.elastic.co/GPG-KEY-elasticsearch'
  notifies :run, resources(:execute => "apt-get-update"), :immediately
end

package "elasticsearch" do
  action :install
  version "5.3.0"
end

template "/etc/elasticsearch/jvm.options" do
  source "jvm.options.erb"
  owner "root"
  group "root"
  mode "0644"
  variables({})
end

service "elasticsearch" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end

package "kibana" do
  action :install
  version "5.3.0"
end

template "/etc/kibana/kibana.yml" do
  source "kibana.yml.erb"
  owner "root"
  group "root"
  mode "0644"
  variables({})
end

service "kibana" do
  supports :status => true, :restart => true, :reload => true
  action [:enable, :start]
end
