class phpdocumentor::profiling {

     #automation of the steps and dependencies described here https://github.com/preinheimer/xhgui

     php::module { "mcrypt": }

     class { "php::pear": }

     php::pecl::module { "mongo":
        use_package     => 'false',
     }

     #I couldn't find the manifest to do this so enable extension using the puppet file resource
     #Not sure if there is a better way
     file { "/etc/php5/cli/conf.d/mongo.ini":
        content => "; enable mongo php extension\nextension=mongo.so;",
     }

     php::pecl::module { "xhprof":
        use_package     => 'false',
        preferred_state => 'beta',
     }

     #I couldn't find the manifest to do this so enable extension using the puppet file resource
     #Not sure if there is a better way
     file { "/etc/php5/cli/conf.d/xhprof.ini":
        content => "; enable mongo php extension\nextension=xhprof.so",
     }

     #gets the code from github for xhgui.
     vcsrepo { "/var/www/xhgui":
         ensure => present,
         provider => git,
         source => 'https://github.com/preinheimer/xhgui',
         before => Exec['install_xhgui_dependacies']
     }

     # xhgui recommneds to run php install.php. All this php file does is install
     # composer, call composer to install libraries and do some file permissions
     # Composer is already installed so instead using puppet to do what the install.php would do

     exec { "install_xhgui_dependacies" :
         command => "/usr/bin/composer update --prefer-dist",
         cwd => "/var/www/xhgui",
         require => Exec['composer install']
     }

     apache::vhost { 'profiling.phpdocumentor.local':
        port => '80',
        docroot => '/var/www/xhgui/webroot'
     }

     file { "/var/www/xhgui/cache":
         ensure => "directory",
         owner  => "www-data",
         group  => "www-data",
         mode   => 740,
         require => Vcsrepo['/var/www/xhgui']
     }

     apache::module { 'rewrite': }

}