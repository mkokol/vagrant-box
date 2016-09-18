# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    # photoprint server
    config.vm.define :photoprint do |photoprint|
        photoprint.vm.box = "ubuntu-14.04"
        photoprint.vm.hostname = "photoprint"
        photoprint.vm.box_url = "~/vagran-box-ubuntu-server-14-04-chef.box"

        # Boot with a GUI so you can see the screen. (Default is headless)
        # config.vm.boot_mode = :gui

        config.vm.provider "virtualbox" do |v|
            v.name = "photoprint"
            v.cpus = 2
            v.memory = 2048
        end

        photoprint.vm.network :private_network, ip: "192.168.56.11"

        photoprint.vm.network "forwarded_port", guest: 22, host: 2202, id: "ssh"
        photoprint.vm.network "forwarded_port", guest: 80, host: 8082, id: "http"
        photoprint.vm.network "forwarded_port", guest: 9200, host: 9202, id: "elastic"

        photoprint.vm.synced_folder "share", "/var/www", :create => true, owner: "vagrant", group: "www-data"

        photoprint.vm.provision :shell, :inline => "sudo apt-get update"

        photoprint.vm.provision :chef_solo do |chef|
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
                },
                :postfix => {
                    :main => {
                        'myhostname' => 'photoprint.in.ua',
                        'inet_interfaces' => 'loopback-only'
                    }
                }
            }

            chef.run_list = [
                'recipe[locale]',
                'recipe[apt]',
                'recipe[vim]',
                'recipe[curl]',
                'recipe[php7.0]',
                'recipe[imagemagick]',
                'recipe[nginx]',
                'recipe[nginx_config]',
                'recipe[mysql_server]',
                'recipe[mysql_tuning]',
                'recipe[java]',
                'recipe[elasticsearch]',
                'recipe[elasticsearch_config]',
                'recipe[kibana4]',
                'recipe[postfix]',
                'recipe[phpmyadmin]'
            ]
        end
    end

    # pinloft server
    config.vm.define :pinloft do |pinloft|
        pinloft.vm.box = "bento/ubuntu-16.04"
        pinloft.vm.hostname = "pinloft"

        pinloft.vm.provider "virtualbox" do |v|
            # Boot with a GUI so you can see the screen. (Default is headless)
            # v.gui = true

            v.name = "pinloft"
            v.cpus = 2
            v.memory = 2048
        end

        pinloft.vm.synced_folder "share", "/var/www", create: true, type: "nfs"
        pinloft.vm.network :private_network, ip: "192.168.56.12"

        pinloft.vm.network "forwarded_port", guest: 22, host: 2203, id: "ssh"
        pinloft.vm.network "forwarded_port", guest: 80, host: 8083, id: "http"
        pinloft.vm.network "forwarded_port", guest: 9200, host: 9203, id: "elastic"

        pinloft.vm.provision :shell, :inline => "sudo apt-get update"

        pinloft.vm.provision :chef_solo do |chef|
            # Rub chef with debug mode
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
                            :server_name => "pinloft",
                            :index_location => "/public",
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
                :elasticsearch => {
                    "version" => "2.4.0"
                },
                :elasticsearch_config => {
                    "allocated_memory" => "512m",
                    "cluster_name" => "pinloft",
                    "node_name" => "pinloft-node01"
                },
                :postfix => {
                    :main => {
                        'myhostname' => 'pinloft.com',
                        'inet_interfaces' => 'loopback-only'
                    }
                }
            }

            chef.run_list = [
                'recipe[locale]',
                'recipe[apt]',
                'recipe[vim]',
                'recipe[curl]',
                'recipe[nginx]',
                'recipe[nginx_config]',
                'recipe[php7.0]',
                'recipe[imagemagick]',
                'recipe[java]',
                'recipe[elasticsearch]',
                'recipe[elasticsearch_config]',
                'recipe[kibana4]',
                'recipe[postfix]'
            ]
        end
    end
end