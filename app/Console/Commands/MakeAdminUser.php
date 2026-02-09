<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminUser extends Command
{
    protected $signature = 'orewire:make-admin {email : The user email to grant admin access}';

    protected $description = 'Grant dashboard admin access to a user by email (sets is_admin=true)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("User not found: {$email}");

            return self::FAILURE;
        }

        if ($user->is_admin) {
            $this->info("User {$email} already has admin access.");

            return self::SUCCESS;
        }

        $user->update(['is_admin' => true]);
        $this->info("Granted admin access to {$email}.");

        return self::SUCCESS;
    }
}
