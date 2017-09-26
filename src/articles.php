<?php
include_once "./php/domain.php";
include_once "./php/datasource.php";
include_once "./php/service.php";
include_once "./php/loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/b0ef146d683da5b86bbff393444d94be/raw/ValueObject.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/70ea426a90e8092b62257e76a5fc9495/raw/ApiUtils.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/af966db032b295711878386fb4dfde08/raw/ArraySchema.php");

function main() {
  api_forminput_jsonoutput(
    function(): array {
      $schema = new ArraySchema([
        'id' => [
          'description' => 'article id',
          'is_option' => true,
          'validate' => [Validation::length(24)]
        ]
      ]);
      $request = new ArrayValidation($schema ,$_GET);

      if($request->has('id')) {
        $id = $request->get('id', function($v) { return new ArticleId($v); });
        $body = (new ArticleBodyRepositoryImpl())->findById($id);
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
