Events and the Event Dispatcher
===============================

Objected Oriented code has gone a long way to ensuring code written
for your projects is extensible. By creating classes that have well
defined responsibilities, your code becomes more flexible.

If a user wants to modify a class's behavior, he can extend it using
a subclass to override the behaviour. But if the user wants to share
his changes with other users who have made their own subclasses to
change the behaviour, code inheritance is moot.

A real-world example is when you want to provide a plugin system for
your class. A plugin should be able to add methods, or do something
before or after a method is executed, without interfering with other
plugins. This is not an easy problem to solve with single
inheritance, and multiple inheritance (were it possible with PHP)
has its own drawbacks.

Enter Symfony Event Dispatcher. The library implements the
[Observer](http://en.wikipedia.org/wiki/Observer_pattern) pattern in
a simple and effective way to make all these things possible and
make your projects truly extensible (see the recipes section below
for some possible implementations of these patterns).

The main goal of Symfony Event Dispatcher is to allow objects to
communicate together without knowing each other. It is possible
thanks to a central object, the *dispatcher*.

Objects (*listeners*) can *connect* to the dispatcher to listen to
specific events, and some others can *notify* an *event* to the
dispatcher. Whenever an event is notified, the dispatcher will call
the listeners.

Events
------

Unlike many other observer implementations, you don't need to create
a class to create a new event. All events are of course still
objects, but all events are instances of the built-in `sfEvent`
class.

>**NOTE**
>You can of course extends the `sfEvent` class to specialize an event
>further, or enforce some constraints, but most of the time it would add
>a new level of complexity that is not necessary.

An event is uniquely identified by a string. By convention, it is
better to use lowercase letters, numbers and underscores (`_`) for
event names. Furthermore, to better organize your events, a good
convention is to prefix the event name with a namespace followed by
a dot (`.`).

Here are examples of good event names:

    [php]
    change_culture
    response.filter_content

As you might have noticed, event names contain a verb to indicate
that they relate to something that happens.

The Dispatcher
--------------

The dispatcher is the object responsible for maintaining a register of
listeners and calling them whenever an event is notified.

By default, the dispatcher class is `sfEventDispatcher`:

    [php]
    $dispatcher = new sfEventDispatcher();

Event Objects
-------------

The event object, of class `sfEvent`, stores information about the
notified event. Its constructor takes three arguments:

  * The *subject* of the event (most of the time, this is the object
    notifying the event, but it can also be `null`);

  * The event name;

  * An array of parameters to pass to the listeners (an empty array
    by default).

As most of the time an event is called from an object context, the
first argument is almost always `$this`:

    [php]
    $event = new sfEvent($this, 'user.change_culture', array('culture' => $culture));

The event object has several methods to get event information:

  * `getName()`: Returns the identifier of the event.

  * `getSubject()`: Gets the subject object attached to the event;

  * `getParameters()`: Returns the event parameters.

The event object can also be accessed as an array to get its
parameters:

    [php]
    echo $event['culture'];

Connecting Listeners
--------------------

Obviously, you need to connect some listeners to the dispatcher before
it can be useful. A call to the dispatcher `connect()` method
associates a PHP callable to an event.

The `connect()` method takes two arguments:

  * The event name;

  * A PHP callable to call when the event is notified.

>**NOTE**
>A [PHP callable](http://www.php.net/manual/en/function.is-callable.php)
>is a PHP variable that can be used by the `call_user_func()` function
>and returns `true` when passed to the `is_callable()` function. A
>string represents a function, and an array can represent an object
>method or a class method.

    [php]
    $dispatcher->connect('user.change_culture', $callable);

Once a listener is registered with the event dispatcher, it waits
until the event is notified. The event dispatcher keeps a record of
all event listeners, and knows which ones to call when an event is
notified.

>**NOTE**
>The listeners are called by the event dispatcher in the same order you
>connected them.

For the example above, `$callable` will be called by the dispatcher
whenever the `user.change_culture` event is notified by an object.

When calling the listeners, the dispatcher passes them an `sfEvent`
object as a parameter. So, the listener receives the event object as
its first argument.

Notifying Events
----------------

Events can be notified by using three methods:

 * `notify()`
 * `notifyUntil()`
 * `filter()`

### `notify`

The `notify()` method notifies all listeners in turn.

    [php]
    $dispatcher->notify($event);

By using the `notify()` method, you make sure that all the listeners
registered on the notified event are executed but none can return a
value to the subject.

### `notifyUntil`

In some cases, you need to allow a listener to stop the event and
prevent further listeners from being notified about it. In this
case, you should use `notifyUntil()` instead of `notify()`. The
dispatcher will then execute all listeners until one returns `true`,
and then stop the event notification:

    [php]
    $dispatcher->notifyUntil($event);

The listener that stops the chain may also call the
`setReturnValue()` method to return back some value to the subject.

The notifier can check if a listener has processed the event by
calling the `isProcessed()` method:

    [php]
    if ($event->isProcessed())
    {
      $ret = $event->getReturnValue();

      // ...
    }

### `filter`

The `filter()` method asks all listeners to filter a given value,
passed by the notifier as its second argument, and retrieved by the
listener callable as the second argument:

    [php]
    $dispatcher->filter($event, $response->getContent());

All listeners are passed the value and they must return the filtered
value, whether they altered it or not. All listeners are guaranteed
to be executed.

The notifier can get the filtered value by calling the
`getReturnValue()` method:

    [php]
    $ret = $event->getReturnValue();
