<div class="page-sidebar custom-scrollbar">
    <div class="sidebar-user text-center">
        <div>
            <img class="img-50 rounded-circle" src="{{ asset('images/logo-login.png') }}" alt="#">
        </div>
        <h6 class="mt-3 f-12">{{ Auth::user()->name }}</h6>
    </div>
    <ul class="sidebar-menu">
        <li class="{{ request()->segment(1) == '' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sidebar-header">
                <i class="icon-desktop"></i><span>Dashboard</span>
            </a>
        </li>
        @if (Auth::user()->type == 0 || Auth::user()->type == 1)
            <li class="{{ request()->segment(2) == 'kaategori' ? 'active' : '' }}">
                <a href="{{ route('kategori.index') }}" class="sidebar-header">
                    <i class="icon-list"></i><span>Kategori</span>
                </a>
            </li>
            <li class="{{ request()->segment(2) == 'kitab' ? 'active' : '' }}">
                <a href="{{ route('book.index') }}" class="sidebar-header">
                    <i class="icon-book"></i><span>Kitab</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->type == 0)
            <li class="{{ request()->segment(1) == 'user' ? 'active' : '' }}">
                {{-- <a href="{{ route('user.index') }}" class="sidebar-header">
                    <i class="icon-anchor"></i><span> Manajemen Users</span>
                </a> --}}
            </li>
        @endif
    </ul>
    <div class="sidebar-widget text-center">
        <div class="sidebar-widget-top">
            <h6 class="mb-2 fs-14">Butuh Bantuan?</h6>
            <i class="icon-bell"></i>
        </div>
        <div class="sidebar-widget-bottom p-20 m-20">
            <p>+62 852-3488-3488
                <br>Abdurrachman
            </p>
        </div>
    </div>
</div>
