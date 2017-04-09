if defined?(ChefSpec)
  def enable_macosx_autologin(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:macosx_autologin, :enable, resource_name)
  end

  def disable_macosx_autologin(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:macosx_autologin, :disable, resource_name)
  end
end
