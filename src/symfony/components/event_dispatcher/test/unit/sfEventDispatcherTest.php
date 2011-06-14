<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../lime/lime.php';
require_once dirname(__FILE__).'/../../lib/sfEvent.php';
require_once dirname(__FILE__).'/../../lib/sfEventDispatcher.php';

$t = new lime_test(19);

$dispatcher = new sfEventDispatcher();

// ->connect() ->disconnect()
$t->diag('->connect() ->disconnect()');
$dispatcher->connect('bar', 'listenToBar');
$t->is($dispatcher->getListeners('bar'), array('listenToBar'), '->connect() connects a listener to an event name');
$dispatcher->connect('bar', 'listenToBarBar');
$t->is($dispatcher->getListeners('bar'), array('listenToBar', 'listenToBarBar'), '->connect() can connect several listeners for the same event name');

$dispatcher->connect('barbar', 'listenToBarBar');
$dispatcher->disconnect('bar', 'listenToBarBar');
$t->is($dispatcher->getListeners('bar'), array('listenToBar'), '->disconnect() disconnects a listener for an event name');
$t->is($dispatcher->getListeners('barbar'), array('listenToBarBar'), '->disconnect() disconnects a listener for an event name');

$t->ok($dispatcher->disconnect('foobar', 'listen') === false, '->disconnect() returns false if the listener does not exist');

// ->getListeners() ->hasListeners()
$t->diag('->getListeners() ->hasListeners()');
$t->is($dispatcher->hasListeners('foo'), false, '->hasListeners() returns false if the event has no listener');
$dispatcher->connect('foo', 'listenToFoo');
$t->is($dispatcher->hasListeners('foo'), true, '->hasListeners() returns true if the event has some listeners');
$dispatcher->disconnect('foo', 'listenToFoo');
$t->is($dispatcher->hasListeners('foo'), false, '->hasListeners() returns false if the event has no listener');

$t->is($dispatcher->getListeners('bar'), array('listenToBar'), '->getListeners() returns an array of listeners connected to the given event name');
$t->is($dispatcher->getListeners('foobar'), array(), '->getListeners() returns an empty array if no listener are connected to the given event name');

$listener = new Listener();

// ->notify()
$t->diag('->notify()');
$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'listenToFoo'));
$dispatcher->connect('foo', array($listener, 'listenToFooBis'));
$e = $dispatcher->notify($event = new sfEvent(new stdClass(), 'foo'));
$t->is($listener->getValue(), 'listenToFoolistenToFooBis', '->notify() notifies all registered listeners in order');
$t->is($e, $event, '->notify() returns the event object');

$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'listenToFooBis'));
$dispatcher->connect('foo', array($listener, 'listenToFoo'));
$dispatcher->notify(new sfEvent(new stdClass(), 'foo'));
$t->is($listener->getValue(), 'listenToFooBislistenToFoo', '->notify() notifies all registered listeners in order');

// ->notifyUntil()
$t->diag('->notifyUntil()');
$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'listenToFoo'));
$dispatcher->connect('foo', array($listener, 'listenToFooBis'));
$e = $dispatcher->notifyUntil($event = new sfEvent(new stdClass(), 'foo'));
$t->is($listener->getValue(), 'listenToFoolistenToFooBis', '->notifyUntil() notifies all registered listeners in order and stops if it returns true');
$t->is($e, $event, '->notifyUntil() returns the event object');

$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'listenToFooBis'));
$dispatcher->connect('foo', array($listener, 'listenToFoo'));
$e = $dispatcher->notifyUntil($event = new sfEvent(new stdClass(), 'foo'));
$t->is($listener->getValue(), 'listenToFooBis', '->notifyUntil() notifies all registered listeners in order and stops if it returns true');

// ->filter()
$t->diag('->filter()');
$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'filterFoo'));
$dispatcher->connect('foo', array($listener, 'filterFooBis'));
$e = $dispatcher->filter($event = new sfEvent(new stdClass(), 'foo'), 'foo');
$t->is($e->getReturnValue(), '-*foo*-', '->filter() filters a value');
$t->is($e, $event, '->filter() returns the event object');

$listener->reset();
$dispatcher = new sfEventDispatcher();
$dispatcher->connect('foo', array($listener, 'filterFooBis'));
$dispatcher->connect('foo', array($listener, 'filterFoo'));
$e = $dispatcher->filter($event = new sfEvent(new stdClass(), 'foo'), 'foo');
$t->is($e->getReturnValue(), '*-foo-*', '->filter() filters a value');

class Listener
{
  protected
    $value = '';

  function filterFoo(sfEvent $event, $foo)
  {
    return "*$foo*";
  }

  function filterFooBis(sfEvent $event, $foo)
  {
    return "-$foo-";
  }

  function listenToFoo(sfEvent $event)
  {
    $this->value .= 'listenToFoo';
  }

  function listenToFooBis(sfEvent $event)
  {
    $this->value .= 'listenToFooBis';

    return true;
  }

  function getValue()
  {
    return $this->value;
  }

  function reset()
  {
    $this->value = '';
  }
}
