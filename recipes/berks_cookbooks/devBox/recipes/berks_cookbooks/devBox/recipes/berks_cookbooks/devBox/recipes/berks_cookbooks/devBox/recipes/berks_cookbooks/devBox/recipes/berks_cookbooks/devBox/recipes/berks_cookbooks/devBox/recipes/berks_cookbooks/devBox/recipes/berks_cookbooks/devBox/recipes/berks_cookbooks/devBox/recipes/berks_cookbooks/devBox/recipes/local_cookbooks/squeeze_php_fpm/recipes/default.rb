Chef::Log.info("Install php-fpm")

#cookbook_file "/etc/apt/sources.list.d/dotdeb.list" do
#  source "dotdeb.list"
#  mode 0644
#  owner "root"
#  group "root"
#  notifies :run, resources(:execute => "apt-get update"), :immediately
#end

bash "dotdeb keys" do
  code <<-EOH
    gpg --keyserver keys.gnupg.net --recv-key 89DF5277
    gpg -a --export 89DF5277 | apt-key add -
  EOH
  action :nothing
end

execute "apt-get update" do
  action :nothing
end

apt_repository "dotdeb.org" do
  uri "http://php53.dotdeb.org"
  components ["squeeze", "all"]
  deb_src true
  action :add
  notifies :run, resources(:bash => "dotdeb keys", :execute => "apt-get update"), :immediately
end

%w{php5-fpm}.each do |pkg|
  package pkg do
    action :install
  end
end
