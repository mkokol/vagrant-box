Chef::Log.info("Install kibana 4")

execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

execute "kibana-right" do
  command "chown -R kibana:kibana /opt/kibana"
  action :nothing
end

apt_repository 'kibana4' do
  uri 'http://packages.elastic.co/kibana/4.4/debian'
  components ['stable', 'main']
  action :add
  notifies :run, resources(:execute => "apt-get-update"), :immediately
end

package "kibana" do
  options '--force-yes'
  action :install
  notifies :run, resources(:execute => "kibana-right"), :immediately
end

#service "kibana" do
#  action :run
#end

execute "install-sense" do
  command "/opt/kibana/bin/kibana plugin --install elastic/sense"
  action :run
  not_if do FileTest.directory?('/opt/kibana/installedPlugins/sense') end
  notifies :run, resources(:execute => "kibana-right"), :immediately
end

service "kibana" do
  action :restart
end
