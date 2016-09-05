Chef::Log.info("Install php7.0")

apt_package "software-properties-common" do
  action :install
end

execute "apt-get-update" do
  command "apt-get update"
  ignore_failure true
  action :nothing
end

execute "add php7 repository" do
  command "add-apt-repository ppa:ondrej/php"
  user "root"
  notifies :run, resources(:execute => "apt-get-update"), :immediately
end

%w{php7.0}.each do |pkg|
  package pkg do
    action :install
  end
end
