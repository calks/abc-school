<?php // $Id: dateUtils.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $

define('DATEUTILS_SECONDS_PER_DAY', 24 * 60 * 60);

class DateUtils {

  /**
   * Checks if a string argument matches the date format YYYY-MM-DD or YY-MM-DD.
   * @param string $date
   * @return boolean
   * @static
   */
  function isValidDate($date) {
    return preg_match("/^([0-9]{2}|[0-9]{4})-[0-1]?[0-9]-([0-2]?[0-9]|3[0-1])\$/sD", $date);
  }

  /**
   * Formats the date from the "yyyy-mm-dd" form to the specified format.
   * @param string $format the argument for date() function
   * @param string $date
   * @return string
   * @static
   */
  function FormatDate($format, $date) {
    if (!isset($date) || $date == "" || !isset($format) || $format == "") {
      return NULL;
    }
    if (!DateUtils::isValidDate($date)) {
      return NULL;
    }

    $arr = explode("-", $date);

    if ($arr[0] < 1970 || $arr[0] > 2038) {
      $res = $date;
    }
    else{
      $stamp = mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
      $res = date($format, $stamp);
    }

    return $res;
  }

  function ToUSDate($date) {
    if (!isset($date) || $date == "") return false;
    if (!DateUtils::isValidDate($date)) {
      return NULL;
    }

    $arr = explode("-", $date);

    return sprintf("%s %02u, %u", date("F", mktime(0, 0, 0, $arr[1], 1, 2000)), $arr[2], $arr[0]);
  }

  /**
   * Returns current date and time in ISO (YYYY-MM-DD HH:NN:SS) format
   * @return string
   * @static
   */
  function getCurrentDateTime() {
    return date("Y-m-d H:i:s");
  }

  /**
   * Returns current date in ISO (YYYY-MM-DD) format
   * @return string
   * @static
   */
  function getCurrentDate() {
    return date("Y-m-d");
  }

  /**
   * Returns current year in (YYYY) format
   * @return string
   * @static
   */
  function getCurrentYear() {
    return date("Y");
  }

  /**
   * Returns current month in (MM) format
   * @return string
   * @static
   */
  function getCurrentMonth() {
    return date("m");
  }

  /**
   * Returns current day in (DD) format
   * @return string
   * @static
   */
  function getCurrentDay() {
    return date("d");
  }

  /**
   * Returns current time in ISO (HH:NN:SS) format
   * @return string
   * @static
   */
  function getCurrentTime() {
    return date("H:i:s");
  }

  /**
   * Returns current date and time in (YYYYMMDDHHNNSS) format
   * @return string
   * @static
   */
  function getCurrentTimestamp() {
    return date("YmdHis");
  }

  /**
   * Converts YYYYMMDDHHNNSS to number of seconds since epoch.
   * @param string $timestamp
   * @return int
   * @static
   */
  function parseTimestamp($timestamp) {
    if (strlen($timestamp) == 14) {
      return strtotime(substr($timestamp,0,4).'-'.substr($timestamp,4,2).'-'.substr($timestamp,6,2).' '.substr($timestamp,8,2).':'.substr($timestamp,10,2).':'.substr($timestamp,12,2));
    }
    else {
      return strtotime($timestamp);
    }
  }

  /**
   * Formats number of seconds since epoch to the HTTP date format.
   * @param int $value
   * @return string
   * @static
   */
  function formatHttpDate($value) {
    return gmdate('D, d M Y H:i:s', $value).' GMT';
  }

  /**
   * Increment year and month by the specified number of months,
   * returns array(year,month)
   * @param int $year
   * @param int $month
   * @param int $increment
   * @return array
   * @static
   */
  function incrementMonth($year, $month, $increment) {
    $sign = $increment >= 0 ? 1 : -1;
    $year += intval($increment / 12);
    $increment %= 12;
    $month += $increment;
    if ($month <= 0 || $month > 12) {
      $year += $sign;
      $month -= 12 * $sign;
    }
    return array($year, $month);
  }

  /**
   * Extracts hours and minutes from full datetime string (YYYY-MM-DD HH:NN:SS)
   * @param string $datetime
   * @return string
   * @static
   */
  function extractShortTime($datetime) {
    if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $datetime)) {
      return substr($datetime, 11, 5);
    }
    else {
      return $datetime;
    }
  }

  /**
   * Calculates difference and formats it as time.
   * @param string $startDateTime
   * @param string $endDateTime
   * @return string
   * @static
   */
  function getTimeDifference($startDateTime, $endDateTime) {
    $seconds = strtotime($endDateTime) - strtotime($startDateTime);
    $minutes = $seconds / 60;
    if ($minutes >= 0) {
      $prefix = '';
    }
    else {
      $prefix = '-';
      $minutes = -$minutes;
    }
    return $prefix.intval($minutes / 60).":".sprintf("%02u", $minutes % 60);
  }
}

?>
