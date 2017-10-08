<?php // $Id: MailSender.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $

define('MAIL_PRIORITY_HIGHEST', 1);
define('MAIL_PRIORITY_HIGH', 2);
define('MAIL_PRIORITY_NORMAL', 3);
define('MAIL_PRIORITY_LOW', 4);
define('MAIL_PRIORITY_LOWEST', 5);

class MailSender {

  /**
   * @constructor
   * @param any $params a������������ ������ � �����������, ������� ��������
   * ����� �������������� � ��������� ������.
   */
  function MailSender($params = NULL) {
  }

  /**
   * ������� ������ ������, ������� ����� ���������� ������� ����� ���������.
   * @param any $params ������������� ������ �������� ��� ������
   * @return MailMessage
   */
  function createMessage($params = NULL) {
    return new MailMessage();
  }

  /**
   * ���������� ��������� ������ ������.
   * @param MailMessage $msg
   * @return bool
   */
  function send($msg, $additional_parameters = NULL) {
    if (count($msg->attachments) == 0) {
      if (!is_object($msg->body)) {
        $msg->setBody('');
      }
    }
    if (is_null($additional_parameters)) {
        return @mail($msg->getAddressString("to", $msg->isWindows()),
                    $msg->getSubject(),
                    $msg->prepareBody(),
                    $msg->buildHeaders());
    }
    else {
        return @mail($msg->getAddressString("to", $msg->isWindows()),
                    $msg->getSubject(),
                    $msg->prepareBody(),
                    $msg->buildHeaders(),
                    $additional_parameters);
    }
  }

  /**
   * ��������������� ������� ��� �������� ������� ���������.
   * TODO ��� ������ ����� ����� ��� ����������� mail() ������ �� ���� ?
   * @param string $from
   * @param any $to
   * @param string $subject
   * @param string $body [TODO or any]
   * @param string $contentType
   */
  function sendMessage($from, $to, $subject, $body, $contentType = "text/plain") {
  }

}

/**
 * class for e-mail address representation.
 */
class MailAddress {

  /**
   * e-mail address
   * @access private
   */
  var $address;

  /**
   * e-mail address owner's name
   * @access private
   */
  var $name;

  /**
   * @constructor
   * @param string $address
   * @param string $name
   */
  function MailAddress($address, $name) {
    $this->address = $address;
    $this->name    = $name;
  }

  /**
   * Returns e-mail address
   * @return string
   */
  function getAddress() {
    return $this->address;
  }

  /**
   * Returns e-mail address owner's name
   * @return string
   */
  function getName() {
    return $this->name;
  }

  /**
   * Returns e-mail address in format user_name <e_mail_address> if name isn't null,
   * otherwise - getAddress() function is called
   * @return string
   */
  function toString() {
    return $this->getName() ? $this->getName()." <".$this->getAddress().">" : $this->getAddress();
  }

}

/**
 * attachment.
 */
class MailAttachment {

  /**
   * Content-Type
   * @access private
   */
  var $contentType;

  /**
   * name of attachment
   * @access private
   */
  var $name;

  /**
   * content
   * @access private
   */
  var $content;

  /**
   * @constructor
   * @param string $contentType
   * @param string $name
   * @param string $content
   */
  function MailAttachment($contentType, $name, $content) {
    $this->contentType = $contentType;
    $this->name        = $name;
    $this->content     = $content;
  }

  function buildHeaders() {
    $res  = "Content-Type: ".$this->contentType."; name=\"".$this->name."\"\n";
    $res .= "Content-Transfer-Encoding: base64\n";
    $res .= "Content-Disposition: attachment; filename=\"".$this->name."\"\n";
    return $res;
  }

  /**
   * Returns attachment for e-mail
   * @return string
   */
  function buildPart() {
    return $this->buildHeaders()."\n".chunk_split(base64_encode($this->content), 72);
  }
}

class MessageBody {

  /**
   * Content-Type
   * @access private
   */
  var $contentType;

  /**
   * charset
   * @access private
   */
  var $charset;

  /**
   * encoding 7/8 bit
   * @access private
   */
  var $encoding;

  /**
   * text
   * @access private
   */
  var $text;

  /**
   * @constructor
   * @param string $text
   * @param string $contentType
   * @param string $charset
   * @param string $encoding
   */
  function MessageBody($text, $contentType, $charset, $encoding) {
    $this->text        = $text;
    $this->contentType = $contentType;
    $this->charset     = $charset;
    $this->encoding    = $encoding;
  }

  function buildHeaders() {
    $res  = "Content-Type: ".$this->contentType."; charset=\"".$this->charset."\"\n";
    $res .= "Content-Transfer-Encoding: ".$this->encoding."\n";
    return $res;
  }

  /**
   * Returns body as a string
   * @return string
   */
  function buildPart() {
    return $this->buildHeaders()."\n".$this->text."\n\n";
  }

}

/**
 * ������ ���������� ��������.
 */
class MailMessage {

  /**
   * The value of 'From' address
   * @access private
   */
  var $from = null;

  /**
   * The value of 'Reply-To' address
   * @access private
   */
  var $replyTo = null;

  /**
   * associative array of recipient addresses. The keys are 'to', 'cc', 'bcc'.
   * The values are arrays of MailAddress objects.
   * @access private
   */
  var $recipients = array();

  /**
   * boundary of message
   * @access private
   */
  var $boundary;

  /**
   * subject of message
   * @access private
   */
  var $subject;

  /**
   * body of message
   * @access private
   */
  var $body;

  /**
   * reading confirmation
   * @access private
   */
  var $readConfirm;

  /**
   * array of attachments
   * @access private
   */
  var $attachments = array();

  /**
   * mail priority
   */
  var $priority = null;

  /**
   * @constructor
   * @param string $contentType
   * @param string $name
   * @param string $content
   */

  function MailMessage() {
    $this->boundary = "bound".md5(uniqid(time()));
  }

  /**
   * Sets the address to the 'From' field
   * @param string $address
   * @param string $name
   */
  function setFrom($address, $name = "") {
    $this->from = new MailAddress($address, $name);
  }

  /**
   * Sets the address to the 'Reply-To' field
   * @param string $address
   * @param string $name
   */
  function setReplyTo($address, $name = "") {
    $this->replyTo = new MailAddress($address, $name);
  }

  /**
   * Adds address to the 'to' field
   * @param string $address
   * @param string $name
   * @return bool
   */
  function addTo($address, $name = "") {
    return $this->addAddress("to", $address, $name);
  }

  /**
   * Adds address to the 'cc' field
   * @param string $address
   * @param string $name
   * @return bool
   */
  function addCc($address, $name = "") {
    return $this->addAddress("cc", $address, $name);
  }

  /**
   * Adds address to the 'bcc' field
   * @param string $address
   * @param string $name
   * @return bool
   */
  function addBcc($address, $name = "") {
    return $this->addAddress("bcc", $address, $name);
  }

  /**
   * TODO docs
   * adds attachment
   * @return void
   */
  function addAttachment($contentType, $name, $content) {
    $this->attachments[] = new MailAttachment($contentType, $name, $content);
  }

  /**
   * TODO docs
   * @param string $physicalName
   * @param string $contentType
   * @param string $logicalName
   */
  function addFile($physicalName, $logicalName = NULL, $contentType = NULL) {
    if (file_exists($physicalName)) {
      $fp = fopen($physicalName, "rb");
      $content = fread($fp, filesize($physicalName));
      fclose($fp);
      if (is_null($contentType) && function_exists("mime_content_type")) {
        $contentType = mime_content_type($physicalName);
      }
      if (is_null($logicalName)) {
        $logicalName = basename($physicalName);
      }
      $this->addAttachment($contentType, $logicalName, $content);
    }
    else {
      //Debug::dump('No file "'.$physicalName.'"');
    }
  }

  /**
   * ��������� ������������ ������. �������� � ������� 'CC', 'BCC' ������ �
   * ��������������� ���������, ��������� ������ ������ � 'To'
   * @param any $recipients
   */
  function add($recipients) {
  }

  /**
   * Adds address to the $fieldName field
   * @param string $fieldName
   * @param string $address
   * @param string $name
   * @return bool
   * @access private
   */
  function addAddress($fieldName, $address, $name) {
    if (!StringUtils::isValidEmail($address)) {
      return false;
    }
    if (!is_array($this->recipients)) {
      $this->recipients = array();
    }
    if (array_key_exists($fieldName, $this->recipients)) {
      $this->recipients[$fieldName][] = new MailAddress($address, $name);
    }
    else {
      $this->recipients[$fieldName] = array(new MailAddress($address, $name));
    }
    return true;
  }

  /**
   * Returns string of adresses for field $fieldName
   * @param string $fieldName
   * @return string
   * @access private
   */
  function getAddressString($fieldName, $onlyAddresses = false) {
    if (array_key_exists($fieldName, $this->recipients)) {
      $res = "";
      $values = $this->recipients[$fieldName];
      if (is_array($values)) {
        foreach ($values as $addr) {
          $res .= ($res ? "," : "").($onlyAddresses ? $addr->getAddress() : $addr->toString());
        }
      }
      else {
        $res = $onlyAddresses ? $addr->getAddress() : $addr->toString();
      }
      return $res;
    }
    else {
      return NULL;
    }
  }

  /**
   * Sets subject of message
   * @param string $subject
   * @return void
   */
  function setSubject($subject) {
    $this->subject = $subject;
  }

  /**
   * Returns subject of message
   * @return string
   */
  function getSubject() {
    return $this->subject;
  }

  function isWindows() {
    return DIRECTORY_SEPARATOR == '\\';
  }

  function generateMessageId() {
    return "<" . md5(uniqid(rand(), true)) . '.' . time() . '.' . $this->from->toString() . ">";
  }

  /**
   * Returns header of message
   * @param bool $readConfirm
   * @return string
   * @access private
   */
  function buildHeaders() {
    $res  = "Date: ".date("r",time())."\n";
    $res .= "Message-ID: ".$this->generateMessageId()."\n";
    if (!is_null($this->from)) {
      $res .= "From: ".$this->from->toString()."\n";
      if ($this->getReadConfirm()) {
        $res .=  "X-Confirm-Reading-To: ".$this->from->getAddress(). "\n";
        $res .=  "Disposition-Notification-To: ".$this->from->getAddress()."\n";
      }
    }
    if (!is_null($this->replyTo)) {
      $res .= "Reply-To: ".$this->replyTo->toString()."\n";
    }
    if ($this->isWindows()) {
      if (array_key_exists('to', $this->recipients)) {
        $res .= "To:".$this->getAddressString("to", false)."\n";
      }
    }
    if (array_key_exists('cc', $this->recipients)) {
      $res .= "Cc:".$this->getAddressString("cc", false)."\n";
    }
    if (array_key_exists('bcc', $this->recipients)) {
      $res .= "Bcc:".$this->getAddressString("bcc", false)."\n";
    }
    $res .= "MIME-Version: 1.0\n";

    if ($this->priority) {
      $priorities = array(MAIL_PRIORITY_HIGHEST=>"Highest", MAIL_PRIORITY_HIGH=>"High", MAIL_PRIORITY_NORMAL=>"Normal", MAIL_PRIORITY_LOW=>"Low", MAIL_PRIORITY_LOWEST=>"Lowest");
      $res .= "X-Priority: ".$this->priority."\n";
      $res .= "X-MSMail-Priority: ".$priorities[$this->priority]."\n";
    }

    if (count($this->attachments)) {
      $res .= "Content-Type: multipart/mixed; boundary=\"".$this->boundary."\"\n";
    }
    else {
      $res .= $this->body->buildHeaders();
    }
    return $res;
  }

  /**
   * Sets body text of message
   * @param string $bodyText
   * @param string $contentType
   * @param string $charset
   * @return void
   */
  function setBody($bodyText, $contentType = "text/plain", $charset = "iso-8859-1", $encoding = "7bit") {
    $this->body = new MessageBody($bodyText, $contentType, $charset, $encoding);
  }

  /**
   * Returns body of message
   * @return string
   * @access private
   */
  function prepareBody() {
    if (count($this->attachments)) {
      $res  = "This is a MIME encoded message\n\n";
      if (is_object($this->body)) {
        $res .= "--".$this->boundary."\n";
        $res .= $this->body->buildPart();
      }
      foreach ($this->attachments as $attachment) {
        $res .= "--".$this->boundary."\n";
        $res .= $attachment->buildPart();
      }
      $res .= "--".$this->boundary."--\n";
      return $res;
    }
    else {
      return $this->body->text;
    }
  }

  /**
   * Sets option for reading confirmation
   * @param bool $readConfirm
   * @return void
   */
  function setReadConfirm($readConfirm) {
    $this->readConfirm = $readConfirm;
  }

  /**
   * returns option for reading confirmation
   * @return bool
   */
  function getReadConfirm() {
    return isset($this->readConfirm) && $this->readConfirm;
  }


  /**
   * Removes all recipients the the message.
   * @return void
   */
  function removeRecipients() {
    $this->recipients = array();
  }

  /**
   * Sets the priority of the message
   * @param int $priority use one of constants MAIL_PRIORITY_* defined in this file
   */
  function setPriority($priority) {
    if (intval($priority)) {
      $this->priority = $priority;
    }
    else {
      trigger_error("Not integer value passed to setPriority()", E_USER_WARNING);
    }
  }

}

?>
