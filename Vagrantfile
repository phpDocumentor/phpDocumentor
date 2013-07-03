# -*- mode: ruby -*-
# vi: set ft=ruby :

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

  config.vm.provision :puppet do |puppet|
     puppet.module_path    = ".puppet/modules"
     puppet.manifests_path = ".puppet/manifests"
     puppet.manifest_file  = "phpdoc.pp"
     puppet.options        = [ '--verbose' ]
  end
end
