<?php

namespace App\Command;

use App\Manager\ArticleManager;
use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCountArticlesCommand extends Command
{
    protected static $defaultName = 'app:user-count-articles';
    private $userManager;
    private $articleManager;

    public function __construct(UserManager $userManager, ArticleManager $articleManager)
    {
        $this->userManager = $userManager;
        $this->articleManager = $articleManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Count the number of articles for an user')
            ->addArgument('email', InputArgument::REQUIRED, 'User\'s email')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $user = $this->userManager->getUserByEmail($email);
        if($user) {
            $userId = $user->getId();
            $counter = $this->articleManager->countArticlesForUser($userId);
            $io->success(sprintf('L\'utilisateur %s a écrit %d articles',$user->getEmail(), $counter));

        } else {
            $io->error(sprintf('Cannot find such user with %s email', $email));
        }
    }
}
