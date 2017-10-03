<?php
include_once "./php/domain.php";
include_once "./php/loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/af966db032b295711878386fb4dfde08/raw/ArraySchema.php");

class SchemaTypes {
  static $required = false;
  static $option = true;
}

interface SchemaDefine {
  function schema(bool $is_option): array;
  function value(ArrayValidation $validation, string $key);
}

class ArticleIdDefine implements SchemaDefine {
  function schema(bool $is_option = false): array {
    return [
      'description' => 'article id',
      'is_option' => $is_option,
      'validate' => [Validation::length(24)]
    ];
  }

  function value(ArrayValidation $validation, string $key): ArticleId {
    return $validation->get($key, function($v) { return new ArticleId($v); });
  }
}

class ArticleBodyDefine implements SchemaDefine {
  function schema(bool $is_option = false): array {
    return [
      'description' => 'body text',
      'is_option' => $is_option
    ];
  }

  function value(ArrayValidation $validation, string $key): ArticleBody {
    return $validation->get($key, function($v) { return new ArticleBody($v); });
  }
}

class ArticlePublishDateTimeUnixDefine implements SchemaDefine {
  function schema(bool $is_option = false): array {
    return [
      'description' => '[sec], not [msec]',
      'validate' => [Validation::length(10)]
    ];
  }

  function value(ArrayValidation $validation, string $key): ArticlePublishDateTime {
    return $publish_datetime = $validation->get($key, function($v) { return ArticlePublishDateTime::create_from_unixtimestamp(intval($v)); });
  }
}
