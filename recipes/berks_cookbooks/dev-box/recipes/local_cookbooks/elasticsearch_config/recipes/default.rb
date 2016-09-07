Chef::Log.info("start elastic")

elasticsearch_configure 'elasticsearch' do
  allocated_memory node["elasticsearch_config"]["allocated_memory"]
  configuration ({
    'cluster.name' => node["elasticsearch_config"]["cluster_name"],
    'node.name' => node["elasticsearch_config"]["node_name"]
  })
end

service "elasticsearch" do
  action :restart
end
