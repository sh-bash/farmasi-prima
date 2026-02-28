<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class NotificationDropdown extends Component
{
    protected $listeners = [
        'refresh-notification' => '$refresh'
    ];
    public $unreadCount = 0;

    public function hydrate()
    {
        // prevent unwanted hydration
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        $this->dispatch('refresh-notification');
    }

    public function openNotification($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->find($id);

        if ($notification) {

            $notification->markAsRead();

            $this->dispatch('refresh-notification');

            return redirect($notification->data['url']);
        }
    }

    public function mount()
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function render()
    {
        $currentUnread = auth()->user()->unreadNotifications()->count();

        if ($currentUnread > $this->unreadCount) {
            $this->dispatch('play-sound');
        }

        $this->unreadCount = $currentUnread;
        return view('livewire.layout.notification-dropdown');
    }
}