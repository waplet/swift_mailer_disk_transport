<?php
/**
 * Pretends messages have been sent, but just ignores them.
 *
 * @author <Maris "waplet" Jankovskis>
 */

class Swift_Transport_DiskTransport implements Swift_Transport
{
  private $_eventDispatcher;
  private $_path;

  public function __construct(Swift_Events_EventDispatcher $eventDispatcher)
  {
    $this->_eventDispatcher = $eventDispatcher;
  }

  public function setPath($path)
  {
    $this->_path = $path;
  }
  public function isStarted()
  {
    return true;
  }

  public function start()
  {
  }

  public function stop()
  {
  }

  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {

    if(empty($this->_path))
      throw new Swift_TransportException(
        'Please specify path'
      );

    $fileName = date('YmdHis') . '_' . uniqid() . '.html';

    $failedRecipients = (array) $failedRecipients;

    if ($evt = $this->_eventDispatcher->createSendEvent($this, $message)) {
        $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
        if ($evt->bubbleCancelled()) {
            return 0;
        }
    }

    $count = (
        count((array) $message->getTo())
        + count((array) $message->getCc())
        + count((array) $message->getBcc())
        );

    $toHeader = $message->getHeaders()->get('To');

    if (!$toHeader)
      throw new Swift_TransportException(
        'Cannot send message without a recipient'
      );

    // now starts the saving part
    $fp = fopen($this->_path . $fileName, 'w');
    if($fp === false)
      throw new Swift_TransportException('Could not open file handle');

    foreach($message->getTo() as $email => $name)
    {
      fwrite($fp, 'To: ' . '(' . $name . ') ' . $email . "\n");
    }
    fwrite($fp, 'Subject: ' . $message->getSubject() . "\n");
    fwrite($fp, "Content: \n" . $message->getBody() . "\n");
    fwrite($fp, "Headers: " . $message->getHeaders()->toString() . "\n");

    fclose($fp);

    if($evt)
    {
      $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
      $evt->setFailedRecipients($failedRecipients);
      $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
    }

    return $count;
  }

  public function registerPlugin(Swift_Events_EventListener $plugin)
  {
    $this->_eventDispatcher->bindEventListener($plugin);
  }
}
