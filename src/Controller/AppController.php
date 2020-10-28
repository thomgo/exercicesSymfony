<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Service\SwearCleaner;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AppController extends AbstractController
{

  /**
   * @Route("/index", name="app_home")
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
     * @Route("/admin/article/add", name="admin_add")
     * @IsGranted("ROLE_ADMIN")
    */
    public function newArticle(Request $request) {
      $article = new Article();
      $form = $this->createForm(ArticleType::class, $article);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $article->setCreation(new \DateTime());
        $article->setViewsNumber(0);
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
      }
      return $this->render("admin/addArticle.html.twig", ["form" => $form->createView()]);

     }

     /**
      * @Route("/add/comment", name="app_addComment")
     */
     public function newComment(Request $request) {
       $comment = new Comment();

       $form = $this->createFormBuilder($comment)
           ->add('content', TextType::class)
           ->add('pseudo', TextType::class)
           ->add('enregistrer', SubmitType::class)
           ->getForm();

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
         dump($comment);
         $comment->setDate();
         $comment->setUser($this->getUser());
       }

       return $this->render("comment/new.html.twig", [
         'form' => $form->createView()
       ]);

      }
}
