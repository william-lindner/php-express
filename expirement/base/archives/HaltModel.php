<?php

class HaltModel extends Model {
 protected function unsetHalt() {
  if (isset($_SESSION['halt_view'])) {
   unset($_SESSION['halt_view']);
  }
  return true;
 }
}