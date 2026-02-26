<div class="row justify-content-center push">
    <div class="col-md-8 col-lg-6 col-xl-4">

        <div class="block block-rounded shadow-none mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">Login</h3>
            </div>

            <div class="block-content">
                <div class="p-sm-3 px-lg-4 px-xxl-5 py-lg-5">

                    <form wire:submit.prevent="login">

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
                                   placeholder="Password">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn w-100 btn-alt-success">
                            Login
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>