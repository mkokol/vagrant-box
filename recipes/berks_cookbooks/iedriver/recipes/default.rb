if platform?('windows')
  version = node['iedriver']['version']
  iedriver_dir = "#{node['iedriver']['home']}\\iedriver-#{version}"

  directory node['iedriver']['home'] do
    action :create
  end

  bit = node['kernel']['machine'] == 'x86_64' ? 'x64' : 'Win32'
  zip = "#{Chef::Config[:file_cache_path]}\\IEDriverServer_#{bit}_#{version}.zip"

  powershell_script "unzip #{zip}" do
    code "Add-Type -A 'System.IO.Compression.FileSystem';" \
        " [IO.Compression.ZipFile]::ExtractToDirectory('#{zip}', '#{iedriver_dir}');"
    action :nothing
  end

  remote_file zip do
    source "#{node['iedriver']['url']}/#{iedriver_short_version(version)}/IEDriverServer_#{bit}_#{version}.zip"
    not_if { ::File.exist?("#{iedriver_dir}/IEDriverServer.exe") }
    notifies :run, "powershell_script[unzip #{zip}]", :immediately if platform?('windows')
  end

  name = 'Command line server for the IE Driver'
  execute "Firewall rule '#{name}'" do
    command "netsh advfirewall firewall add rule name=\"#{name}\" dir=in profile=private"\
    " action=allow program=\"#{iedriver_dir}\\IEDriverServer.exe\""
    action :run
    not_if "netsh advfirewall firewall show rule name=\"#{name}\" > nul"
  end

  link "#{node['iedriver']['home']}\\IEDriverServer.exe" do
    to "#{iedriver_dir}\\IEDriverServer.exe"
  end

  env 'Add iedriver to path' do
    key_name 'PATH'
    action :modify
    delim ::File::PATH_SEPARATOR
    value node['iedriver']['home']
  end

  include_recipe 'iedriver::config_ie' if node['iedriver']['config_ie']
else
  Chef::Log.warn("IEDriverServer cannot be installed on #{node['platform']} platform!")
end
