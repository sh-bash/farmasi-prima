<div class="container-fluid mt-4">

    <div class="block block-rounded">
        <div class="block-header d-flex justify-content-between align-items-center">
            <h3 class="block-title">All Notifications</h3>

            @if(auth()->user()->unreadNotifications->count() > 0)
                <button class="btn btn-sm btn-primary"
                        wire:click="markAllAsRead">
                    Mark All as Read
                </button>
            @endif
        </div>

        <div class="block-content p-0">

            <ul class="nav-items">

                @forelse($notifications as $notification)

                    <li class="border-bottom">

                        <a href="{{ $notification->data['url'] }}"
                           class="text-dark d-flex py-3 px-3"
                           wire:click.prevent="markAsRead('{{ $notification->id }}')">

                            <div class="flex-shrink-0 me-3">
                                <i class="fa fa-fw fa-shopping-cart
                                   {{ $notification->read_at ? 'text-muted' : 'text-primary' }}">
                                </i>
                            </div>

                            <div class="flex-grow-1">

                                <div class="fw-semibold
                                     {{ $notification->read_at ? 'text-muted' : '' }}">
                                    {{ $notification->data['message'] }}
                                </div>

                                <div class="small text-muted">
                                    {{ $notification->created_at->format('d M Y H:i') }}
                                    • {{ $notification->created_at->diffForHumans() }}
                                </div>

                            </div>

                            @if(!$notification->read_at)
                                <span class="badge bg-danger ms-2">New</span>
                            @endif

                        </a>

                    </li>

                @empty

                    <li class="text-center py-5 text-muted">
                        No notifications found
                    </li>

                @endforelse

            </ul>

        </div>

        <div class="block-content">
            {{ $notifications->links() }}
        </div>

    </div>

</div>