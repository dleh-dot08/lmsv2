<x-guest-layout>
  <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <div class="card">
            <div class="card-body">
              {{-- <div class="app-brand justify-content-center mb-1">
                  <a href="/" class="app-brand-link">
                      <img src="/assets/img/icons/logo-anagata-academy.png" alt="Logo Aplikasi" class="app-brand-logo" style="max-width: 200px;">
                      </a>
              </div> --}}
              <h4 class="mb-3 text-center">Selamat Datang Kembali! 👋</h4>
              <p class="mb-4 text-center text-muted">Silakan login untuk melanjutkan.</p>

              <form id="formAuthentication" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="mb-3 form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="form-label">Email</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="anagata@gmail.com"
                        required
                        autocomplete="email"
                        autofocus
                    />
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3 form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-secondary text-sm" href="{{ route('password.request') }}">
                                {{ __('Lupa Password?') }}
                            </a>
                        @endif
                    </div>
                    <div class="input-group input-group-merge">
                        <input
                            type="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password"
                            required
                            autocomplete="current-password"
                        />
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember-me" {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                  </div>
                  </div>

                <div class="mb-4"> <button class="btn btn-primary d-grid w-100" type="submit">Log In</button>
                </div>
              </form>

              <p class="text-center">
                <span>Belum punya akun?</span>
                <a href="{{ route('register') }}" class="text-primary fw-semibold">
                  <span>Daftar Sekarang</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
</x-guest-layout>
<style>
  /* Custom CSS untuk halaman login */

/* Branding Logo Styling */
.app-brand-logo {
    max-width: 150px; /* Sesuaikan ukuran logo */
    height: auto;
    display: block; /* Agar margin auto berfungsi untuk centering */
    margin: 0 auto; /* Tengahkankan logo */
}

/* Judul dan Sub-judul */
.authentication-inner h4 {
    font-weight: 700; /* Lebih tebal */
    color: #333; /* Warna yang lebih gelap */
}

.authentication-inner p.text-muted {
    font-size: 0.95rem;
    color: #666; /* Sedikit lebih gelap dari text-muted default */
}

/* Form Group Enhancements */
.form-group.has-error .form-control.is-invalid {
    border-color: #dc3545; /* Bootstrap danger color */
}

.invalid-feedback {
    display: block; /* Memastikan pesan error selalu terlihat */
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

/* Button Styling */
.btn-primary {
    /* Sesuaikan warna dan shadow jika perlu */
    background-color: #007bff;
    border-color: #007bff;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); /* Contoh shadow saat hover */
}

/* Link Styling */
.text-secondary {
    color: #6c757d !important; /* Pastikan warnanya konsisten */
    font-weight: 500;
}

.text-secondary:hover {
    color: #007bff !important; /* Warna hover yang konsisten */
    text-decoration: underline;
}

.text-primary {
    font-weight: 600; /* Sedikit lebih tebal dari default */
    color: #007bff !important;
}
.text-primary:hover {
    text-decoration: underline;
}

/* Card Shadow (opsional) */
.card {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Berikan shadow yang lebih halus */
}
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const togglePassword = document.querySelector('.input-group-text.cursor-pointer');

    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        });
    }
});
</script>