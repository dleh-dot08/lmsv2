<x-guest-layout>
    <h4 class="mb-2 text-center">Lupa Kata Sandi?</h4>
    <p class="mb-4 text-center">Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                placeholder="Masukkan email Anda" 
                value="{{ old('email') }}" 
                required 
                autofocus
            >
        </div>

        <button type="submit" class="btn btn-warning d-grid w-100 text-white">
            Kirim Tautan Reset
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}">
            <i class="bx bx-chevron-left scaleX-n1-rtl"></i> Kembali ke login
        </a>
    </div>
</x-guest-layout>
