Chef::Log.info("Install kibana 4")

execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

apt_repository 'kibana4' do
  uri 'http://packages.elastic.co/kibana/4.4/debian'
  components ['stable', 'main']
  action :add
  notifies :run, resources(:execute => "apt-get-update"), :immediately
end

package "kibana" do
  action :install
  options '--force-yes'
end

service "kibana" do
  action :restart
end
