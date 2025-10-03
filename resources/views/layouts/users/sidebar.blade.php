    @php
        use App\Models\User; // Import Model User untuk mengakses konstanta role ID
        $roleId = Auth::user()->role_id;
    @endphp

    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <ul class="menu-inner py-1">
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard</div>
                </a>
            </li>
            
            @if ($roleId == User::ID_SUPER_ADMIN || $roleId == User::ID_ADMIN)
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Master Data & Manajemen</span>
                </li>
            @endif
            
            {{-- 1. Menu Hanya untuk Super Admin & Admin --}}
            @if ($roleId == User::ID_SUPER_ADMIN || $roleId == User::ID_ADMIN)
                <li class="menu-item {{ request()->routeIs('users.*', 'jenjang.*', 'kategori.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-group"></i>
                        <div data-i18n="Data Manajemen">Manajemen Pengguna</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <div data-i18n="List Pengguna">Semua Pengguna</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.sekolah.*') ? 'active' : '' }}">
                            <a href="{{ route('schools.index') }}" class="menu-link">
                                <div data-i18n="Sekolah">Data Sekolah</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- 2. Menu Khusus Mentor dan Admin/Super Admin --}}
            @if ($roleId == User::ID_SUPER_ADMIN || $roleId == User::ID_ADMIN || $roleId == User::ID_MENTOR)
                <li class="menu-item {{ request()->routeIs('materi.*', 'kelas.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-book"></i>
                        <div data-i18n="Materi">Manajemen Konten</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('materi.index') ? 'active' : '' }}">
                            <a href="##" class="menu-link">
                                <div data-i18n="Materi Kursus">Materi Kursus</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                            <a href="##" class="menu-link">
                                <div data-i18n="Kelas">Data Kelas</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- 3. Menu Khusus Peserta --}}
            @if ($roleId == User::ID_PESERTA)
                <li class="menu-item {{ request()->routeIs('my-course.*') ? 'active' : '' }}">
                    <a href="##" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-chalkboard"></i>
                        <div data-i18n="Kelas Saya">Kelas Saya</div>
                    </a>
                </li>
            @endif
            
            {{-- 4. Menu Khusus Karyawan --}}
            @if ($roleId == User::ID_KARYAWAN)
                <li class="menu-item {{ request()->routeIs('laporan-internal.*') ? 'active' : '' }}">
                    <a href="##" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Laporan">Laporan Internal</div>
                    </a>
                </li>
            @endif

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pengaturan</span>
            </li>
            <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <a href="##" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Profile">Profil Saya</div>
                </a>
            </li>

        </ul>
    </aside>

    <style>
        .app-brand {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-image {
            max-width: 100%;
            width: 80%;
            height: auto;
        }
    </style>
