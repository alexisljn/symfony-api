<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    // Norme pour nommer les évènements : Entité concernée.Action effectuée
    const NAME = "user.registered";

    protected $user;

    public function  __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}