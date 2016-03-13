# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|

    # web server
    config.vm.define :web do |web|
        web.vm.box = "debian-squeeze"
        # Debian Squeeze amd64 (Chef 10.24.4, Puppet 3.1.1, VirtualBox 4.2.12)
        # https://dl.dropboxusercontent.com/u/13054557/vagrant_boxes/debian-squeeze.box
        web.vm.box_url = "~/debian-squeeze.box"

        # Boot with a GUI so you can see the screen. (Default is headless)
        # config.vm.boot_mode = :gui
        web.vm.network :hostonly, "192.168.56.11"
        web.vm.network :bridged, :bridge => "eth0"
        web.vm.forward_port 80, 8081

        web.vm.share_folder "www", "/var/www", "/mnt/web-www", :create => true

        web.vm.provision :shell, :inline => "sudo apt-get update"

        web.vm.provision :chef_solo do |chef|
            # chef.log_level = :debug
            chef.cookbooks_path = ["recipes/cookbooks", "recipes/my_cookbooks"]

            chef.add_recipe "locale-gen"
            chef.add_recipe "apt"
            chef.add_recipe "vim"
            chef.add_recipe "git"
            chef.add_recipe "subversion"

            chef.add_recipe "memcached"
            chef.add_recipe "redisio"

            chef.add_recipe "squeeze_php_fpm"
            chef.add_recipe "php::module_curl"
            chef.add_recipe "php::module_mysql"
            chef.add_recipe "php::module_gd"
            chef.add_recipe "php::module_memcache"

            chef.add_recipe "nginx"
            chef.add_recipe "nginx_config"

            chef.json = {
                :localegen  => {
                    'lang' => ['en_US','en_US.utf8','de_DE','de_DE.utf8' ]
                },
                :php => {
                    :version => "5.3.*"
                },
                :memcached => {
                    :memory => 64
                },
                :nginx_config => {
                    :folders => [
                        {
                            :path => "/var/www/intvou"
                        }
                    ],
                    :hosts => [
                        {
                            :path => "/var/www/intvou/frontend/public/",
                            :server_name => "intvou.frontend.vb",
                            :server_email => "admin@intvou.frontend"
                        },
                        {
                            :path => "/var/www/intvou/backend/public/",
                            :server_name => "intvou.backend.vb",
                            :server_email => "admin@intvou.backend"
                        }
                    ]
                }
            }
        end
    end
end