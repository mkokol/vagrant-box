actions :install, :upgrade, :remove
default_action :install

attribute(:version, kind_of: String, name_attribute: true)
attribute(:checksum, kind_of: String)
attribute(:attempts, kind_of: Integer, default: 5)
attribute(:lang, kind_of: String, default: lazy { node['mozilla_firefox']['lang'] })
attribute(:force_32bit, kind_of: [TrueClass, FalseClass], default: lazy { node['mozilla_firefox']['force_32bit'] })
attribute(:use_package_manager, kind_of: [TrueClass, FalseClass],
                                default: lazy { node['mozilla_firefox']['use_package_manager'] })
attribute(:path, kind_of: [String, NilClass])
attribute(:link, kind_of: [String, Array, NilClass])
attribute(:packages, kind_of: Array, default: lazy { node['mozilla_firefox']['packages'] })

attribute(:windows_ini_source, kind_of: String, default: 'windows.ini.erb')
attribute(:windows_ini_content, kind_of: String, default: lazy { { InstallDirectoryPath: :path } })
attribute(:windows_ini_cookbook, kind_of: String, default: 'mozilla_firefox')
