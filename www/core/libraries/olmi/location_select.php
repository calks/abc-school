<?php

    Application::loadLibrary('olmi/field');

    class locationSelectField extends TSelectField {

        protected $values;
        protected $regionValue;
        protected $cityValue;

        function __construct($aName, $aValue, $aValues, $attributes = NULL) {

            $this->Name = $aName;
            $this->Value = $aValue;
            $this->Values = $aValues;
            $this->attributes = $attributes;

            $this->SetValue($aValue);
        }

        function SetValue($aValue) {
            $this->regionValue = isset($aValue['region']) ? $aValue['region'] : null;
            $this->cityValue = isset($aValue['city']) ? $aValue['city'] : null;
        }

        function GetValue() {
            return array(
                'region' => $this->regionValue,
                'city' => $this->cityValue
            );
        }

        function SetFromPost($POST) {
            $posted_value = isset($POST[$this->Name ]) ? $POST[$this->Name ] : '';

            if (!is_array($posted_value)) {
                if (strpos($posted_value, 'city_') !== false) {
                    $city_key = substr($posted_value, 5);
                    $this->cityValue = $city_key;
                    foreach ($this->Values as $region_key => $region_data) {
                        if (is_array($region_data['cities']) && array_key_exists($city_key, $region_data['cities'])) {
                            $this->regionValue = $region_key;
                        }
                    }
                } elseif (strpos($posted_value, 'region_') !== false) {
                    $region_key = substr($posted_value, 7);
                    $this->cityValue = null;
                    $this->regionValue = $region_key;
                }
            } else {
                $this->cityValue = $posted_value['city'];
                $this->regionValue = $posted_value['region'];
            }
        }

        function GetAsHTML() {

            $Res = '<select class="location_select" name="'.htmlspecialchars($this->Name).'"'.HtmlUtils::attributes($this->attributes).'>';

            foreach ($this->Values as $region_key => $region_data) {

                if (is_array($region_data)) {
                    $region_selected = ($this->regionValue == $region_key && is_null($this->cityValue)) ? 'selected="selected"' : '';
                    $Res .= "<option style=\"font-weight: bold;\" class=\"region\" value=\"region_$region_key\" $region_selected>{$region_data['name']}</option>\n";

                    foreach ($region_data['cities'] as $city_key => $city_data) {
                        $city_selected = ($this->cityValue == $city_key) ? 'selected="selected"' : '';
                        $Res .= "<option style=\"padding-left: 10px;\" class=\"city\" value=\"city_$city_key\" $city_selected>{$city_data['name']}</option>\n";
                    }
                } else {
                    $default_selected = (is_null($this->regionValue) && is_null($this->cityValue)) ? 'selected="selected"' : '';
                    $Res .= "<option class=\"default\" value=\"$region_key\" $default_selected>{$region_data}</option>\n";
                }
            }
            $Res .= "</select>";

           return $Res;
        }
    }
