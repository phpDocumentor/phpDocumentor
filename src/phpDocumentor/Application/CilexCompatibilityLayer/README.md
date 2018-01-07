Cilex Compatibility Layer
=========================

For phpDocumentor 3 we wanted to migrate from Cilex to Symfony 4 without breaking backwards
compatibility with userland plugins.

With this bundle we provide a layer simulating all the necessary classes and interfacing to
process Cilex ServiceProviders and inject the defined services into Symfony.

It is important to note that this Compatibility Layer only simulates Cilex if it is isn't
loaded. As such phpDocumentor 3 is incompatible with a project including Cilex as its own
framework.

Another important note is that the classes in this bundle do not follow the PSR-2 namespace
naming convention as we need to capture specific classes. This also means that when you
want to use this that you need to add the necessary autoloading statements to your composer.json.
