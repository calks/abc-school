<?php // $Id: pagenavigation.inc.php,v 1.1 2010/11/11 09:51:41 nastya Exp $

$INC_pagenavigation = true;

// ===========================================================================================
//                                                                                          //
//  TPageNavigation library                                                                 //
//  Writen by Maxim Makarenko                                                               //
//  (C) Olmisoft Inc                                                                        //
//                                                                                          //
// ===========================================================================================

/*

class TPageNavigation {
  var $PageDelta = 3;
  var $CurrentPage;
  var $LastPage;
  var $Link;
  var $PageSize;

  constructor TPageNavigation($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage);
  void Display(void);
  function getOffset();
  function getPageSize();
  function getCurrentPage();
}
*/

////////////////////////////////////////////////
//  TPageNavigation class
////////////////////////////////////////////////

class TPageNavigation {
  var $PageDelta = 9;     // How much pages are visible. MUST be odd (3, 5, 7, 9, ...)
  var $CurrentPage;       // Current page
  var $LastPage;          // Last page in the set
  var $Link;              // File name with parameters for links (without "page=" part)
  var $PageSize;          // page size - number of objects per page
  var $parameterName = 'page';
  var $pageSeparator = '&nbsp;&nbsp;';
  var $displayFirstAndPrev = TRUE;
  var $displayLastAndNext = TRUE;

  //====================================================
  // Constructor
  function TPageNavigation($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage) {
    // page size
    if (is_numeric($aItemsPerPage)) {
      $this->PageSize = max($aItemsPerPage, 1);
    }
    else {
      $this->PageSize = 20;
    }
    // Current page
    if ($aCurrentPage > 0) {
      $this->CurrentPage = round($aCurrentPage);
    }
    else {
      $this->CurrentPage = 1;
    }

    $this->Link = $aLink;
    if (strstr($this->Link, "?")) $this->Link .= "&";
    else $this->Link .= "?";

    // Last page
    if ($aQtyOfObjects > 0) {
      $this->LastPage = ceil($aQtyOfObjects / $this->PageSize);
      if ($this->LastPage < 1) $this->LastPage = 1;
      if ($this->CurrentPage > $this->LastPage) {
        $this->CurrentPage = $this->LastPage;
      }
    }
    else {
      $this->LastPage = $this->CurrentPage;
    }
  }

  //====================================================
  // DisplayPageNavigation
  function DisplayPageNavigation() {
    // TODO ������� ����� ���� getCallerLocation()
    $stack = debug_backtrace();
    if (array_key_exists(0, $stack)) {
      $entry = $stack[0];
      $location = $entry['file'].':'.$entry['line'];
    }
    else {
      $location = NULL;
    }
    // TODO 'at' ������ ������ ���� ���������� $location
    user_error('TPageNavigation::DisplayPageNavigation is deprecated at '.$location);
    $this->Display();
  }

  /**
   * Returns URL to the specified page.
   * @param int $pageNumber
   * @return string
   */
  function getPageUrl($pageNumber) {
    return $this->Link.$this->parameterName.'='.$pageNumber;
  }

  //====================================================
  // Display
  function Display() {

    if (!$this->isVisible()) return;

    $PageOffset = round(($this->PageDelta - 1) / 2);   // Distance for the border

    // Max & Min page
    if ($this->CurrentPage > $PageOffset + 1) {
      $MinPage = $this->CurrentPage - $PageOffset;
    }
    else {
      $MinPage = 1;
    }

    $MaxPage = $MinPage + $this->PageDelta - 1;
    if ($MaxPage > $this->LastPage) {
      $MaxPage = $this->LastPage;
      $MinPage = $this->LastPage - $this->PageDelta + 1;
    }
    if ($MinPage < 1) {
      $MinPage = 1;
    }

    if ($this->CurrentPage != 1 && $this->displayFirstAndPrev) {
      $this->printPageLink(1, '&lt;&lt;');
      print $this->pageSeparator;
      $this->printPageLink($this->CurrentPage - 1, '&lt;');
      print $this->pageSeparator;
    }

    for ($i=$MinPage; $i < $this->CurrentPage; $i++) {
      if ($i != $MinPage) {
        print $this->pageSeparator;
      }
      $this->printPageLink($i, $i);
    }
    $this->printCurrentPage();
    for ($i=$this->CurrentPage+1; $i<=$MaxPage; $i++) {
      if ($i != $this->CurrentPage + 1) {
        print $this->pageSeparator;
      }
      $this->printPageLink($i, $i);
    }
    if ($this->CurrentPage != $this->LastPage && $this->displayLastAndNext) {
      print $this->pageSeparator;
      $this->printPageLink($this->CurrentPage + 1, '&gt;');
      print $this->pageSeparator;
      $this->printPageLink($this->LastPage, '&gt;&gt;');
    }
  }

  function isVisible() {
    return $this->LastPage > 1;
  }

  function printCurrentPage() {
    if ($this->CurrentPage != 1) {
      print $this->pageSeparator;
    }
    print('<b>'.$this->CurrentPage.'</b>');
    if ($this->CurrentPage != $this->LastPage) {
      print $this->pageSeparator;
    }
  }

  function printPageLink($pageNumber, $linkText) {
    print '<a href="'.$this->getPageUrl($pageNumber).'">'.$linkText.'</a>';
  }

  function getOffset() {
    return ($this->CurrentPage - 1) * $this->PageSize;
  }

  function getPageSize() {
    return $this->PageSize;
  }

  function getCurrentPage() {
    return $this->CurrentPage;
  }

  function getParameterName() {
    return $parameterName;
  }

  function setParameterName($parameterName) {
    $this->parameterName = $parameterName;
  }

}

