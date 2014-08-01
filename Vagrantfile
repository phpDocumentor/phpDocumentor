# -*- mode: ruby -*-
# vi: set ft=ruby :

# source: https://stackoverflow.com/questions/2108727/which-in-ruby-checking-if-program-exists-in-path-from-ruby
def which(cmd)
  exts = ENV['PATHEXT'] ? ENV['PATHEXT'].split(';') : ['']
  ENV['PATH'].split(File::PATH_SEPARATOR).each do |path|
    exts.each { |ext|
      exe = File.join(path, "#{cmd}#{ext}")
      return exe if File.executable? exe
    }
  end
  return nil
end

Vagrant::Config.run do |config|
  config.vm.box     = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.share_folder(
    "vagrant-root",
    "/vagrant",
    ".",
    :nfs => (RUBY_PLATFORM =~ /linux/ or RUBY_PLATFORM =~ /darwin/)
  )
  config.vm.network :hostonly, "192.168.255.2"
  config.vm.host_name = "dev.app.phpdoc.org"

  if which('ansible-playbook')
      config.vm.provision "ansible" do |ansible|
        ansible.playbook = "ansible/playbook.yml"
        ansible.tags     = "vagrant"
      end
  else
    config.vm.provision :shell, path: "ansible/windows.sh"
  end
end
