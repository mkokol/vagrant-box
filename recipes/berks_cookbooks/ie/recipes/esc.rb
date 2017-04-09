# http://support.microsoft.com/kb/933991
if platform?('windows')
  value = node['ie']['esc'] ? 1 : 0

  registry_key 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\{A509B1A7-37EF-4b3f-8CFC-4F3A74704073}' do
    values [{ name: 'IsInstalled', type: :dword, data: value }]
  end

  registry_key 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\{A509B1A8-37EF-4b3f-8CFC-4F3A74704073}' do
    values [{ name: 'IsInstalled', type: :dword, data: value }]
  end

  registry_key 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\ChefIE_ESCZoneMap_IEHarden' do
    values [
      { name: 'Version', type: :string, data: Time.now.to_i },
      { name: 'StubPath', type: :string, data: "reg add \"HKCU\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\" \
          "Internet Settings\\ZoneMap\" /v IEHarden /d #{value} /t REG_DWORD /f" }
    ]
  end
else
  log('Recipe ie::esc is only available for Windows platforms!') { level :warn }
end
