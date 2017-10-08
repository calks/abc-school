<?php

    //set_include_path(get_include_path() . PATH_SEPARATOR . '')

    set_include_path(get_include_path()
        . PATH_SEPARATOR . Application::getSitePath() . '/core/libraries/qcal/lib'
    );

    include_once 'lib/autoload.php';


    class QCalAdapter {
        protected $errors;
        protected $date_list;

        public function __construct() {
            $this->errors = array();
            $this->date_list = array();
        }


        public function getErrors() {
            return $this->errors;
        }

        // recursive function to extract reserved dates list
        // from sophisticated qCal class output
        protected function qCalToDateList($qcal_node) {
            if ($qcal_node->getName() == 'VEVENT') {
                $start = $qcal_node->getProperty('DTSTART');
                $end = $qcal_node->getProperty('DTEND');

                // get unix timestamp of event start and end time
                $start = $start[0]->getValueObject()->getValue()->format('U');
                $end = $end[0]->getValueObject()->getValue()->format('U');

                echo date("d/m/Y H:i:s", $start) . ' - ' . date("d/m/Y H:i:s", $end) . '<br>';

                $dates = array();
                $date = $start;
                while($date <= $end) {
                    if (empty($dates)) $type = 'evening';
                    elseif($date == $end) $type = 'morning';
                    else $type = 'booked';

                    $entry = new stdClass();
                    $entry->type = $type;
                    $entry->cdate = date('Y-m-d H:i:s', $date);

                    $dates[] = $entry;

                    $date += 3600 * 24;
                }

                $this->date_list = array_merge($this->date_list, $dates);
                //echo $start . ' ' . $end;

                return;
            }
            foreach ($qcal_node->getChildren() as $child) {
                if (is_array($child)) {
                    foreach ($child as $c) {
                        $this->qCalToDateList($c);
                    }
                }
                else {
                    $this->qCalToDateList($child);
                }
            }
        }

        // the function scans date list and merges consequent reservations
        // (where end of first reservation and start of the second occur on same day)
        // into one big reservation
        protected function mergeOverlappingReservations() {
            foreach($this->date_list as $date1) {
                if ($date1->type != 'morning') continue;
                foreach($this->date_list as $key=>$date2) {
                    if ($date2->type != 'evening') continue;
                    if ($date2->cdate != $date1->cdate) continue;
                    $date1->type = 'booked';
                    unset($this->date_list[$key]);
                }
            }
        }

        public function extractReservedDatesList($ical_feed_url) {
            if (!$ical_feed_url || !website_address_valid($ical_feed_url)) {
                $this->errors[] = "Invalid feed URL";
                return false;
            }
            $content = @file_get_contents($ical_feed_url);

            if ($content === false) {
                $this->errors[] = "Can't read feed data";
                return false;
            }

            // In theory, calendar date should always contain
            // some wrapper structures (BEGIN END and so on)
            // But VRBO.com supply zero length file for empty calendar.
            // So we exit here to avoid parser errors
            if(!$content) return;

            $parser = new qCal_Parser();
            $ical = $parser->parse($content);

            $this->date_list = array();
            $result = $this->qCalToDateList($ical);
            if ($result === false) return $false;
            $this->mergeOverlappingReservations();

            return $this->date_list;

        }



    }



