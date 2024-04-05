@foreach ($babs as $bab)
    <div class="d-flex m-t-20">
        <h4>Bab</h4>

        <div class="d-flex flex-column" style="width: 100%;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="alert alert-success dark m-l-10 m-r-10" role="alert">
                        {!! $bab->translate_title !!}
                    </div>
                    <hr style="flex-grow: 1;">
                </div>

                {{-- @forelse ($collection as $item)

                @empty

                @endforelse --}}

                @php
                    $i = 1;
                @endphp
                @foreach ($bab->bait as $bait)
                    <div class="m-l-10 m-t-10 p-10" style="width: 100%; border: 2px dashed #c4c4c4;">
                        <div class="d-flex flex-row-reverse flex-wrap">
                            @foreach ($bait->kata as $kata)
                                <div class="badge m-1 float-right kata" style="border-radius: 30px;padding: 10px; background-color: #fff6e3; cursor: pointer;" onclick="editData({{ $kata->id }}, this)">
                                    <h1 class="arab text-dark harokat" style="display: none;">{{ $kata->arab_harokat }}</h1>
                                    <h1 class="arab text-dark nonharokat">{{ $kata->arab }}</h1>
                                    {!! $kata->translate_word !!}
                                    <h5><span class="badge badge-primary mt-2">{{ count($kata->vars) }} var</span></h5>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="m-l-10 m-t-10">
                        <h5>
                            Terjemahan Bait :
                            <div class="alert alert-warning dark m-l-5" role="alert" style="display: inline;padding: 0 6px;">{{ $i }}</div>
                        </h5>
                    </div>
                    <div class="alert alert-light m-b-0 m-l-10" role="alert" style="width: 100%;">
                        {!! $bait->translate_bait !!}
                    </div>
                    @php
                        $i ++;
                    @endphp
                @endforeach
        </div>
    </div>
@endforeach

<script>
$(document).ready(() => {
    $('.kata p').addClass('text-black-50 text-terjemahan teksTerjemah')
})
</script>
