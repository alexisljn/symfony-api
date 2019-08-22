<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractFOSRestController
{

    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user", name="user")
     */
    // Ancienne méthode dans ArticleController (appel en dur du Repo et de l'EntityManager dans le body de la fonction)
    public function index(Request $request, UserManager $userManager)
    {
        $user = new User();
        // Création du formulaire. 2 params, la classe du formulaire et l'instanciation de l'entité concernée
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        // Validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //$entityManager = $this->getDoctrine()->getManager();
            // Met l'opération d'ajout d'un User dans la "file d'attente"
            //$entityManager->persist($user);
            // On vide la file d'attente
            //$entityManager->flush();
            $userManager->saveUser($user);
            return $this->redirectToRoute('home');

        }

        // Nouvelle manière de faire qui injecte le repo dans la fonction pour utiliser les méthodes de doctrine
        // plus rapidement
        $users = $userManager->getAllUsers();

        return $this->render('user/index.html.twig', array(
            'form' => $form->createView(),
            'users' => $users
        ));

    }

    /**
     * @Route("/user/remove/{id}", name="user_remove")
     */
    public function remove(User $user, EntityManagerInterface $em)
    {
        $articles = $user->getArticles();
        foreach($articles as $article) {
            $article->setUser(null);
        }

        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('home');
    }

    /**
    // * @Route("/user/{id}", name="profile")
     */
   /* public function profile(User $user)
    {
        // Créer la vue associé et retourner user
        return $this->render('user/profile.html.twig', array(
            'user' => $user,
        ));
    }*/


    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/api/users/{email}")
     */
    public function getApiUser(User $user)
    {
        return $this->view($user);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/api/users")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the datas of users",
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Forbidden",
     * )
     */
    public function getApiUsers(){
        $users = $this->userRepository ->findAll();
        return $this->view($users);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Post("/api/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function postApiUser(User $user, ConstraintViolationListInterface $validationErrors)
    {
        $errors = [];
        if($validationErrors->count() > 0) {
            /** @var ConstraintViolation $constraintViolation */
            foreach ($validationErrors as $constraintViolation) {
                // Returns the violation message. (Ex. This value should not be blank.)
                $message = $constraintViolation->getMessage();
                // Returns the property path from the root element to the violation. (Ex. lastname)
                $propertyPath = $constraintViolation->getPropertyPath();
                $errors[] = ['message' => $message, 'propertyPath' => $propertyPath];
            }
        }
        if (!empty($errors)) {
            // Throw a 400 Bad Request with all errors messages (Not readable, you can do better)
            throw new BadRequestHttpException(json_encode( $errors));
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->view($user);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Patch("/api/users/{email}")
     */
    public function patchApiUser(User $user, Request $request, ValidatorInterface $validator)
    {
        // Normalement, le tableau d'attributs ne se ferait pas ici. On opterait plutôt
        // pour la création d'un Provider qui fournirait à l'application le tableau des attributes
        $attributes = ['firstname' => 'setFirstname',
                        'lastname' => 'setLastname',
                        'email' => 'setEmail'];
        foreach($attributes as $attributeName => $setterName) {
            if (is_null($request->get($attributeName))) {
                continue;
            }
            $user->$setterName($request->request->get($attributeName));
        }

        $validationErrors = $validator->validate($user);
        if($validationErrors->count() > 0) {
            /** @var ConstraintViolation $constraintViolation */
            foreach ($validationErrors as $constraintViolation) {
                // Returns the violation message. (Ex. This value should not be blank.)
                $message = $constraintViolation->getMessage();
                // Returns the property path from the root element to the violation. (Ex. lastname)
                $propertyPath = $constraintViolation->getPropertyPath();
                $errors[] = ['message' => $message, 'propertyPath' => $propertyPath];
            }
        }
        if (!empty($errors)) {
            // Throw a 400 Bad Request with all errors messages (Not readable, you can do better)
            throw new BadRequestHttpException(json_encode( $errors));
        }
        //dd($validator);

        $this->entityManager->flush();
        return $this->view($user);
    }
    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Delete("/api/users/{email}")
     */
    public function deleteApiUser(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new Response(null,204);
    }

    /**
     * @Rest\Get("/api/admin/users")
     */
    public function adminApiUsers()
    {
        $users = $this->userRepository->findAll();
        return $this->view($users);
    }
}
