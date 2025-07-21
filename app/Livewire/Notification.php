<?php

namespace App\Livewire;

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class Notification extends Component
{
    #[Session]
    public array $messages = [];

    public ?string $current = null;
    public bool $show = false;

    #[On('echo:notifications,MessageReceived')]
    public function updatedMessage($payload)
    {
        $this->flashMessage($payload['message']);
    }

    public function flashMessage(string $message)
    {
        $this->messages[] = $message;
        $this->messages = array_slice($this->messages, -5);
        $this->current = $message;
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.notification');
    }
}
