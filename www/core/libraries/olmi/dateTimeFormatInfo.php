<?php // $Id: dateTimeFormatInfo.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $

class DateTimeFormatInfo {
  function getMonthNames() {
    die(get_class($this)."::getMonthNames() not implemented");
  }

  function getWeekDayNames() {
    die(get_class($this)."::getWeekDayNames() not implemented");
  }

  function getAbbreviatedDayNames() {
    die(get_class($this)."::getAbbreviatedDayNames() not implemented");
  }

  function getAbbreviatedMonthNames() {
    die(get_class($this)."::getAbbreviatedMonthNames() not implemented");
  }

  function getFirstWeekDay() {
    die(get_class($this)."::getFirstWeekDay() not implemented");
  }

}

class EnglishDateTimeFormatInfo extends DateTimeFormatInfo {

  function getInstance() {
    static $instance = NULL;
    if (is_null($instance)) {
      $instance = new EnglishDateTimeFormatInfo();
    }
    return $instance;
  }

  function getMonthNames() {
    return array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
  }

  function getWeekDayNames() {
    return array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
  }

  function getAbbreviatedDayNames() {
    return array("sun", "mon", "tue", "wed", "thu", "fri", "sat");
  }

  function getAbbreviatedMonthNames() {
    return array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
  }

  function getFirstWeekDay() {
    return 0;
  }
}

class RussianDateTimeFormatInfo extends DateTimeFormatInfo {

  function getInstance() {
    static $instance = NULL;
    if (is_null($instance)) {
      $instance = new RussianDateTimeFormatInfo();
    }
    return $instance;
  }

  function getMonthNames($grammaticalCase = "n") {
    switch ($grammaticalCase) {
      case "n" :
        return array(1 => "������", 2 => "�������", 3 => "����", 4 => "������", 5 => "���", 6 => "����", 7 => "����", 8 => "������", 9 => "��������", 10 => "�������", 11 => "������", 12 => "�������");
      case "g" :
        return array(1 => "������", 2 => "�������", 3 => "�����", 4 => "������", 5 => "���", 6 => "����", 7 => "����", 8 => "�������", 9 => "��������", 10 => "�������", 11 => "������", 12 => "�������");
    }
  }

  function getWeekDayNames() {
    return array("�����������", "�����������", "�������", "�����", "�������", "�������", "�������");
  }

  function getAbbreviatedDayNames() {
    return array("��", "��", "��", "��", "��", "��", "��");
  }

  function getAbbreviatedMonthNames() {
    return array(1 => "���", 2 => "���", 3 => "���", 4 => "���", 5 => "���", 6 => "���", 7 => "���", 8 => "���", 9 => "���", 12 => "���", 11 => "���", 12 => "���");
  }

  function getFirstWeekDay() {
    return 1;
  }
}

?>
