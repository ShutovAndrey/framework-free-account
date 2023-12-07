<?php

namespace App\Actions;

use App\Models\Gift;
use App\Models\PaymentData;
use App\Services\PaymentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MassPaymentAction extends Command
{
    private PaymentService $payment;

    public function __construct(PaymentService $payment)
    {
        parent::__construct();
        $this->payment = $payment;

    }

    protected function configure()
    {
        $this->setName('payment')
            ->setDescription('Mass payment for users!')
            ->addArgument('quantity', InputArgument::REQUIRED, 'Pass the users quantity.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersGifts = Gift::where('confirmed', false)
            ->leftJoin('users', 'users.id', '=', 'users_gifts.user_id')
            ->addSelect(['users_gifts.id as gift_id', 'amount', 'users.id as user_id', 'bank_account'])
            ->take($input->getArgument('quantity'))
            ->get()
        ;

        foreach ($usersGifts as $gift) {
            $this->payment->create(new PaymentData(
                $gift->user_id,
                $gift->gift_id,
                $gift->amount,
                $gift->bank_account
            ));

            Gift::firstWhere('id', $gift->gift_id)->update(['confirmed' => true]);
        }
        $output->writeln('Ok');

        return 1;
    }
}
