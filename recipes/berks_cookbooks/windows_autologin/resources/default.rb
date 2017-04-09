actions :enable, :disable
default_action :enable

attribute :username, kind_of: String, name_attribute: true
attribute :password, kind_of: [String, NilClass]
attribute :count, kind_of: Integer, default: 0

attribute :confidential, kind_of: [TrueClass, FalseClass], default: true
