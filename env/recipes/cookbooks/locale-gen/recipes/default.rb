case node[:platform]
when 'debian'
  node[:localegen][:lang].each do |lang|
    bash "append_locale" do
      user "root"
      environment ({'lang' => lang})
      code <<-EOH
echo $lang >> /etc/locale.gen
EOH
    end
  end

  execute "locale_gen" do
    command "locale-gen"
  end
when 'ubuntu'
  node[:localegen][:lang].each do |lang|
    bash "append_locale" do
      user "root"
      environment ({'lang' => lang})
      code "locale-gen $lang"
    end
  end
end