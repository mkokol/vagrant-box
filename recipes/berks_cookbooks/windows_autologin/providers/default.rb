use_inline_resources

def whyrun_supported?
  true
end

WINLOGON_KEY = 'HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon'.freeze

def windows_domain(username)
  '\\'.include?(username) ? username.split('\\').first : nil
end

def windows_user(username)
  '\\'.include?(username) ? username.split('\\').last : username
end

action :enable do
  raise 'Password required!' if new_resource.password.nil?

  registry_key "set AutoAdminLogon for #{new_resource.username}" do
    key WINLOGON_KEY
    values [
      { name: 'AutoAdminLogon', type: :string, data: '1' },
      { name: 'DefaultUsername', type: :string, data: windows_user(new_resource.username) },
      { name: 'DefaultPassword', type: :string, data: new_resource.password },
      { name: 'DefaultDomainName', type: :string, data: windows_domain(new_resource.username) }
    ]
    sensitive new_resource.confidential
    action :create
  end

  if new_resource.count > 0
    registry_key "set AutoLogonCount for #{new_resource.username}" do
      key WINLOGON_KEY
      values [{ name: 'AutoLogonCount', type: :dword, data: new_resource.count }]
      sensitive new_resource.confidential
      action :create
    end
  else
    registry_key "delete AutoLogonCount for #{new_resource.username}" do
      key WINLOGON_KEY
      values [{ name: 'AutoLogonCount', type: :dword, data: nil }]
      action :delete
    end
  end
end

action :disable do
  registry_key 'disable AutoAdminLogon' do
    key WINLOGON_KEY
    values [{ name: 'AutoAdminLogon', type: :string, data: '0' }]
    action :create
  end

  registry_key "delete DefaultPassword for #{new_resource.username}" do
    key WINLOGON_KEY
    values [{ name: 'DefaultPassword', type: :string, data: nil }]
    action :delete
  end

  registry_key "delete AutoLogonCount for #{new_resource.username}" do
    key WINLOGON_KEY
    values [{ name: 'AutoLogonCount', type: :dword, data: nil }]
    action :delete
  end
end
