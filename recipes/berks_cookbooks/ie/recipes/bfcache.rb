# https://msdn.microsoft.com/en-us/library/ie/ee330720(v=vs.85).aspx
BFCACHE_32 = 'HKLM\SOFTWARE\Microsoft\Internet Explorer\Main\FeatureControl\FEATURE_BFCACHE'
BFCACHE_64 = 'HKLM\SOFTWARE\Wow6432Node\Microsoft\Internet Explorer\Main\FeatureControl\FEATURE_BFCACHE'

if platform?('windows')
  bfcache = node['kernel']['machine'] == 'x86_64' ? BFCACHE_64 : BFCACHE_32

  if node['ie']['bfcache']
    registry_key bfcache do
      values [{ name: 'iexplore.exe', type: :dword, data: 0 }]
      recursive true
    end
  else
    registry_key bfcache do
      values [{ name: 'iexplore.exe', type: :dword }]
      action :delete
    end
  end
else
  log('Recipe ie::bfcache is only available for Windows platforms!') { level :warn }
end
