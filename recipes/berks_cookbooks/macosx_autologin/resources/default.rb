actions :enable, :disable
default_action :enable

attribute :username, kind_of: String, name_attribute: true
attribute :password, kind_of: [String, NilClass]
attribute :restart_loginwindow, kind_of: [TrueClass, FalseClass], default: false
attribute :confidential, kind_of: [TrueClass, FalseClass], default: true
