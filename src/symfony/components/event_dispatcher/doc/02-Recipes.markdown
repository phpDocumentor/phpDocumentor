Recipes
=======

This chapter lists common usages of the event dispatcher.

Passing along the Event Dispatcher Object
-----------------------------------------

If you have a look at the `sfEventDispatcher` class, you will notice
that the class does not act as a Singleton (there is no
`getInstance()` static method). That is intentional, as you might want
to have several concurrent event dispatchers in a single PHP
request. But it also means that you need a way to pass the
dispatcher to the objects that need to connect or notify events.

The best practice is to inject the event dispatcher object into your
objects, aka dependency injection.

You can use constructor injection:

    [php]
    class Foo
    {
      protected $dispatcher = null;

      public function __construct(sfEventDispatcher $dispatcher)
      {
        $this->dispatcher = $dispatcher;
      }
    }

Or setter injection:

    [php]
    class Foo
    {
      protected $dispatcher = null;

      public function setEventDispatcher(sfEventDispatcher $dispatcher)
      {
        $this->dispatcher = $dispatcher;
      }
    }

Choosing between the two is really a matter of taste. I tend to
prefer the constructor injection as the objects are fully
initialized at construction time. But when you have a long list of
dependencies, using setter injection can be the way to go,
especially for optional dependencies.

>**TIP**
>If you use dependency injection like we did in the two examples above,
>you can then easily use the Symfony Dependency Injection container to
>elegantly manage these objects.

Doing something before or after a Method Call
---------------------------------------------

If you want to do something just before, or just after a method is
called, you can notify respectively an event at the beginning or at
the end of the method:

    [php]
    class Foo
    {
      // ...

      public function send($foo, $bar)
      {
        // do something before the method
        $event = new sfEvent($this, 'foo.do_before_send', array('foo' => $foo, 'bar' => $bar));
        $this->dispatcher->notify($event);

        // the real method implementation is here
        // $ret = ...;

        // do something after the method
        $event = new sfEvent($this, 'foo.do_after_send', array('ret' => $ret));
        $this->dispatcher->notify($event);

        return $ret;
      }
    }

Adding Methods to a Class
-------------------------

To allow multiple classes to add methods to another one, you can
define the magic `__call()` method in the class you want to be
extended like this:

    [php]
    class Foo
    {
      // ...

      public function __call($method, $arguments)
      {
        // create an event named 'foo.method_is_not_found'
        // and pass the method name and the arguments passed to this method
        $event = new sfEvent($this, 'foo.method_is_not_found', array('method' => $method, 'arguments' => $arguments));

        // calls all listeners until one is able to implement the $method
        $this->dispatcher->notifyUntil($event);

        // no listener was able to proces the event? The method does not exist
        if (!$event->isProcessed())
        {
          throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
        }

        // return the listener returned value
        return $event->getReturnValue();
      }
    }

Then, create a class that will host the listener:

    [php]
    class Bar
    {
      public function addBarMethodToFoo(sfEvent $event)
      {
        // we only want to respond to the calls to the 'bar' method
        if ('bar' != $event['method'])
        {
          // allow another listener to take care of this unknown method
          return false;
        }

        // the subject object (the foo instance)
        $foo = $event->getSubject();

        // the bar method arguments
        $arguments = $event['parameters'];

        // do something
        // ...

        // set the return value
        $event->setReturnValue($someValue);

        // tell the world that you have processed the event
        return true;
      }
    }

Eventually, add the new `bar` method to the `Foo` class:

    [php]
    $dispatcher->connect('foo.method_is_not_found', array($bar, 'addBarMethodToFoo'));


Modifying Arguments
-------------------

If you want to allow third party classes to modify arguments passed
to a method just before that method is executed, add a `filter`
event at the beginning of the method:

    [php]
    class Foo
    {
      // ...

      public function render($template, $arguments = array())
      {
        // filter the arguments
        $event = new sfEvent($this, 'foo.filter_arguments');
        $this->dispatcher->filter($event, $arguments);

        // get the filtered arguments
        $arguments = $event->getReturnValue();

        // the method starts here
      }
    }

And here is an example of a filter:

    [php]
    class Bar
    {
      public function filterFooArguments(sfEvent $event, $arguments)
      {
        $arguments['processed'] = true;

        return $arguments;
      }
    }
