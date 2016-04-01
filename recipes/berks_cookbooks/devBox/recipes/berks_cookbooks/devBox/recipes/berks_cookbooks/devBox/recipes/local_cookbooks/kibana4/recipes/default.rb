Chef::Log.info("Install kibana 4")

apt_repository 'kibana4' do
  uri 'http://packages.elastic.co/kibana/4.4/debian'
  components ['main', 'stable']
  action :add
end
