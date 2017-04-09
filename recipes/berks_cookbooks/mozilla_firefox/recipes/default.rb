mozilla_firefox node['mozilla_firefox']['version'] do
  action :nothing
end.run_action(:install)
