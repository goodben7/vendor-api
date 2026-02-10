<?php 

namespace App\MessageHandler\Command;


use App\Entity\Payment;
use Psr\Log\LoggerInterface;
use App\Model\NewPaymentModel;
use App\Manager\PaymentManager;
use App\Message\Command\CreatePaymentCommand;
use App\Message\Command\CommandHandlerInterface;

class CreatePaymentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private PaymentManager $manager
    ) { 
    }

    /**
     * Summary of __invoke
     * @param \App\Message\Command\CreatePaymentCommand $command
     * @throws \Exception
     * @return Payment
     */
    public function __invoke(CreatePaymentCommand $command): Payment
    {
        try {
            $model = new NewPaymentModel(
                $command->order,
                $command->amount,
                $command->method,
                $command->provider,
                $command->transactionRef,
            );

            return $this->manager->createPayment($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \Exception('Error in CreatePaymentCommandHandler: ' . $e->getMessage(), 0, $e);
        }
    }
}