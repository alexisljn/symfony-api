<?php


namespace App\Subscriber;


use App\Event\UserRegisteredEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisteredEvent::NAME => 'onUserRegisteredEvent',
        ];
    }

    public function onUserRegisteredEvent(UserRegisteredEvent $userRegisteredEvent)
    {
        $user = $userRegisteredEvent->getUser();
        $this->logger->info($user->getFirstname()." ".$user->getLastname()." avec l'adresse email ".$user->getEmail()
        ." vient de s'inscrire");

    }
}