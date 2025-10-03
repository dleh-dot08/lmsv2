{{-- Asumsi variabel $currentRoleName dikirim dari ComposerServiceProvider --}}
@php
    // Fallback jika $currentRoleName tidak terdefinisi (hanya untuk keamanan)
    $roleName = $currentRoleName ?? 'Role Tidak Dikenal';
@endphp

<style>
    /* Styling Kustom */
    .navbar-text-main {
        font-family: 'Public Sans', sans-serif;
        font-size: 1.25rem; /* 20px */
        font-weight: 700; /* Bold */
        color: #6d7de5; /* Warna Primer */
    }

    .navbar-text-role {
        font-family: 'Public Sans', sans-serif;
        font-size: 0.9rem; /* 14.4px */
        font-weight: 600;
        color: #acacac; /* Warna Sekunder/Abu-abu */
        margin-left: 0.5rem;
        padding: 2px 8px;
        border-radius: 5px;
        background-color: rgba(172, 172, 172, 0.1);
    }
    
    /* Tombol Logout */
    .btn-logout {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 15px;
        border-radius: 50rem; /* pill shape */
        font-weight: 600;
        transition: background-color 0.3s;
    }

    /* Padding Nav (disesuaikan dengan template Anda) */
    .navbar {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
</style>

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    
    {{-- Tombol Toggle Sidebar (untuk mobile/tablet) --}}
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        
        <div class="d-flex align-items-center me-auto">
            {{-- Nama Pengguna --}}
            <span class="navbar-text-main d-none d-sm-block">
                Halo, <b>{{ Auth::user()->name }}</b>
            </span>
            
            {{-- Nama Role --}}
            <span class="navbar-text-role">
                {{ $roleName }}
            </span>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            
            {{-- Tombol Logout --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    title="Logout">
                    
                    <span class="btn btn-primary btn-logout">
                        <i class="bx bx-power-off me-1"></i>
                        <span class="d-none d-sm-inline">Log Out</span>
                    </span>
                </a>
            </li>
            
            {{-- Form Logout (Tersembunyi) --}}
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
</nav>