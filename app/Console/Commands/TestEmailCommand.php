<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Mail\BaseMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email {email}';

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
        $recipientEmail = $this->argument('email');
        try {
            SendEmailJob::dispatch(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'), '<h1>HELLO WORLD</h1>', 'Test Title', $recipientEmail, 1);
        } catch (\Exception $e) {
            dd($e->getTraceAsString());
        }
    }
}
