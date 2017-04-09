if platform?('windows')
  registry_key 'HKLM\SOFTWARE\Microsoft\Active Setup\Installed Components\ChefIE_Zoom_ZoomFactor' do
    values [
      { name: 'Version', type: :string, data: Time.now.to_i },
      { name: 'StubPath', type: :string, data: "reg add \"HKCU\\SOFTWARE\\Microsoft\\Internet Explorer\\Zoom\"" \
        " /v ZoomFactor /d #{node['ie']['zoom']} /t REG_DWORD /f" }
    ]
  end
else
  log('Recipe ie::zoom is only available for Windows platforms!') { level :warn }
end
