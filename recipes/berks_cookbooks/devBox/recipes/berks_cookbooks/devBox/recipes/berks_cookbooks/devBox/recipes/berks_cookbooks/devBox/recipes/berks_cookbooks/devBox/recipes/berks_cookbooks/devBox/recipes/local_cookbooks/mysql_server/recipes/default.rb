Chef::Log.info("Install mysql-server")

mysql_service 'default' do
  port '3306'
  initial_root_password node["mysql"]["server_root_password"]
  socket '/var/run/mysqld/mysqld.sock'
  action [:create, :start]
end
