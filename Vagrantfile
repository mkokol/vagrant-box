# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    # web server
    config.vm.define :web do |web|
        web.vm.box = "ubuntu-14.04"
        web.vm.hostname = 'web'
        web.vm.box_url = "~/vagran-box-ubuntu-server-14-04-chef.box"

        # Boot with a GUI so you can see the screen. (Default is headless)
        # config.vm.boot_mode = :gui

        web.vm.network :private_network, ip: "192.168.56.11", bridge: "en0"

        web.vm.network "forwarded_port", guest: 22, host: 2202, id: "ssh"
        web.vm.network "forwarded_port", guest: 80, host: 8082, id: "http"
        web.vm.network "forwarded_port", guest: 9200, host: 9202, id: "elastic"

        web.vm.synced_folder "share", "/var/www", :create => true, owner: "www-data", group: "www-data"

        web.vm.provision :shell, :inline => "sudo apt-get update"

        web.vm.provision :chef_solo do |chef|
            # chef.log_level = :debug
            chef.cookbooks_path = [
                "recipes/berks_cookbooks",
                "recipes/local_cookbooks"
            ]

            chef.json = {
                :localegen  => {
                    :lang => ["en_US", "en_US.utf8", "de_DE", "de_DE.utf8"]
                },
                :php => {
                    :version => "7.0.*"
                },
                :mysql => {
                    :server_root_password => "1"
                },
                :nginx_config => {
                    :hosts => [
                        {
                            :server_name => "photoprint",
                            :environment => "development"
                        }
                    ]
                },
                :java => {
                    :install_flavor => "oracle",
                    :jdk_version => "7",
                    :oracle => {
                        "accept_oracle_download_terms" => true
                    }
                },
                :elasticsearch_config => {
                    'allocated_memory' => '512m',
                    'cluster_name' => "photoprint",
                    'node_name' => "photoprint-node01"
                }
            }

            chef.run_list = [
                "recipe[locale]",
                "recipe[apt]",
                "recipe[vim]",
                "recipe[curl]",
                "recipe[php7.0]",
                "recipe[imagemagick]",
                "recipe[nginx]",
                "recipe[nginx_config]",
                "recipe[mysql_server]",
                "recipe[mysql_tuning]",
                "recipe[java]",
                "recipe[elasticsearch]",
                "recipe[elasticsearch_config]",
                "recipe[kibana4]"
            ]
        end
    end
end