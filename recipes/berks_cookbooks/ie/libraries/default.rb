def ie_version
  return unless platform_family?('windows')
  version = ''
  registry_get_values('HKLM\SOFTWARE\Microsoft\Internet Explorer').each do |value|
    return value[:data] if value[:name] == 'svcVersion' # ie >= 10
    # http://support.microsoft.com/en-us/kb/969393
    version = value[:data] if value[:name] == 'Version' # ie < 10
  end
  version
end
