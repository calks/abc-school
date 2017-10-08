<?

    Application::loadLibrary('olmi/dateTimeFormatInfo');

    define('CALENDAR_TABLE_CLASS', "calendar.table.class");
    define('CALENDAR_DATETIME_FORMATINFO', "calendar.datetime.formatInfo");

    class CalendarContext {

      /**
       * @access private
       */
      var $attributes = array();

      function CalendarContext() {
        $this->attributes[CALENDAR_TABLE_CLASS] = "calendar";
        $this->attributes[CALENDAR_DATETIME_FORMATINFO] = EnglishDateTimeFormatInfo::getInstance();
      }

      /**
       * Checks if specified attribute exists.
       * @param string $name
       * @return bool
       */
      function hasAttribute($name) {
        return array_key_exists($name, $this->attributes);
      }

      /**
       * Sets attribute value specifing name and value.
       * @param string $name
       * @param mixed $value
       */
      function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
      }

      /**
       * Removes speciifed attribute.
       * @param string $name
       */
      function removeAttribute($name) {
        unset($this->attributes[$name]);
      }

      /**
       * Returns attribute value with the given name.
       * @param string $name
       * @return mixed
       */
      function getAttribute($name, $defaultValue = NULL) {
        if (array_key_exists($name, $this->attributes)) {
          return $this->attributes[$name];
        }
        else {
          return $defaultValue;
        }
      }

    }

    class Calendar {

      function getDefaultContext() {
        static $defaultContext = NULL;
        if ($defaultContext == NULL) {
          $defaultContext = new CalendarContext();
        }
        return $defaultContext;
      }

      function render($month, $year, $selectedDate, $context = null) {
        if ($context == null) {
          $context = $this->getDefaultContext();
        }
        $tableClass = $context->getAttribute(CALENDAR_TABLE_CLASS);
        $c = "<table class=\"".$tableClass."\" border=\"1\">\n";
        $c .= $this->getHeadlineRow($month, $year, $context);
        $c .= $this->getWeekDaysRow($context);
        $daysCount = DateUtils::FormatDate("t", $year."-".$month."-01");
        $firstDayWeekDay = DateUtils::FormatDate("w", $year."-".$month."-01");
        $lastDayWeekDay = DateUtils::FormatDate("w", $year."-".$month."-".$daysCount);
        $c .= $this->getBeginEmptyCells($firstDayWeekDay, $context);
        $c .= $this->getDayRows($daysCount, $year, $month, $selectedDate, $context);
        $c .= $this->getEndEmptyCells($lastDayWeekDay, $context);
        $c .= "</table>\n";
        return $c;
      }

      function getHeadLineRow($month, $year, $context) {
        $header = $this->getHeadlineText($month, $year, $context);
        if (!is_null($header) && $header != '') {
          return "<tr><th class=\"headline\" colspan=\"7\">".$header."</th></tr>\n";
        }
        else {
          return "";
        }
      }

      function getHeadlineText($month, $year, $context) {
        $dateTimeFormatInfo = $context->getAttribute(CALENDAR_DATETIME_FORMATINFO);
        if (!is_null($dateTimeFormatInfo)) {
          $monthNames = $dateTimeFormatInfo->getMonthNames();
          return $monthNames[intval($month)].", ".$year;
        }
        else {
          return $year.'/'.$month;
        }
      }

      function getWeekDaysRow($context) {
        $days = $this->getWeekDays($context);
        if (is_array($days)) {
          $res  = "<tr>";
          $dayBeginWeek = $this->getDayBeginWeek($context);
          for ($i=$dayBeginWeek; $i<$dayBeginWeek+7; $i++) {
            $res .= "<th>".$days[$i%7]."</th>";
          }
          $res .= "</tr>\n";
          return $res;
        }
        else {
          return "";
        }
      }

      function getDayBeginWeek($context) {   //  0=sunday, 1=monday, etc...
        $dateTimeFormatInfo = $context->getAttribute(CALENDAR_DATETIME_FORMATINFO);
        if (!is_null($dateTimeFormatInfo)) {
          return $dateTimeFormatInfo->getFirstWeekDay();
        }
        else {
          return 1;
        }
      }

      function getWeekDays($context) {
        $dateTimeFormatInfo = $context->getAttribute(CALENDAR_DATETIME_FORMATINFO);
        if (!is_null($dateTimeFormatInfo)) {
          return $dateTimeFormatInfo->getAbbreviatedDayNames();
        }
        else {
          return NULL;
        }
      }

      function getDayRows($daysCount, $year, $month, $selectedDate, $context) {
        $res = "";
        $dayBeginWeek = $this->getDayBeginWeek($context);
        for ($i=1; $i<=$daysCount; $i++) {
          //TODO для получения дня недели можно просто увеличивать значение полученное в начале.
          $date = $this->getDateValue($year, $month, $i);
          $dayOfWeek = DateUtils::FormatDate("w", $date);
          if ($dayOfWeek % 7 == $dayBeginWeek) {  // if begin of the week
            $res .= "<tr>";
          }
          $res .= $this->getDayCell($date, $dayOfWeek, $selectedDate, $i, $context);
          if (($dayOfWeek+1) % 7 == $dayBeginWeek) {  // if end of the week
            $res .= "</tr>\n";
          }
        }
        return $res;
      }

      function getDateValue($year, $month, $day) {
        return $year."-".sprintf("%02u", $month)."-".sprintf("%02u", $day);
      }

      function getDayCell($date, $dayOfWeek, $selectedDate, $dayNumber, $context) {
        if ($date == $selectedDate) {
          $tdClass = ' class="selected"';
        }
        elseif ($date == DateUtils::getCurrentDate()) {
          $tdClass = ' class="today"';
        }
        elseif ($dayOfWeek % 7 == 0 || $dayOfWeek % 7 == 6) {
          $tdClass = ' class="weekend"';
        }
        else {
          $tdClass = '';
        }
        return '<td'.$tdClass.'>'.$this->getCellValue($date, $dayNumber, $context).'</td>';
      }

      function getCellValue($date, $dayNumber, $context) {
        return $dayNumber;
      }

      function getBeginEmptyCells($firstDayWeekDay, $context) {
        $res = "";
        $dayBeginWeek = $this->getDayBeginWeek($context);
        $beginEmptyCellsCount = (7 - $dayBeginWeek + $firstDayWeekDay) % 7;
        //TODO постараться написать проще.
        if ($beginEmptyCellsCount) {
          if ($dayBeginWeek) {
            $res .= "<tr>".str_repeat("<td>&nbsp;</td>", min($beginEmptyCellsCount, 5));
            $res .= str_repeat("<td class=\"weekend\">&nbsp;</td>", max($beginEmptyCellsCount-5, 0));
          }
          else {
            $res .= "<tr><td class=\"weekend\">&nbsp;</td>";
            $res .= str_repeat("<td>&nbsp;</td>", min($beginEmptyCellsCount-1, 5));
          }
        }
        return $res;
      }

      function getEndEmptyCells($lastDayWeekDay, $context) {
        $res = "";
        $dayBeginWeek = $this->getDayBeginWeek($context);
        $endEmptyCellsCount = (6 + $dayBeginWeek - $lastDayWeekDay)%7;
        if ($endEmptyCellsCount) {
          if ($dayBeginWeek) {
            $res .= str_repeat("<td>&nbsp;</td>", max($endEmptyCellsCount-2, 0));
            $res .= str_repeat("<td class=\"weekend\">&nbsp;</td>", min($endEmptyCellsCount, 2));
          }
          else {
            $res .= str_repeat("<td>&nbsp;</td>", $endEmptyCellsCount-1)."<td class=\"weekend\">&nbsp;</td>";
          }
          $res .= "</tr>\n";
        }
        return $res;
      }
    }

    class CalendarUrl extends Calendar {

      var $url;
      var $dateParamName;

      function CalendarUrl($url = null, $dateParamName = null) {
        $this->url = $url;
        $this->dateParamName = $dateParamName;
      }

      function getDateParamName() {
        return $this->dateParamName;
      }

      function getCellValue($date, $dayNumber, $context) {
        return $this->getDateLink($date, $context, $dayNumber);
      }

      function getDateLink($date, $context, $text, $titleText = "") {
        if ($this->url && $this->dateParamName) {
          $title = $titleText ? " title=\"".$titleText."\"" : "";
          $url = $this->url;
          $url->setParam($this->dateParamName, $date);
          return "<a href=\"".$url->toString()."\"".$title.">".$text."</a>";
        }
        else {
          return $text;
        }
      }

      function getHeadLineRow($month, $year, $context) {
        //TODO сделать define-ы, причем два отдельных.
        if ($context->getAttribute("prevNext")) {
          list($prev,$next) = $this->getPrevNext($month, $year);
          $res  = "<tr><th>".$this->getDateLink($prev, $context, "&lt;&lt;", "prev")."</th>";
          $res .= "<th class=\"headline\" colspan=\"5\">".$this->getHeadlineText($month, $year, $context)."</th>";
          $res .= "<th>".$this->getDateLink($next, $context, "&gt;&gt;", "next")."</th></tr>";
          return $res;
        }
        else {
          return Calendar::getHeadLineRow($month, $year, $context);
        }
      }

      function getPrevNext($month, $year) {
        $d = getDate(strtotime($year."-".$month."-01"));
        $p = getDate(strtotime(date("Y-m-d", mktime(1,1,1,$d["mon"]-1,1,$d["year"]))));
        $prev = $this->getDateValue($p["year"], $p["mon"], 1);
        $n = getDate(strtotime(date("Y-m-d", mktime(1,1,1,$d["mon"]+1,1,$d["year"]))));
        $next = $this->getDateValue($n["year"], $n["mon"], 1);
        return array($prev, $next);
      }
    }


