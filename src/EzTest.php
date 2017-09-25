<?php
class _EzTestSubject {
  public $parent;
  public $value;
  function __construct(string $value, ?_EzTestSubject $parent = null) {
    $this->value = $value;
    $this->parent = $parent;
  }

  function get_nest_count(): int {
    $v = 0;
    $current = $this;
    while($current->parent !== null) {
      $current = $current->parent;
      $v = $v + 1;
    }
    return $v;
  }

  function get_text() {
    $current = $this;
    $v = $current->value;
    while($current->parent !== null) {
      $current = $current->parent;
      $v = $current->value . ' : ' . $v;
    }
    return $v;
  }
}

class EzTest {
  public $subject;
  public $array;
  function __construct(_EzTestSubject $subject, array $array) {
    $this->subject = $subject;
    $this->array = $array;
  }

  function run(): int {
    $count = 0;
    foreach($this->array as $test) {
      $class_name = get_class($test);
      if($class_name === 'EzTest') {
        $count += (new EzTest(new _EzTestSubject($test->subject->value, $this->subject) , $test->array))->run();
      } else {
        $test(function($act, $exp, $msg = 'テストエラー') {
          $t = $this->subject->get_text();
          $trace = debug_backtrace();
          $msg = "$t : $msg : " . $trace[0]['file'] . ' : ' . $trace[0]['line'];
          assert($act === $exp, $msg);
        });
        $count++;
      }
    }

    if($this->subject->get_nest_count() == 0) {
      echo "TOTAL TEST COUNT : $count";
    }
    return $count;
  }
}
function test(string $text, array $array) {
  return new EzTest(new _EzTestSubject($text), $array);
}
