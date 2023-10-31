<?php

namespace App\Livewire\Auth\Password;

use App\Models\User;
use App\Notifications\PasswordRecoveryNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Recovery extends Component
{
    #[Rule(['required', 'email'])]
    public $email = null;

    public $message = null;

    #[Layout('components.layouts.guest')]
    public function render(): View
    {
        return view('livewire.auth.password.recovery');
    }

    public function requestPasswordRecovery(): void
    {
        $this->validate();

        $user = User::whereEmail($this->email)->first();

        if ($user) {
            $this->message = 'You will receive an email with a link to reset your password.';
            $user->notify(new PasswordRecoveryNotification());
        }
    }
}
