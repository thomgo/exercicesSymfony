<?php
namespace App\Service;

use App\Entity\Article;

/**
 *
 */
class SwearCleaner
{
  const SWEARS = ["merde", "connard", "abruti", "putain", "bordel"];
  const REPLACEMENT = "########";

  public function cleanSwear(Article $article): Article {
    $article->setContent(str_replace(self::SWEARS, self::REPLACEMENT, $article->getContent()));
    return $article;
  }

}
