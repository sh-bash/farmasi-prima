<div class="dropdown d-inline-block ms-2"  wire:poll.5s>

    <button type="button"
            class="btn btn-sm btn-alt-secondary position-relative"
            data-bs-toggle="dropdown">

        <i class="fa fa-fw fa-bell"></i>

        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif

    </button>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 border-0 fs-sm">

        <div class="p-2 bg-body-light border-bottom text-center rounded-top">
            <h5 class="dropdown-header text-uppercase">
                Notifications
            </h5>
        </div>

        <ul class="nav-items mb-0">

            @forelse(auth()->user()->unreadNotifications->take(7) as $notification)

                <li>
                    <a class="text-dark d-flex py-2"
                        href="#"
                        wire:click="openNotification('{{ $notification->id }}')">

                        <div class="flex-shrink-0 me-2 ms-3">
                            <i class="fa fa-fw fa-shopping-cart text-primary"></i>
                        </div>

                        <div class="flex-grow-1 pe-2">
                            <div class="fw-semibold">
                                {{ $notification->data['message'] }}
                            </div>
                            <span class="fw-medium text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                    </a>
                </li>

            @empty
                <li class="text-center py-3 text-muted">
                    No notifications
                </li>
            @endforelse

        </ul>

        <div class="p-2 border-top text-center">
            <a class="d-inline-block fw-medium" href="{{ route('notifications') }}">
                <i class="fa fa-fw fa-arrow-down me-1 opacity-50"></i>
                View All
            </a>
        </div>

    </div>
</div>