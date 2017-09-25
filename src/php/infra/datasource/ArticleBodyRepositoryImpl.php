<?php
include_once "./php/loader.php";
include_once "./php/domain.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/4bd4aefe6f687b8a3fcb1b365519c933/raw/File.php");

class ArticleFileName extends File {
  function save_body(ArticleBody $body) {
    $this->save_text($body->getValue());
    chmod($this->getValue(), 770);
  }
  static function create(ArticleId $id): ArticleFileName {
    return new ArticleFileName('./data/' . substr($id->getValue(), 0, 4) . '/' . substr($id->getValue(), 4, 4) . '/' . $id->getValue() . '.txt');
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
