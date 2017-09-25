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
      throw new RequestValidationException('GET not found');
    },
    function(){
      if(!isset($_POST['id'])) {
        throw new RequestValidationException('body required');
      }
      if(!isset($_POST['body'])) {
        throw new RequestValidationException('body required');
      }

      $id = new ArticleId($_POST['id']);
      $body = new ArticleBody($_POST['body']);

      $datetimeFactory = new DateTimeFactory();
      $service = new UpdateArticleService(new ArticleRepositoryImpl($datetimeFactory), new ArticleBodyRepositoryImpl(), $datetimeFactory);
      $service->updateBody($id, $body);
      return ResponseUtil::ok(array('ok'));
    }
  );
}

main();
