<?php

class TGroupedSelectField extends TField {
  var $Options;
  var $attributes;

  //====================================================
  // Constructor
  function TGroupedSelectField($aName, $aValue, $values, $attributes = NULL) {
    $this->Name = $aName;
    $this->Value = $aValue;
    $this->Options = array();
    foreach ($values as $group => $val) {
        if (!is_array($val)) $this->Options[$group]= new TSelectOption($group, $val);
        else {
            foreach ($val as $k => $v)
                $this->Options[$group][] = new TSelectOption($k, $v);
        }
    }
    $this->attributes = $attributes;
  }

  /**
   * @return string
   */
  function GetAsHTML(){
    $count = count($this->Options);
    $Res = '<select name="'.htmlspecialchars($this->Name).'"'.HtmlUtils::attributes($this->attributes).'>';
    foreach ($this->Options as $group=>$options) {
        if (is_object($options)) $Res .= $options->GetAsHTML($this->Value);
        else {
            $group = htmlspecialchars($group, ENT_QUOTES);
            $Res .= "<optgroup label=\"$group\">";
            foreach ($options as $opt) $Res .= $opt->GetAsHTML($this->Value);
            $Res .= "</optgroup>";
        }
    }
    $Res .= "</select>";
    return $Res;
  }

}
