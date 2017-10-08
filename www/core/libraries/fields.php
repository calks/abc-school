<?php



    class TCheckboxIdField extends TCheckboxField{
        var $attributes;

        function TCheckboxIdField($aName, $aValue, $attributes = NULL, $aChecked = false){
            $this->TField($aName, $aValue);
            $this->Checked = $aChecked;
            $this->attributes = $attributes;
        }

        function GetAsHTML() {
            $Res = '<input type="checkbox" name="'.$this->Name.'" value="'.$this->Value.'" '.$this->attributes;
            if ($this->Checked) {
              $Res .= ' checked';
            }
            $Res .= '>';

        return $Res;
      }

    }


    class TDateCalendarNoscriptField extends TDateCalendarField{
        function GetAsHTML(){
            if($this->isUnsupportedBrowser()){
              return TDateField::GetAsHTML();
            }

            $input_id = $this->Name."_inp";
            $button_id = $this->Name."_btn";

            $script = "<script type=\"text/javascript\">\n".
                "<!--\n ".
                " var set = new Object();\n".
                " set.inputField  = \"".$input_id."\";\n".
                " set.ifFormat    = \"".$this->dFormat."\";\n".
                " set.button      = \"".$button_id."\";\n".
                " set.align       = \"Tl\";\n".
                " set.singleClick = true;\n".
                " Calendar.setup(set);\n".
                "-->\n".
                "</script>\n";

           // $res = "<input size=\"".$this->size."\" name=\"".$this->Name."\" id=\"".$input_id."\" maxlength=\"10\" onfocus=\"javascript:vDateType='1'\" onkeyup=\"DateFormat(this,this.value,event,false,'1')\" onblur=\"DateFormat(this,this.value,event,true,'1')\" value=\"".$this->GetHTMLSafetyValue()."\" type=\"text\" onchange=\"synchroDate(this);\">";
            $res = "<input size=\"".$this->size."\" name=\"".$this->Name."\" id=\"".$input_id."\" maxlength=\"10\" onfocus=\"javascript:vDateType='1'\" value=\"".$this->GetHTMLSafetyValue()."\" type=\"text\" onchange=\"synchroDate(this);\">";
            $res .= "<img align=\"middle\" src=\"".$this->image."\" id=\"".$button_id."\" style=\"display:inline; vertical-align: middle; cursor: pointer;\" title=\"Date selector\" alt=\"Date selector\">";

            return $res.$script;

        }
    }


    class TObjectParentSelectField extends TSelectField {
        function TObjectParentSelectField($aName, $aValue, $aValues, $keyField, $valueField, $attributes=NULL) {
            $this->Name = $aName;
            $this->Value = $aValue;
            $this->Options = array();
            $this->Options[] = new TSelectOption(0, '--- Верхний уровень ---');
            foreach ($aValues as $obj) {
                $this->Options[] = new TSelectOption($obj->$keyField, $obj->$valueField);
            }
            $this->attributes = $attributes;
        }
    }


