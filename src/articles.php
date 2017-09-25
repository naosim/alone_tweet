<?php
include_once "./php/domain.php";
include_once "./php/datasource.php";
include_once "./php/service.php";
include_once "./php/loader.php";
includeFromWeb("https://gist.githubusercontent.com/naosim/b0ef146d683da5b86bbff393444d94be/raw/ValueObject.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/70ea426a90e8092b62257e76a5fc9495/raw/ApiUtils.php");

function main() {
  api_forminput_jsonoutput(
    function(): array {
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
      if(!isset($_POST['body'])) {
        throw new RequestValidationException('body required');
      }

      if(!isset($_POST['publish_datetime_unix'])) {
        throw new RequestValidationException('publish_datetime_unix required');
      }

      $body = new ArticleBody($_POST['body']);

      $publish_datetime = ArticlePublishDateTime::create_from_unixtimestamp(intval($_POST['publish_datetime_unix']));

      $datetimeFactory = new DateTimeFactory();
      $service = new NewArticleService(new ArticleRepositoryImpl($datetimeFactory), new ArticleBodyRepositoryImpl(), $datetimeFactory);
      $id = $service->invoke($body, $publish_datetime);
      return ResponseUtil::ok(array('article_id'=>$id->getValue()));
    }
  );
}

main();
