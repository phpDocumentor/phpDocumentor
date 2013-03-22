group { "puppet":
    ensure => "present",
}

node default {
    File { owner => 0, group => 0, mode => 0644 }
    file { '/etc/motd':
      content => "Welcome to your Vagrant-built virtual machine! Managed by Puppet.\n"
    }

    exec { "apt-get update":
      command => "/usr/bin/apt-get update"
    }

    include phpdocumentor
}