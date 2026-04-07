<div class="row justify-content-center push">
    <div class="col-md-8 col-lg-6 col-xl-4">

        <div class="block block-rounded shadow-none mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">Reset Password</h3>
            </div>

            <div class="block-content">
                <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">
                    <p class="fs-sm text-muted">
                        Masukkan email dan password baru untuk reset langsung.
                    </p>

                    <form wire:submit.prevent="resetPassword">

                        <div class="mb-4">
                            <input type="email"
                                   wire:model="email"
                                   class="form-control form-control-lg form-control-alt"
                                   placeholder="Email">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <input type="password"
                                   wire:model="password"
                                   class="form-control form-control-lg form-control-alt"
                                   placeholder="Password baru">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <input type="password"
                                   wire:model="password_confirmation"
                                   class="form-control form-control-lg form-control-alt"
                                   placeholder="Konfirmasi password baru">
                        </div>

                        <button type="submit" class="btn w-100 btn-alt-primary mb-3">
                            Simpan Password
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="fs-sm fw-medium">
                                Kembali ke login
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
