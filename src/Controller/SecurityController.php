<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\LoginUserType;
use App\Form\ProfileUserType;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $event = new UserRegisteredEvent($user);
            $eventDispatcher->dispatch(UserRegisteredEvent::NAME,$event);
            $logger->info('User registered now !');
            $this->addFlash('success', 'You created successfully an account');
            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginUserType::class, $user);

        return $this->render('security/login.html.twig', array(
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('security/profile.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(UserRepository $userRepository) {
        $users = $userRepository->findAll();
        return $this->render('security/admin.html.twig', array(
            'users'=> $users
        ));
    }
}
