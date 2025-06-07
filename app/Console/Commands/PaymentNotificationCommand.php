<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use App\Repositories\PaymentProviderRepository;

class PaymentNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment:notification {payment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        $payment = Payment::find($paymentId);
        if ($payment) {
            sleep(30);
            $providerService = PaymentProviderRepository::getProvider($payment);
            $status = $providerService::updateStatus($payment);
            if ($status) {
                $this->info("\nThe Payment status " . $payment->id . " is: " . $status . " ...\n");
            }
        }
    }
}
