if defined?(ChefSpec)
  def install_mozilla_firefox(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:mozilla_firefox, :install, resource_name)
  end

  def update_mozilla_firefox(resource_name)
    ChefSpec::Matchers::ResourceMatcher.new(:mozilla_firefox, :update, resource_name)
  end
end
