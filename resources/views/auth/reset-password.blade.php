<x-guest-layout>
    <h4 class="mb-2 text-center">Atur Ulang Kata Sandi</h4>
    <p class="mb-4 text-center">Silakan masukkan kata sandi baru Anda di bawah ini.</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                value="{{ old('email', $request->email) }}" 
                required 
                autofocus 
                autocomplete="username"
            >
        </div>

        <div class="mb-3 form-password-toggle">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <div class="input-group input-group-merge">
                <input 
                    type="password" 
                    id="password" 
                    class="form-control" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                >
            </div>
        </div>

        <div class="mb-3 form-password-toggle">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <div class="input-group input-group-merge">
                <input 
                    type="password" 
                    id="password_confirmation" 
                    class="form-control" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                >
            </div>
        </div>

        <button type="submit" class="btn btn-warning d-grid w-100 text-white">
            Reset Password
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}">
            <i class="bx bx-chevron-left scaleX-n1-rtl"></i> Kembali ke login
        </a>
    </div>
</x-guest-layout>
