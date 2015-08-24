Installation
============


1. Add dependencies at transport_deps.php
-----------------------------------------
```php
->register('transport.disk')
->asNewInstanceOf('Swift_Transport_DiskTransport')
->withDependencies(['transport.eventdispatcher'])
```

2. Edit necessary path for saved messages
-----------------------------------------

At Swift_DiskTransport::__construct() method.
```php
$this->setPath('...');
```

3. Feel free to create saved message as you want
------------------------------------------------

At Swift_Transport_DiskTransport::send() method.
