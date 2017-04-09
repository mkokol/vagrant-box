use_inline_resources

def whyrun_supported?
  true
end

def win_long_version(version)
  new_resource.version.include?('esr') ? "#{version} ESR" : version
end

# support firefox 64-bit windows (v42.0+)
def win_64bit?
  x86_64? && (latest_version? || new_resource.version.split('.').first.to_i >= 42)
end

def x86_64?
  node['kernel']['machine'] == 'x86_64' && !new_resource.force_32bit
end

def version(download_url)
  /.\d\.\d.\d|\d+.\d/.match(download_url).to_s
end

def filename(download_url)
  download_url.slice(download_url.rindex('/') + 1, download_url.size)
end

def latest_version?
  new_resource.version.include?('latest')
end

# Returns resolved download url, e.g.,
# https://download.mozilla.org/?product=firefox-latest&os=linux64&lang=en-US ->
# http://download.cdn.mozilla.net/pub/firefox/releases/47.0.1/linux-x86_64/en-US/firefox-47.0.1.tar.bz2
def download_url
  uri = URI("https://download.mozilla.org/?product=#{firefox_product}&os=#{firefox_os}&lang=#{new_resource.lang}")
  response = Net::HTTP.start(uri.host, use_ssl: true, verify_mode: OpenSSL::SSL::VERIFY_NONE) do |http|
    http.get uri.request_uri
  end
  raise("#{response.code} #{response.message}: #{uri}") unless response.is_a?(Net::HTTPRedirection)
  response['location']
end

def firefox_product
  case new_resource.version
  when 'latest'
    'firefox-latest'
  when 'latest-esr'
    'firefox-esr-latest'
  when 'latest-beta'
    'firefox-beta-latest'
  else
    "firefox-#{new_resource.version}-SSL"
  end
end

def firefox_os
  case node['platform']
  when 'windows'
    win_64bit? ? 'win64' : 'win'
  when 'mac_os_x'
    'osx'
  else
    x86_64? ? 'linux64' : 'linux'
  end
end

def explode_tarball(filename, dest_path)
  directory dest_path do
    recursive true
  end

  execute 'untar-firefox' do
    command "tar --strip-components=1 -xjf #{filename} -C #{dest_path}"
  end
end

def windows_install(download_url)
  bit = win_64bit? ? 'x64' : 'x86'
  options = '-ms'

  unless new_resource.path.nil?
    rendered_ini = "#{Chef::Config[:file_cache_path]}/firefox-#{new_resource.version}.ini"
    options = "/INI=#{rendered_ini}"

    template rendered_ini do # ~FC021
      source new_resource.windows_ini_source
      variables new_resource.windows_ini_content
      cookbook new_resource.windows_ini_cookbook
    end
  end

  # https://wiki.mozilla.org/Installer:Command_Line_Arguments
  windows_package "Mozilla Firefox #{win_long_version(version(download_url))} (#{bit} #{new_resource.lang})" do
    source download_url
    retries new_resource.attempts
    installer_type :custom
    options options
    checksum new_resource.checksum unless new_resource.checksum.nil?
    action :install
  end
end

def osx_install(download_url)
  dmg_package 'Firefox' do
    dmg_name 'firefox'
    destination new_resource.path unless new_resource.path.nil?
    source download_url
    retries new_resource.attempts
    checksum new_resource.checksum unless new_resource.checksum.nil?
    action :install
  end
end

def linux_install(download_url)
  cached_file = ::File.join(Chef::Config[:file_cache_path], filename(download_url))
  path = new_resource.path.nil? ? "/opt/firefox/#{version(download_url)}_#{new_resource.lang}" : new_resource.path

  remote_file cached_file do
    source download_url
    retries new_resource.attempts
    checksum new_resource.checksum unless new_resource.checksum.nil?
    action :create
  end

  new_resource.packages.each do |pkg|
    package pkg
  end

  explode_tarball(cached_file, path)

  link new_resource.link.nil? ? '/usr/bin/firefox' : new_resource.link do # ~FC021
    to ::File.join(path, 'firefox').to_s
  end

  return unless new_resource.link.is_a?(Array)

  new_resource.link.each do |lnk|
    link lnk do
      to ::File.join(path, 'firefox').to_s
    end
  end
end

def firefox_install
  if platform?('windows', 'mac_os_x') || (platform?('ubuntu') && !new_resource.use_package_manager)
    url = download_url
    case node['platform']
    when 'windows'
      windows_install(url)
    when 'mac_os_x'
      osx_install(url)
    else
      linux_install(url)
    end
  else
    pkg = platform?('debian') ? 'firefox-esr' : 'firefox'
    # install at compile time so version is available during convergence
    package pkg do
      retries new_resource.attempts
      action :nothing
    end.run_action(:upgrade)
  end
end

action :install do
  firefox_install
end

action :update do
  firefox_install
end
