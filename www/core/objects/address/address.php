<?php

    class address extends DataObject {

        const TABLE_NAME = 'addresses';
        const TABLE_NAME_CITY = 'cities';
        const TABLE_NAME_LOCATION = 'locations';

        public $object_id;
        public $object_type;
        public $city_id;
        public $street_address;
        public $state;
        public $zip;
        public $phone;
        public $email;
        public $website;
        public $additional_info;
        public $external_map_url;
        public $display_get_directions;

        public $coord_x;
        public $coord_y;

        protected static $internal_locations_collection;

        public function get_table_name()
        {
            return self :: TABLE_NAME;
        }

        public function __construct() {

            if (self::$internal_locations_collection === NULL) {
                $db = Application::getDb();
                $locations_table = $this->get_locations_table_name();
                $cities_table = $this->get_cities_table_name();
                $list = $db->executeSelectAllObjects("
                    SELECT
                        c.id AS city_id, c.name AS city_name, c.code,
                        l.id AS location_id, l.name AS location_name
                    FROM $cities_table c LEFT JOIN $locations_table l ON c.location_id = l.id
                ");
                self::$internal_locations_collection = array();
                foreach ($list as $item) {
                    if (!isset(self::$internal_locations_collection[$item->location_id])) self::$internal_locations_collection[$item->location_id] = array(
                        'name' => $item->location_name,
                        'cities' => array()
                    );
                    self::$internal_locations_collection[$item->location_id]['cities'][$item->city_id] = array(
                        'name' => $item->city_name,
                        'code' => $item->code
                    );
                }
            }
        }

        function get_cities_table_name() {
            return self::TABLE_NAME_CITY;
        }

        function get_locations_table_name() {
            return self::TABLE_NAME_LOCATION;
        }

        function get_locations_collection() {
            return self::$internal_locations_collection;
        }

        function get_region_select($add_null_item = false) {
            $out = get_empty_select($add_null_item);
            $data = $this->get_locations_collection();

            foreach ($data as $location_id => $location_info) {
                $out[$location_id] = $location_info['name'];
            }

            return $out;
        }

        function get_city_select($add_null_item = false) {
            $out = get_empty_select($add_null_item);
            $data = $this->get_locations_collection();
            foreach ($data as $location_info) {
                $options = array();
                foreach ($location_info['cities'] as $id => $city_info) {
                    $option = $city_info['name'];
                    if ($city_info['code']) $option .= ' ('.$city_info['code'].')';
                    $options[$id] = $option;
                }
                $out[$location_info['name']] = $options;
            }
            return $out;
        }

        function get_location_select($add_null_item = false) {
            $out = get_empty_select($add_null_item);
            $data = $this->get_locations_collection();
            foreach ($data as $k => $v) {
                $out[$k] = $v;
            }
            return $out;
        }

        public function make_form(&$form, $add_null_item = false) {
            //$form = parent::make_form($form);

            Application::loadLibrary('olmi/grouped_select');
            $form->addField(new TGroupedSelectField("city_id", "", $this->get_city_select(true)));
            $form->addField(new TEditField("street_address", "", 100, 255));
            $form->addField(new TEditField("state", "", 50, 100));
            $form->addField(new TEditField("zip", "", 20, 20));
            $form->addField(new TEditField("phone", "", 100, 255));
            $form->addField(new TEditField("email", "", 100, 255));
            $form->addField(new TEditField("website", "", 100, 255));
            $form->addField(new TEditorField("additional_info"));
            $form->addField(new TEditField("external_map_url", "", 100, 255));
            $form->addField(new TCheckboxField("display_get_directions", ""));
            $form->addField(new TEditField("coord_x", "", 20, 20));
            $form->addField(new TEditField("coord_y", "", 20, 20));

            return $form;
        }

        public function load_list($params) {
            $locations_table = $this->get_locations_table_name();
            $locations_abr = $this->get_table_abr($locations_table);

            $cities_table = $this->get_cities_table_name();
            $cities_abr = $this->get_table_abr($cities_table);
            $table_abr = $this->get_table_abr();

            $params['fields'][] = "$locations_abr.name as location_name";
            $params['fields'][] = "$cities_abr.location_id";
            $params['fields'][] = "$cities_abr.name as city_name";
            $params['fields'][] = "$cities_abr.code as city_code";

            $params['from'][] = "LEFT JOIN `$cities_table` $cities_abr ON $cities_abr.id = $table_abr.city_id";
            $params['from'][] = "LEFT JOIN `$locations_table` $locations_abr ON $locations_abr.id = $cities_abr.location_id";

            $list = parent::load_list($params);

            return $list;
        }

        // TODO this hack is for real estate object only. Normally it is enought
        // to have load_into_object_list() method only and use it inside load_list()
        // methods of each object (cause in our architecture load() method calls load_list()
        // to actually retrieve object data). So it can be safely deleted after move
        // of real estate to new architecture (making it a DataObject son)
        public function load_into_object(&$object) {
            $list = array($object);
            $this->load_into_object_list($list);
            $object = $list[0];
        }

        public function load_into_object_list( $list, $params = array() ) {
            $ids = array();
            $object_mapping = array();

            foreach ($list as $key => $item) {
                $ids[$item->getName()][] = $item->id;
                $object_mapping[$item->getName()][$item->id] = $key;
            }

            $conditions = array();
            foreach ($ids as $object_type => $object_ids) {
                if (!$object_ids) continue;
                foreach ($object_ids as & $id) $id = (int) $id;
                $object_ids = implode(',', $object_ids);
                $object_type = addslashes($object_type);
                $conditions[] = "object_type='$object_type' AND object_id IN($object_ids)";
            }

            if (!$conditions) return;

            $params['where'][] = implode(' OR ', $conditions);
            $address_list = $this->load_list($params);

            foreach ($address_list as $addr) {
                $item_key = $object_mapping[$addr->object_type][$addr->object_id];
                $list[$item_key]->internal_address = $addr;
            }
        }

        public function save() {
            if (!$this->object_id) return null;
            if (!$this->object_type) return null;

            //if( $this->website && 0 !== strpos( $this->website, 'http://' ) )
            //  $this->website = 'http://' . $this->website;

            $this->display_get_directions = (int) $this->display_get_directions;
            $this->coord_x = (float)( $this->coord_x );
            $this->coord_y = (float)( $this->coord_y );
            $this->city_id = (int)( $this->city_id );
            return parent::save();
        }

        public function mass_delete($object_type, $object_id) {
            $object_type = addslashes($object_type);
            $object_id = (int) $object_id;
            $db = Application::getDb();
            $table = $this->get_table_name();
            $db->execute("
                DELETE FROM $table
                WHERE object_type='$object_type' AND object_id=$object_id
            ");
        }

        public function get_city_name($city_id) {
            $collection = $this->get_locations_collection();
            foreach ($collection as $location_id => $location_data) {
                $cities = $location_data['cities'];
                if (isset($cities[$city_id])) return $cities[$city_id]['name'];
            }
            return '';
        }

        public function get_city_code($city_id) {
            $collection = $this->get_locations_collection();
            foreach ($collection as $location_id => $location_data) {
                $cities = $location_data['cities'];
                if (isset($cities[$city_id])) return $cities[$city_id]['code'];
            }
            return '';
        }


        public function get_region_name($location_id) {
            $collection = $this->get_locations_collection();
            if (isset($collection[$location_id])) return $collection[$location_id]['name'];
            return '';
        }

        public function get_address_string() {
            $out = array();

            if( $this->coord_x && $this->coord_y )
                $out = "{$this->coord_x},{$this->coord_y}";
            else
            {
                $street_address = isset($this->street_address) ? $this->street_address : null;
                $state = isset($this->state) ? $this->state : null;
                $zip = isset($this->state) ? $this->zip : null;
                $city_name = isset($this->city_name) ? $this->city_name : null;

                if ($street_address) $out[] = $street_address;
                if ($city_name) $out[] = $city_name;
                if ($state || $zip) {
                    $state_zip = array();
                    if ($state) $state_zip[] = $state;
                    if ($zip) $state_zip[] = $zip;
                    $out[] = implode(' ', $state_zip);
                }

                $out = implode(', ', $out);
            }

            return $out;
        }

        public function getWebsiteHref()
        {
            if( $this->website && 0 !== strpos( $this->website, 'http://' ) && 0 !== strpos( $this->website, 'https://' ) )
                return 'http://' . $this->website;
            else return $this->website;
        }

        public function on_map() {
            return ($this->coord_x!=0 || $this->coord_y!=0);
        }


    }
