<script src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        Livewire.hook('request', ({ respond, succeed, fail }) => {

            counter++;
            loader.style.display = 'flex';

            let done = () => {
                counter--;
                if (counter <= 0) {
                    loader.style.display = 'none';
                }
            };

            respond(done);
            succeed(done);
            fail(done);
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