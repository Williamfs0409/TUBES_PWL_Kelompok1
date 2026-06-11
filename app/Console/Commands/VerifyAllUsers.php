<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyAllUsers extends Command
{
    protected $signature = 'users:verify-all';
    protected $description = 'Mark all existing unverified users as email-verified';

    public function handle(): int
    {
        $count = User::whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);

        $this->info("Done! {$count} user(s) marked as verified.");

        return self::SUCCESS;
    }
}
