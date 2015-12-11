Legacy Namespace Converter Plugin
=================================

This plugin will convert legacy/[PEAR-style namespaces](http://pear.php.net/manual/en/standards.naming.php) to real namespaces for phpDocumentor.
For example the class name `My_Special_ClassName` will be transformed into the class `ClassName` with namespace `My\Special`.

Example configuration file:

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <plugins>
        <plugin path="LegacyNamespaceConverter" />
      </plugins>
    </phpdocumentor>

If your legacy class names did not contain a vendor prefix and you are mixing with [PSR-4-style](http://www.php-fig.org/psr/psr-4/) classes, you might want to add a vendor prefix to your legacy classes by configuring this plugin.  
You can archive this by configuring the parameter *NamespacePrefix*:

        ..
        <plugin path="LegacyNamespaceConverter">
          <parameter key="NamespacePrefix">VendorName</parameter>
        </plugin>
        ..

If you're getting generation errors like: 

    PPC:ERR-50008
    PHP Notice:  Undefined index: VAL:ERRLVL-50015 

you probably need to add the default 'Core' plugin also:

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
      <plugins>
        <plugin path="Core" />
        <plugin path="LegacyNamespaceConverter" />
      </plugins>
    </phpdocumentor>

This is so because "when you define plugins you override the whole set so that you could replace everything should you want to.". For more info - [here](https://github.com/phpDocumentor/phpDocumentor2/issues/1534#issuecomment-128092193)
