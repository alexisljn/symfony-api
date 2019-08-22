<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function listArticlesAction(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $em)
    {
        // Récupération du manager de Doctrine, utilisé pour faire des requêtes (Ancienne méthode)
        //$em = $this->getDoctrine()->getManager();

        // Récupéres les entités de la table Article
        //$repo = $em->getRepository(Article::class);

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
        }

        $articles = $articleRepository->findAll();

        //$articlesNotExpired
        /*$articlesPublished = $repo->findBy(['published' => true]);
        $articlesNotPublished = $repo->findBy(['published' => false]);
        $articlesExpired = $repo->getExpiredArticles();*/
        // Retourne les résultats à la vue
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView()
        ]);
    }


}
