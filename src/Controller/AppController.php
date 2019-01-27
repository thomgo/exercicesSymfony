<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Service\SwearCleaner;

class AppController extends AbstractController
{

  /**
   * @Route("/index")
   * @Route("/")
  */
  public function home() {
    $repository = $this->getDoctrine()->getRepository(Article::class);
    $articles = $repository->findAll();

    return $this->render("article/home.html.twig", ["articles" => $articles]);

   }

   /**
    * @Route("/article/{id}", name="single_show", requirements={"id"="\d+"})
   */
   public function single($id = 1, SwearCleaner $swearCleaner) {
     $repository = $this->getDoctrine()->getRepository(Article::class);
     $article = $repository->find($id);
     if (!$article) {
      throw $this->createNotFoundException("Mince il semble que vous cherchiez à voir un article qui n'existe pas");
     }
     $article = $swearCleaner->cleanSwear($article);
     return $this->render("article/single.html.twig", ["article" => $article]);
    }

    /**
     * @Route("/admin/article/add")
    */
    public function newArticle() {

      return new Response("Voici l'admin");

     }
}
