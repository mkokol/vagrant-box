# https://support.microsoft.com/en-us/kb/182569
SECURITY_ZONES = 'HKCU\SOFTWARE\Microsoft\Windows\CurrentVersion\Internet Settings\Zones'
ACTIVE_SETUP_ZONES = 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\ChefIE_Zone'

if platform?('windows')
  timestamp = Time.now.to_i
  security_zones =  node['ie']['zone']

  security_zones['local_home'].each do |k, v|
    registry_key "#{ACTIVE_SETUP_ZONES}0_#{k}" do
      values [
        { name: 'Version', type: :string, data: timestamp },
        { name: 'StubPath', type: :string, data: "reg add \"#{SECURITY_ZONES}\\0\" /v #{k} /d #{v} /t REG_DWORD /f" }
      ]
      recursive true
    end
  end

  security_zones['internet'].each do |k, v|
    registry_key "#{ACTIVE_SETUP_ZONES}3_#{k}" do
      values [
        { name: 'Version', type: :string, data: timestamp },
        { name: 'StubPath', type: :string, data: "reg add \"#{SECURITY_ZONES}\\3\" /v #{k} /d #{v} /t REG_DWORD /f" }
      ]
      recursive true
    end
  end

  security_zones['local_internet'].each do |k, v|
    registry_key "#{ACTIVE_SETUP_ZONES}1_#{k}" do
      values [
        { name: 'Version', type: :string, data: timestamp },
        { name: 'StubPath', type: :string, data: "reg add \"#{SECURITY_ZONES}\\1\" /v #{k} /d #{v} /t REG_DWORD /f" }
      ]
      recursive true
    end
  end

  security_zones['trusted_sites'].each do |k, v|
    registry_key "#{ACTIVE_SETUP_ZONES}2_#{k}" do
      values [
        { name: 'Version', type: :string, data: timestamp },
        { name: 'StubPath', type: :string, data: "reg add \"#{SECURITY_ZONES}\\2\" /v #{k} /d #{v} /t REG_DWORD /f" }
      ]
      recursive true
    end
  end

  security_zones['restricted_sites'].each do |k, v|
    registry_key "#{ACTIVE_SETUP_ZONES}4_#{k}" do
      values [
        { name: 'Version', type: :string, data: timestamp },
        { name: 'StubPath', type: :string, data: "reg add \"#{SECURITY_ZONES}\\4\" /v #{k} /d #{v} /t REG_DWORD /f" }
      ]
      recursive true
    end
  end
else
  log('Recipe ie::zone is only available for Windows platforms!') { level :warn }
end
