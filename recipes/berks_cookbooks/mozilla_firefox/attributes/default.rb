default['mozilla_firefox']['version'] = 'latest-esr'
default['mozilla_firefox']['lang'] = 'en-US'
default['mozilla_firefox']['force_32bit'] = node['mozilla_firefox']['32bit_only']
default['mozilla_firefox']['use_package_manager'] = true
default['mozilla_firefox']['packages'] = case node['platform_family']
                                         when 'debian'
                                           %w(bzip2 libasound2 libgtk2.0-0 libdbus-glib-1-2 libxt6)
                                         when 'fedora'
                                           %w(bzip2 autoconf-213 gtk3-devel GConf2-devel dbus-glib-devel
                                              yasm-devel alsa-lib-devel pulseaudio-libs-devel)
                                         else
                                           %w(bzip2 gtk3-devel GConf2-devel dbus-glib-devel
                                              alsa-lib-devel pulseaudio-libs-devel)
                                         end
