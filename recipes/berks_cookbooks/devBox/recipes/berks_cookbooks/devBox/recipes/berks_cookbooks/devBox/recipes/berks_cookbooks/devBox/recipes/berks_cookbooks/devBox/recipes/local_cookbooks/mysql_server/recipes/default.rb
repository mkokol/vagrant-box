Chef::Log.info("Install mysql-server")

mysql_service 'default' do
  port '3306'
  initial_root_password '1'
  server_debian_password '1'
  server_repl_password '1'
  action [:create, :start]
end