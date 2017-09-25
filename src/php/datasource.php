<?php
include_once "loader.php";
include_once "./php/domain.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/4bd4aefe6f687b8a3fcb1b365519c933/raw/File.php");


function rstrpos ($haystack, $needle) {
  $size = strlen ($haystack);
  $pos = strpos(strrev($haystack), $needle);
  if ($pos === false) {
    return false;
  }
  return $size - $pos;
}

class ArticleFileName extends File {
  function save_body(ArticleBody $body) {
    $this->save_text($body->getValue());
    chmod($this->getValue(), 770);
  }
  static function create(ArticleId $id): ArticleFileName {
    return new ArticleFileName('./data/' . substr($id->getValue(), 0, 4) . '/' . substr($id->getValue(), 4, 4) . '/' . $id->getValue() . '.txt');
  }
}

class ArticleRepositoryImpl implements ArticleRepository {
  private $datetimeFactory;

  function __construct(DateTimeFactory $datetimeFactory) {
    $this->datetimeFactory = $datetimeFactory;
  }

  public function get_next_id(): ArticleId {
    $filename = './data/article_id_sequence.txt';
    $text = file_get_contents($filename);
    if(!$text) {
      $text = '0';
    }
    $num = intval($text);
    $num = $num + 1;
    file_put_contents($filename, strval($num));
    return ArticleId::create($this->datetimeFactory->createDateTime(), $num);
  }

  private function load_json(): array {
    $filename = './data/article_list.json';
    $text = file_get_contents($filename);
    if(!$text) {
      $text = '[]';
    }
    return json_decode($text, true);
  }

  public function save(ArticleEntity $entity) {
    $filename = './data/article_list.json';
    $json = $this->load_json();
    $json[] = array(
      $entity->id()->getValue(),
      $entity->publish_datetime()->getDbValue(),
      $entity->create_datetime()->getDbValue(),
      $entity->last_update_datetime()->getDbValue()
    );
    file_put_contents($filename, json_encode($json));
  }

  public function update(ArticleEntity $entity) {
    $filename = './data/article_list.json';
    $json = $this->load_json();
    $newJson = [];
    foreach($json as $obj) {
      if($obj[0] != $entity->id()->getValue()) {
        $newJson[] = $obj;
      }
    }
    $newJson[] = array(
      $entity->id()->getValue(),
      $entity->publish_datetime()->getDbValue(),
      $entity->create_datetime()->getDbValue(),
      $entity->last_update_datetime()->getDbValue()
    );
    file_put_contents($filename, json_encode($newJson));
  }

  public function all(): array {
    $ary = [];
    $json = $this->load_json();
    foreach($json as $obj) {
      $ary[] = new ArticleEntity(
        new ArticleId($obj[0]),
        new ArticlePublishDateTime(new DateTime('@' . $obj[1])),
        new ArticleCreateDateTime(new DateTime('@' . $obj[2])),
        new ArticleLastUpdateDateTime(new DateTime('@' . $obj[3]))
      );
    }
    return $ary;
  }

  public function findById(ArticleId $articleId): ArticleEntity {
    $ary = $this->all();
    $entity = null;
    foreach($ary as $e) {
      if($e->id()->getValue() === $articleId->getValue()) {
        return $e;
      }
    }

    throw new RuntimeException("not found");
  }
}

class ArticleBodyRepositoryImpl implements ArticleBodyRepository {
  public function save(ArticleBodyEntity $entity) {
    $file = ArticleFileName::create($entity->id());
    $file->save_body($entity->body());
  }

  public function update(ArticleBodyEntity $entity) {
    $this->save($entity);
  }

  public function findById(ArticleId $articleId): ArticleBody {
    $file = ArticleFileName::create($articleId);
    if(!$file->exists()) {
      throw new RuntimeException('file not found');
    }
    return new ArticleBody($file->load_text());
  }
}
