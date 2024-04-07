@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item mx-3 disabled" aria-disabled="true">
                    <span class="page-link">@lang('pagination.previous')</span>
                </li>
            @else
                <li class="page-item mx-3">
                    <a class="btn btn-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                </li>
            @endif

            @auth
                @php
                    $segment = request()->segment(2);
                    $suratPost_id = explode('&', request()->segment(3))[0];
                    if($segment == 'alquran') {
                        $pageUser = PageUser::where('user_id', auth()->id())->where('type', 'alquran')->where('surat_id', $suratPost_id)->where('page', request()->page ?? 1)->first();
                    } else {
                        $suratPost_id = request()->segment(4);
                    }
                @endphp
                @if ($pageUser)
                    <button class="btn btn-secondary" id="btn-page" onclick="pageUser(`{{ request()->page ?? 1 }}`, `{{ $segment }}`, `{{ $suratPost_id }}`, 'remove', this)"><i class="fa fa-book"></i> ({{ request()->page ?? 1 }}) <i class="fa fa-bookmark"></i> Hapus</button>
                @else
                    <button class="btn btn-warning" id="btn-page" onclick="pageUser(`{{ request()->page ?? 1 }}`, `{{ $segment }}`, `{{ $suratPost_id }}`, 'store', this)"><i class="fa fa-book"></i> ({{ request()->page ?? 1 }}) <i class="fa fa-bookmark"></i> Simpan</button>
                @endif
            @endauth

            @guest
                <button onclick="window.location.replace(`{{ route('admin.login') }}`)" class="btn btn-warning" ><i class="fa fa-book"></i> ({{ request()->page ?? 1 }}) <i class="fa fa-bookmark"></i> Simpan</button>
            @endguest

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item mx-3">
                    <a class="btn btn-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                </li>
            @else
                <li class="page-item mx-3 disabled" aria-disabled="true">
                    <span class="page-link">@lang('pagination.next')</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
