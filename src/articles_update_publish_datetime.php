<?php
include_once "./php/domain.php";
include_once "./php/datasource.php";
include_once "./php/service.php";
include_once "./php/loader.php";
include_once "./php/infra/api/SchemaTypes.php";
include_once "./php/infra/AppAuth.php";

includeFromWeb("https://gist.githubusercontent.com/naosim/b0ef146d683da5b86bbff393444d94be/raw/ValueObject.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/70ea426a90e8092b62257e76a5fc9495/raw/ApiUtils.php");
includeFromWeb("https://gist.githubusercontent.com/naosim/af966db032b295711878386fb4dfde08/raw/ArraySchema.php");

function main() {
  AppAuth::auth();

  api_forminput_jsonoutput(
    function(): array {
      throw new RequestValidationException('GET not found');
    },
    function(){
      $idDefine = new ArticleIdDefine();
      $publishDatetimeDefine = new ArticlePublishDateTimeUnixDefine();
      $schema = new ArraySchema([
        'id' => $idDefine->schema(),
        'publish_datetime_unix' => $publishDatetimeDefine->schema()
      ]);
      $request = new ArrayValidation($schema ,$_POST);

      $id = $idDefine->value($request, 'id');
      $publish_datetime = $publishDatetimeDefine->value($request, 'publish_datetime_unix');

      $datetimeFactory = new DateTimeFactory();
      $service = new UpdateArticleService(new ArticleRepositoryImpl($datetimeFactory), new ArticleBodyRepositoryImpl(), $datetimeFactory);
      $service->updatePublishDateTime($id, $publish_datetime);
      return ResponseUtil::ok(array('ok'));
    }
  );
}

main();
