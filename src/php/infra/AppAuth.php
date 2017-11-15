<?php
include_once "./php/loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/3d2d1f825baa6e533c238b86fceea82e/raw/BasicAuth.php");
class AppAuth {
  static function auth() {
    basic_auth('admin', 'test');
  }
}
