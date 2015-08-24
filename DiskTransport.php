<?php

/**
 * @author <Maris "waplet" Jankovskis>
 */

class Swift_DiskTransport extends Swift_Transport_DiskTransport {

  public function __construct()
  {
    call_user_func_array(
      [$this, 'Swift_Transport_DiskTransport::__construct'],
      Swift_DependencyContainer::getInstance()
          ->createDependenciesFor('transport.disk')
    );

    $this->setPath('/var/mail/mailtodisk/');
  }

  public static function newInstance()
  {
    return new self();
  }
}