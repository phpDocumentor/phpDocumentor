class phpdocumentor::setup {
    package{["git", "graphviz", "python-setuptools", "make", "texlive-latex-recommended", "texlive-fonts-recommended", "openjdk-6-jre"]:
        ensure => present
    }

    include php
    php::module { "xsl": }
    php::module { "intl": }

    class { 'composer':
      command_name => 'composer',
      target_dir   => '/usr/bin',
      auto_update  => true
    }

    include composer
    exec { "composer install":
      cwd     => "/vagrant",
      command => "/usr/bin/composer install --dev > /var/log/phpdoc-composer.log",
      require => [ Package["git"], Class["php", "composer"], Php::Module["xsl", "intl"] ],
      timeout => 0
    }

    exec { "sudo easy_install -U sphinx":
        command => "/usr/bin/sudo /usr/bin/easy_install -U sphinx",
        require => [ Package["python-setuptools"] ],
        timeout => 0
    }
}