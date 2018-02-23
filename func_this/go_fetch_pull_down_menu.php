<?php
  function go_fetch_pull_down($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . go_output_string($name) . '"';
    if (go_not_null($parameters)) $field .= ' ' . $parameters;
    $field .= '>';
    if (empty($default) && isset($GLOBALS[$name])) $default = stripslashes($GLOBALS[$name]);
    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . go_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' SELECTED';
      }
      $field .= '>' . go_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';
    if ($required == true) $field .= TEXT_FIELD_REQUIRED;
    return $field;
  }   
?>