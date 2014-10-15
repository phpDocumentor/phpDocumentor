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
