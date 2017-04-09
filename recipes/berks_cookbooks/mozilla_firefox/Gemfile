source 'https://rubygems.org'

gem 'chef', '~> 12.6'

group :lint do
  gem 'foodcritic'
  gem 'rubocop'
end

group :unit do
  gem 'berkshelf'
  gem 'chefspec'
end

group :integration do
  gem 'kitchen-dokken'
  gem 'kitchen-localhost'
  gem 'kitchen-vagrant'
  gem 'test-kitchen', '~> 1.13.0'
  gem 'winrm-fs'
end
