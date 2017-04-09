if defined?(ChefSpec)
  def enable_windows_autologin(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:windows_autologin, :enable, resource_name)
  end

  def disable_windows_autologin(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:windows_autologin, :disable, resource_name)
  end
end
