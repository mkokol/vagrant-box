Chef::Log.info("start elastic")

service "elasticsearch" do
  action :restart
end
