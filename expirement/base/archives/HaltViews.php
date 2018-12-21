<?php

class HaltViews extends ViewEngine {
 public function drawCountries($countries = null) {
  if (!is_array($countries)) {
   throw new Exception('Invalid dataset for countries when loading view.', 500);
  }

  $html = '';
  foreach ($countries as $country) {
   $html .= '<option value=' . $country['country_id'] . '>' . $country['country_name'] . '</option>';
  }
  $html .= '<option value="0">Not Listed</option>';

  return $html;
 }
}