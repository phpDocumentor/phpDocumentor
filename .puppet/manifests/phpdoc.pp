group { "puppet":
    ensure => "present",
}

node default {
    File { owner => 0, group => 0, mode => 0644 }
    file { '/etc/motd':
      content => "Welcome to the Development Environment of phpDocumentor2"
    }

    exec { "apt-update":
      command => "/usr/bin/apt-get update"
    }
    Exec["apt-update"] -> Package <| |>

    include phpdocumentor
}
