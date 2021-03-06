<?php

namespace Oro\Bundle\PhpUnitBundle\EventListener;

use Oro\Bundle\UserBundle\Command\UpdateUserCommand;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserManager;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Update user in test environment after it was changed by --user-password --user-email options
 */
class UpdateUserCommandEventListener
{
    /** @var UserManager */
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if ($event->getCommand() instanceof UpdateUserCommand) {
            $input = $event->getInput();
            $userName = $input->getArgument('user-name');
            if ($userName !== 'admin') {
                return;
            }

            if ($input->hasOption('user-name')) {
                $userName = $input->getOption('user-name');
            }

            /** @var User $user */
            $user = $this->userManager->findUserByUsername($userName);
            $user
                ->setUsername('admin')
                ->setPlainPassword('admin')
                ->setFirstName('John')
                ->setLastName('Doe')
                ->setEmail('admin@example.com')
                ->setSalt('');

            $this->userManager->updateUser($user);
        }
    }
}
