<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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

    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        $this->dispatch('refresh-notification');
    }

    public function render()
    {
        return view('livewire.notification.index', [
            'notifications' => auth()->user()
                ->notifications()
                ->latest()
                ->paginate(15)
        ])->layout('layouts.app', [
            'title' => 'Notifications',
            'subtitle' => 'All system notifications'
        ]);
    }
}