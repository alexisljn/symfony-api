<?php

namespace App\Controller;

use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(UserManager $userManager)
    {
        $users = $userManager->getAllUsers();
        return $this->render('home/index.html.twig', [
            'users' => $users,
        ]);
    }
}
