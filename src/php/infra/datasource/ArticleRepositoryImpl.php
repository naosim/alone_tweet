<?php
include_once "./php/domain.php";

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

  private function sortByPublishDateTime(array $json): array {
    $sortAction = function(array $a, array $b) {
      $publish = $b[1] - $a[1];
      if($publish != 0) {
        return $publish;
      }
      return $b[2] - $a[2];
    };
    usort($json, $sortAction);
    return $json;
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
    $json = $this->sortByPublishDateTime($json);
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
    $newJson = $this->sortByPublishDateTime($newJson);
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
