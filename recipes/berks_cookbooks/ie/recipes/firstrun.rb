if platform?('windows')
  value = node['ie']['firstrun'] ? nil : 1

  registry_key 'HKLM\SOFTWARE\Policies\Microsoft\Internet Explorer\Main' do
    values [{ name: 'DisableFirstRunCustomize', type: :dword, data: value }]
    recursive true
  end

  registry_key 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\ChefIE_FirstRun_DisableFirstRunCustomize' do
    values [
      { name: 'Version', type: :string, data: Time.now.to_i },
      { name: 'StubPath', type: :string, data: "reg add \"HKCU\\SOFTWARE\\Policies\\Microsoft\\"\
      "Internet Explorer\\Main\" /v DisableFirstRunCustomize /d #{value} /t REG_DWORD /f" }
    ]
  end
else
  log('Recipe ie::firstrun is only available for Windows platforms!') { level :warn }
end
