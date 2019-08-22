<?php


namespace App\Manager;


use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleManager
{
    private $articleRepository;
    private $em;

    public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $em)
    {
        $this->articleRepository = $articleRepository;
        $this->em = $em;
    }

    public function countArticlesForUser($id)
    {
        $counter = $this->articleRepository->count(['user' => $id]);
        return $counter;
    }

}