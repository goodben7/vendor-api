<?php 

namespace App\MessageHandler\Command;

use App\Entity\User;
use App\Manager\UserManager;
use Psr\Log\LoggerInterface;
use App\Model\UpdateUserModel;
use App\Message\Command\UpdateUserCommand;
use App\Message\Command\CommandHandlerInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private UserManager $manager
    ) { 
    }

    /**
     * Summary of __invoke
     * @param \App\Message\Command\UpdateUserCommand $command
     * @throws \Exception
     * @return User
     */
    public function __invoke(UpdateUserCommand $command): User
    {
        try {
            $model = new UpdateUserModel(
                $command->email,
                $command->phone,
                $command->displayName,
                $command->userId,
            );

            return $this->manager->updateFrom($command->userId, $model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception('Error in UpdateUserCommandHandler: ' . $e->getMessage(), 0, $e);
        }
    }
}
