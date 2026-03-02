<script src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery (WAJIB untuk Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@livewireScripts

{{-- GLOBAL LIVEWIRE LOADING --}}
<div id="global-loading" class="lw-loading-overlay" style="display:none;">
    <div class="lw-loading-box">
        <span class="spinner-border text-primary"></span>
        <div class="mt-2 fw-semibold">Processing...</div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {

        let loader = document.getElementById('global-loading');
        let counter = 0;

        Livewire.hook('request', (request) => {

            let isNotificationPoll = false;

            try {
                // 🔥 payload adalah STRING
                let body = JSON.parse(request.payload);

                let component = body?.components?.[0];

                if (component) {

                    // Parse snapshot untuk ambil nama component
                    let snapshot = JSON.parse(component.snapshot);
                    let componentName = snapshot?.memo?.name;

                    // Cek apakah ini notification dropdown
                    if (componentName === 'layout.notification-dropdown') {

                        // Pastikan ini polling ($refresh)
                        let calls = component?.calls ?? [];

                        let isPoll = calls.some(call =>
                            call.method === '$refresh'
                            && call.metadata?.type === 'poll'
                        );

                        if (isPoll) {
                            isNotificationPoll = true;
                        }
                    }
                }

            } catch (e) {
                console.warn('Livewire parse error:', e);
            }

            // 🚫 Skip loader hanya untuk polling notification
            if (isNotificationPoll) {
                return;
            }

            counter++;
            loader.style.display = 'flex';

            let done = () => {
                counter--;
                if (counter <= 0) {
                    loader.style.display = 'none';
                }
            };

            request.respond(done);
            request.succeed(done);
            request.fail(done);
        });


    });
</script>

<script>
document.addEventListener('livewire:init', () => {

    Livewire.on('swal', (data) => {
        Swal.fire({
            icon: data.icon ?? 'success',
            title: data.title ?? 'Success',
            text: data.text ?? '',
            timer: 2000,
            showConfirmButton: false
        });
    });

    Livewire.on('swal-confirm', (data) => {
        Swal.fire({
            title: data.title ?? 'Are you sure?',
            text: data.text ?? '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.first().call('delete');
            }
        });
    });

});
</script>

<script>
    function markNotificationRead(id) {

        fetch('/notification/read/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

    }

    Livewire.on('closeProductModal', () => {
        const modalEl = document.getElementById('quickProductModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    });
</script>

<script>
    window.addEventListener('play-sound', () => {
        new Audio('/notification.mp3').play();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function initSelect2() {

            // Destroy dulu agar tidak double init
            // $('.select2-category').select2('destroy');
            // $('.select2-form').select2('destroy');
            // $('.select2-ingredient').select2('destroy');

            $('.select2-category').select2({
                dropdownParent: $('#quickProductModal'),
                width: '100%'
            }).on('change', function () {
                Livewire.dispatch('setCategory', this.value)
            });

            $('.select2-form').select2({
                dropdownParent: $('#quickProductModal'),
                width: '100%'
            }).on('change', function () {
                Livewire.dispatch('setForm', this.value)
            });

            initIngredients();
        }

        const modalEl = document.getElementById('quickProductModal');

        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function () {
                setTimeout(initSelect2, 100);
            });
        }

        document.addEventListener('livewire:update', function () {
            setTimeout(initSelect2, 100);
        });


    });

    function initIngredients() {

        document.querySelectorAll('.select2-ingredient').forEach(function(el){

            // destroy kalau sudah ada
            if ($(el).data('select2')) {
                $(el).select2('destroy');
            }

            $(el).select2({
                dropdownParent: $('#quickProductModal'),
                width: '100%'
            });

            $(el).on('change', function(){

                let index = this.dataset.index;

                Livewire.dispatch('setIngredient', {
                    index: index,
                    value: this.value
                });

            });

        });

    }

    Livewire.on('reinit-ingredients', () => {
        setTimeout(initIngredients, 100);
    });
</script>

@stack('scripts')