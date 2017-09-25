<?php
include_once "./php/domain.php";

class NewArticleService {
  private $articleRepository;
  private $articleBodyRepository;
  private $dateTimeFactory;

  function __construct(ArticleRepository $articleRepository, ArticleBodyRepository $articleBodyRepository, DateTimeFactory $dateTimeFactory) {
    $this->articleRepository = $articleRepository;
    $this->articleBodyRepository = $articleBodyRepository;
    $this->dateTimeFactory = $dateTimeFactory;
  }

  function invoke(ArticleBody $body, ArticlePublishDateTime $publish_Date): ArticleId {
    $id = $this->articleRepository->get_next_id();
    $current_datetime = $this->dateTimeFactory->createCurrentDateTime();

    $article = ArticleEntity::createNew($id, $publish_Date, $current_datetime);
    $body_e = new ArticleBodyEntity($id, $body);

    $this->articleRepository->save($article);
    $this->articleBodyRepository->save($body_e);

    return $id;
  }
}

class UpdateArticleService {
  private $articleRepository;
  private $articleBodyRepository;
  private $dateTimeFactory;

  function __construct(ArticleRepository $articleRepository, ArticleBodyRepository $articleBodyRepository, DateTimeFactory $dateTimeFactory) {
    $this->articleRepository = $articleRepository;
    $this->articleBodyRepository = $articleBodyRepository;
    $this->dateTimeFactory = $dateTimeFactory;
  }

  function updateBody(ArticleId $id, ArticleBody $body) {
    $entity = $this->articleRepository->findById($id);
    $current_datetime = $this->dateTimeFactory->createCurrentDateTime();

    $entity = $entity->updateLastUpdateDateTime($current_datetime);
    $body_e = new ArticleBodyEntity($id, $body);

    $this->articleRepository->update($entity);
    $this->articleBodyRepository->update($body_e);
  }
}
