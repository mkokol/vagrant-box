# MozillaFirefox helper
module MozillaFirefox
  def firefox_version(url = nil)
    return url.match(/(-|%20)([\d|.]*).(exe|dmg|tar\.bz2)/)[2] if url # http://rubular.com/r/thFO453EZZ

    case node['platform']
    when 'windows'
      begin
        firefox_shellout("\"#{ENV['SystemDrive']}\\Program Files\\Mozilla Firefox\\firefox.exe\" -v | more")
          .match(/Mozilla Firefox (.*)/)[1]
      rescue
        firefox_shellout("\"#{ENV['SystemDrive']}\\Program Files (x86)\\Mozilla Firefox\\firefox.exe\" -v | more")
          .match(/Mozilla Firefox (.*)/)[1]
      end
    when 'debian'
      begin
        firefox_shellout('iceweasel -v').match(/Mozilla Firefox (.*)/)[1]
      rescue
        firefox_shellout('firefox -v').match(/Mozilla Firefox (.*)/)[1]
      end
    when 'mac_os_x'
      firefox_shellout('/Applications/Firefox.app/Contents/MacOS/firefox -v')
    else
      firefox_shellout('firefox -v').match(/Mozilla Firefox (.*)/)[1]
    end
  end

  # private

  def firefox_shellout(command)
    cmd = Mixlib::ShellOut.new(command)
    cmd.run_command
    cmd.stdout.strip
  end
end

Chef::Provider.send(:include, MozillaFirefox)
Chef::Recipe.send(:include, MozillaFirefox)
Chef::Resource.send(:include, MozillaFirefox)
