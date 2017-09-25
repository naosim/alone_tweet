<?php
include_once "loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/b0ef146d683da5b86bbff393444d94be/raw/ValueObject.php");

class ArticleId extends StringVO {
  static function create(DateTime $dateTime, int $num): ArticleId {
    return new ArticleId($dateTime->format('Ymd_Hi') . '_' . str_pad('' . $num, 10, "0", STR_PAD_LEFT));
  }
}

class ArticlePublishDateTime extends DateTimeVO {
  static function create_from_unixtimestamp(int $unix_timestamp): ArticlePublishDateTime { // ex) 20170101235959
    return new ArticlePublishDateTime(new DateTime('@' . $unix_timestamp));
  }
}

class ArticleCreateDateTime extends DateTimeVO {
  static function create(CurrentDateTime $currentDateTime): ArticleCreateDateTime {
    return new ArticleCreateDateTime($currentDateTime->getValue());
  }
}

class ArticleLastUpdateDateTime extends DateTimeVO {
  static function create(CurrentDateTime $currentDateTime): ArticleLastUpdateDateTime {
    return new ArticleLastUpdateDateTime($currentDateTime->getValue());
  }
}

class ArticleBody extends StringVO {
}

class ArticleEntity {
  private $id; // ArticleId
  private $publish_date; // ArticlePublishDateTime
  private $create_datetime;
  private $last_update_datetime;
  function __construct(
    ArticleId $id,
    ArticlePublishDateTime $publish_date,
    ArticleCreateDateTime $create_datetime,
    ArticleLastUpdateDateTime $last_update_datetime
  ) {
    $this->id = $id;
    $this->publish_date = $publish_date;
    $this->create_datetime = $create_datetime;
    $this->last_update_datetime = $last_update_datetime;
  }

  function id(): ArticleId {
    return $this->id;
  }

  function publish_datetime(): ArticlePublishDateTime {
    return $this->publish_date;
  }

  function create_datetime(): ArticleCreateDateTime {
    return $this->create_datetime;
  }

  function last_update_datetime(): ArticleLastUpdateDateTime {
    return $this->last_update_datetime;
  }

  function updateLastUpdateDateTime(CurrentDateTime $currentDateTime): ArticleEntity {
    return new ArticleEntity(
      $this->id,
      $this->publish_date,
      $this->create_datetime,
      ArticleLastUpdateDateTime::create($currentDateTime)
    );
  }

  static function createNew(
    ArticleId $id,
    ArticlePublishDateTime $publish_date,
    CurrentDateTime $current_dateime
  ): ArticleEntity {
    return new ArticleEntity(
      $id, $publish_date,
      ArticleCreateDateTime::create($current_dateime),
      ArticleLastUpdateDateTime::create($current_dateime)
    );
  }
}

class ArticleBodyEntity {
  private $id; // ArticleId
  private $body; // ArticleBody
  function __construct(ArticleId $id, ArticleBody $body) {
    $this->id = $id;
    $this->body = $body;
  }

  function id(): ArticleId {
    return $this->id;
  }

  function body(): ArticleBody {
    return $this->body;
  }
}

interface ArticleRepository {
  function get_next_id(): ArticleId;
  function save(ArticleEntity $entity);
  function update(ArticleEntity $entity);
  public function findById(ArticleId $articleId): ArticleEntity;
}

interface ArticleBodyRepository {
  function save(ArticleBodyEntity $entity);
  function update(ArticleBodyEntity $entity);
}
