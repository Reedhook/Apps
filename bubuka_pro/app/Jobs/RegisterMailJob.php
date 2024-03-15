<?php

namespace App\Jobs;

use App\Mail\User\PasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RegisterMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;
    protected $password;

    public function __construct(array $data, string $password)
    {
        $this->data = $data;
        $this->password = $password;
    }

    public function handle(): void
    {
        Mail::to($this->data['email'])->send(new PasswordMail($this->password));
    }
}
