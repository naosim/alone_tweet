<?php
// snipet form https://gist.github.com/naosim/3f18a045222faffd4bf80903e42b8bef
function includeFromWeb($url, $root = '.') {
  $file = $root . '/vendor/' . explode('//', $url)[1];
  $dir = substr($file, 0, strrpos($file, '/'));
  if(!file_exists($file)) {
    if(!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
    $text = trim(file_get_contents($url));
    if(strpos($text, '<?php') === false) {
      throw new RuntimeException("Not PHP FILE: " . $url);
    }
    file_put_contents($file, $text);
  }

  include_once $file;
}
