<?

    class CollectionListingCouplingField extends TField {

        var $fields;
        var $limiter;

        function __construct($name, $collection, $checkedValues = array(), $on_top = array(), $limiter = NULL) {
            $this->TField($name, $checkedValues);
            $this->fields = $collection;
            $this->limiter = intval($limiter) < 1 ? 1 : intval(abs($limiter));
        }

        function SetFromPost($POST) {
            if (isset($POST[$this->Name]))
                $items = $POST[$this->Name];
            else
                $items = array();

            if (isset($POST[$this->Name."_on_top"]))
            {
                $on_top = $POST[$this->Name."_on_top"];
            }
            else
                $on_top = array();

            $fields = array();
            foreach ($items as $item) {
                $obj = new stdClass();
                $obj->id = (int) $item;
                $obj->on_top = (int) in_array($item, $on_top);
                $fields[ $obj->id ] = $obj;
            }

            $this->SetValue($fields);
        }

        function GetAsHTML($tableAttr = array(), $checkBoxAttr = array()) {
            $res = "<table ".HtmlUtils::attributes($tableAttr)." summary=\"\">\n<tr>";
            $i = 0;
            foreach ($this->fields as $value => $caption) {
                if ($this->limiter > 0 && $i % $this->limiter == 0 && $i != 0)
                    $res .= "</tr><tr>\n";

                $res .= "<td>".$this->getItemAsHTML($value)."</td>";
                $res .= "<td>".($caption ? $caption : "&nbsp;")."</td>\n";
                $res .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Place at the top:</td>";
                $res .= "<td>".$this->getItemOnTopAsHTML($value)."</td>\n";

                $i++;
            }
            $res .= "</tr></table>\n";
            return $res;
        }

        function isCheched($value) {
            return @array_key_exists($value, $this->Value);
        }

        function getItemAsHTML($value, $checkboxAttr = array()) {
            if (!array_key_exists($value, $this->fields)) {
                return "wrong value for CollectionCheckBoxField::getItemOnTopAsHTML($value)";
            }

            $isChecked = $this->isCheched($value) ? " checked" : "";
            return "<input type=\"checkbox\" name=\"".$this->Name."[]\" value=\"".$value."\" ".HtmlUtils::attributes($checkboxAttr).$isChecked.">";
        }

        function getItemOnTopAsHTML($value) {
            if (!array_key_exists($value, $this->fields)) {
                return "wrong value for CollectionCheckBoxField::getItemOnTopAsHTML($value)";
            }

            $isChecked = $this->Value[$value]->on_top ? " checked" : "";
            return "<input type=\"checkbox\" name=\"".$this->Name."_on_top[]\" value=\"".$value."\" ".$isChecked.">";
        }

    }
