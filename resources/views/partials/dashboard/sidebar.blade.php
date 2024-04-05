<div class="page-sidebar custom-scrollbar">
    <div class="sidebar-user text-center">
        <div>
            <img class="img-50 rounded-circle" src="{{ asset('images/logo-login.png') }}" alt="#">
        </div>
        <h6 class="mt-3 f-12">{{ Auth::user()->name }}</h6>
    </div>
    <ul class="sidebar-menu">
        <li class="{{ request()->segment(1) == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-header">
                <i class="icon-desktop"></i><span>Dashboard</span>
            </a>
        </li>
        @if (Auth::user()->type == 0 || Auth::user()->type == 1)
            <li class="{{ request()->segment(2) == 'category' ? 'active' : '' }}">
                <a href="{{ route('category.index') }}" class="sidebar-header">
                    <i class="icon-list"></i><span>Kategori</span>
                </a>
            </li>
            <li class="{{ request()->segment(2) == 'post' ? 'active' : '' }}">
                <a href="{{ route('post.index') }}" class="sidebar-header">
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
            <h6 class="mb-2 fs-14">Need Help</h6>
            <i class="icon-bell"></i>
        </div>
        <div class="sidebar-widget-bottom p-20 m-20">
            <p>+1 234 567 899
                <br>help@pixelstrap.com
                <br><a href="#">Visit FAQ</a>
            </p>
        </div>
    </div>
</div>