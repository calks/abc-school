<?php

    Application::loadLibrary('olmi/pagenavigation');

    class PageNav extends TPageNavigation {
        function PageNav($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage) {
            TPageNavigation::TPageNavigation($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage);
            $this->pageSeparator = '&nbsp;';
        }

        function printPageLink($pageNumber, $linkText) {
            print '<a href="#" onclick="page(\''.$pageNumber.'\');return false;">'.$linkText.'</a>';
        }

        function printCurrentPage() {
            if ($this->CurrentPage != 1) {
                print $this->pageSeparator;
                print "<strong>{$this->CurrentPage}</strong>";
            } else {
                print "<strong>{$this->CurrentPage}</strong>";
            }

            if ($this->CurrentPage != $this->LastPage) {
                print $this->pageSeparator;
            }
        }

        function printText($text) {
            print $text;
        }

        function Display() {
            if (!$this->isVisible()) return;

            $PageOffset = round(($this->PageDelta - 1) / 2); // Distance for the border

            // Max & Min page
            if ($this->CurrentPage > $PageOffset + 1) {
                $MinPage = $this->CurrentPage - $PageOffset;
            } else {
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

            if ($this->CurrentPage != 1 && $MinPage >= 2) {
                $this->printPageLink(1, '1');
                print $this->pageSeparator;
                if ($MinPage >= 3) {
                    print $this->printText('...');
                    print $this->pageSeparator;
                }

            }

            for ($i = $MinPage; $i < $this->CurrentPage; $i++) {
                if ($i != $MinPage) {
                    print $this->pageSeparator;
                }
                $this->printPageLink($i, $i);
            }
            $this->printCurrentPage();
            for ($i = $this->CurrentPage + 1; $i <= $MaxPage; $i++) {
                if ($i != $this->CurrentPage + 1) {
                    print $this->pageSeparator;
                }
                $this->printPageLink($i, $i);
            }

            if ($this->CurrentPage != $this->LastPage && $MaxPage <= ($this->LastPage - 1)) {
                if ($MaxPage <= ($this->LastPage - 2)) {
                    print $this->pageSeparator;
                    print $this->printText('...');
                }

                print $this->pageSeparator;
                $this->printPageLink($this->LastPage, $this->LastPage);
            }
        }
    }

    class PageNavHref extends PageNav {
        function printPageLink($pageNumber, $linkText) {
            print '<a href="'.$this->getPageUrl($pageNumber).'">'.$linkText.'</a>';
        }
    }

    class RealestatePageNavHref extends PageNavHref {
        var $text;

        function RealestatePageNavHref($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage, $text = '') {
            parent::PageNav($aLink, $aQtyOfObjects, $aItemsPerPage, $aCurrentPage);
            $this->text = $text;
        }

        function Display() {
            if (!$this->isVisible()) return;

            $prev_page = $this->CurrentPage - 1;
            if ($prev_page < 1) $prev_page = null;

            $next_page = $this->CurrentPage + 1;
            if ($next_page > $this->LastPage) $next_page = null;

            if ($this->text) {
                print $this->text;
                print $this->pageSeparator;
            }

            if ($prev_page) {
                print '<a href="'.$this->getPageUrl($prev_page).'">&lt;&lt; prev </a>';
                print $this->pageSeparator;
            }

            parent::Display();

            if ($next_page) {
                print $this->pageSeparator;
                print '<a href="'.$this->getPageUrl($next_page).'"> next &gt;&gt;</a>';
            }

        }
    }

    class TPageNavigationWithHash extends TPageNavigation {
        var $LinkHash;

        function getPageUrl($pageNumber) {
            $link = $this->Link.$this->parameterName.'='.$pageNumber;
            if ($this->LinkHash) $link .= '#'.$this->LinkHash;
            return $link;
        }

    }
