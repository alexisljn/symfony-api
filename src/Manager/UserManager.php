<?php


namespace App\Manager;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getUsersByFirstname(string $firstname): ?array
    {
        return $this->userRepository->findBy(['firstname' =>$firstname], ['email' => 'asc']);
    }

    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
    }

    public function saveUser(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}