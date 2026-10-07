<?php
namespace Database\States;

use App\Models\User;

class EnsureAdminSeeded
{
    public function __invoke(): void
    {
        if ($this->present()) {
            return;
        }

        User::factory()->create([
            'name' => 'saleh ahmed',
            'email' => 'salehaldhaheri09@gmail.com',
            'email_verified_at' => now(),
        ]);
    }

    private function present(): bool
    {
        return User::Where('email', 'salehaldhaheri09@gmail.com')->exists();
    }
}
