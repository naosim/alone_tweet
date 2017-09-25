<?php
include_once "./php/domain.php";
include_once "./php/datasource.php";
include_once "./php/service.php";
include_once "./php/loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/b0ef146d683da5b86bbff393444d94be/raw/ValueObject.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/70ea426a90e8092b62257e76a5fc9495/raw/ApiUtils.php");

class ArraySchema {
  private $schema;
  private $params;
  function __construct($schema, $params) {
    $this->schema = $schema;
    $this->params = $params;

  }

  function validate($key) {
    $s = $this->schema[$key];
    if($s === null) {
      throw new RuntimeException("$key is not defined in schema");
    }

    if($s['is_option'] && !isset($this->params[$key])) {
      return;
    }

    if(!$s['is_option'] && !isset($this->params[$key])) {
      throw new RequestValidationException("$key required");
    }

    $v = $this->params[$key];

    if($s['validate']) {
      $msg = $s['get_not_valid_message']($v);
      if($msg) {
        throw new RequestValidationException($msg);
      }
    }
  }

  function is(string $key): bool {
    $this->validate($key);
    return isset($this->params[$key]);
  }

  function get(string $key, Closure $map = null) {
    $this->validate($key);
    $v = $this->params[$key];
    if($map == null) {
      return $v;
    }
    return $map($v);
  }
}

function main() {
  api_forminput_jsonoutput(
    function(): array {
      $schema = array(
        'article_id' => array('is_option'=>true)
      );
      $request = ArraySchema($schema ,$_GET);


      if(isset($_GET['article_id'])) {
        $body = (new ArticleBodyRepositoryImpl())->findById(new ArticleId($_GET['article_id']));
        return array('body' => $body->getValue());
      }
      $datetimeFactory = new DateTimeFactory();
      $ary = (new ArticleRepositoryImpl($datetimeFactory))->all();
      $result = [];
      foreach($ary as $entity) {
        $result[] = array(
          "id" => $entity->id()->getValue(),
          "publish_datetime" => $entity->publish_datetime()->getApiValue(),
          "create_datetime" => $entity->create_datetime()->getApiValue(),
          "last_update_datetime" => $entity->last_update_datetime()->getApiValue(),
        );
      }
      return $result;
    },
    function(){
      throw new RequestValidationException('POST not found');
    }
  );
}

main();
