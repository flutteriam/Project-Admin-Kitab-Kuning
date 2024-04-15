@foreach ($babs as $bab)
    <br>
    <h4>Bab {{ $bab->order }}</h4>
    <div class="d-flex m-t-20">
        <div class="d-flex flex-column" style="width: 100%;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="alert alert-success dark m-l-10 m-r-10" role="alert">
                    {{ $bab->title }}
                </div>
                <hr style="flex-grow: 1;">
                <div class="d-flex justify-content-between m-l-10">
                    <button type="button" class="btn btn-warning" onclick="editData(`{{ $bab->id }}`, this)"><i
                            class="fa fa-pencil"></i></button>
                    <button type="button" class="btn btn-danger m-l-5"
                        onclick="deleteData(`{{ $bab->id }}`, this)"><i class="fa fa-trash"></i></button>
                </div>
            </div>

            @foreach ($bab->chapters as $k_chapter => $chapter)
                <div class="d-flex align-items-center m-l-10 m-t-10">
                    <h5 style="display: inherit;">
                        Bait/Kalimat {{ $chapter->order }}
                    </h5>

                    <div class="alert alert-light m-b-0 m-l-10" style="" role="alert" style="width: 100%;"
                        ondblclick="editTranslate(`{{ $chapter->id }}`)">
                        <div id="text-chapter-{{ $chapter->id }}">{!! $chapter->translate !!}</div>
                        <div id="form-chapter-{{ $chapter->id }}" style="display:none;">
                            <textarea name="translate" class="form-control" id="input-chapter-{{ $chapter->id }}"
                                placeholder="Masukkan Translate Bait" cols="30" rows="5" data-id="{{ $chapter->id }}"
                                data-bab-id="{{ $chapter->bab_id }}" data-book-id="{{ $chapter->book_id }}">{!! $chapter->translate !!}</textarea>
                            <button class="btn btn-primary mt-3"
                                onclick="saveEditBait(`#input-chapter-{{ $chapter->id }}`)">Simpan</button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between m-l-10">
                        <button type="button" class="btn btn-warning"
                            onclick="editDataBait(`{{ $chapter->id }}`, {{ $bab->chapters->count() }}, this)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-danger m-l-5"
                            onclick="deleteDataBait(`{{ $chapter->id }}`, this)"><i class="fa fa-trash"></i></button>
                    </div>
                </div>

                <div class="m-l-10 m-t-10 p-10" style="width: 100%; border: 2px dashed #c4c4c4;">
                    <ul class="d-flex flex-row-reverse flex-wrap sortable" data-chapter-id="{{ $chapter->id }}"
                        data-bab-id="{{ $chapter->bab_id }}" data-book-id="{{ $chapter->book_id }}">
                        @foreach ($chapter->words as $kata)
                            <li id="kata-{{ $kata->id }}"
                                class="arab text-center m-3 float-right kata ui-state-default badge-kata"
                                style="border-radius: 30px;padding: 10px; background-color: #fff6e3; cursor: pointer;"
                                ondblclick="editDataWord(this)" data-id="{{ $kata->id }}"
                                data-order="{{ $kata->order }}" data-chapter-id="{{ $kata->chapter_id }}"
                                data-bab-id="{{ $kata->bab_id }}" data-book-id="{{ $kata->book_id }}">
                                <h1 class="arab text-dark harokat" style="display: none;">{{ $kata->arab_harokat }}
                                </h1>
                                <h1 class="arab text-dark nonharokat">{{ $kata->arab }}</h1>
                                {!! $kata->translate_word !!}
                            </li>
                        @endforeach

                        <div class="d-flex m-1 float-right add-kata">
                            <i class="fa fa-plus-circle txt-success align-self-center"
                                style="font-size: 50px; cursor: pointer;"
                                onclick="addDataWord(`{{ $bab->id }}`, `{{ $chapter->id }}`, this)"></i>
                        </div>
                    </ul>
                </div>

                <hr style="width:100%;border:1px solid rgba(0, 0, 0, 0.5)" />
            @endforeach

            <div class="d-flex align-items-center m-l-10 m-t-10">
                <button type="button" class="btn btn-danger m-r-5"
                    onclick="addDataBait(`{{ $bab->id }}`, this)"><i class="fa fa-plus-circle"></i> Tambah
                    Bait/Kalimat</button>
            </div>
        </div>
    </div>
@endforeach

<script>
    $(document).ready(() => {
        $('.kata p').addClass('text-black-50 text-terjemahan teksTerjemah')
        $(".sortable").disableSelection();
        $(".sortable").sortable({
            update: function(event, ui) {
                let {
                    babId,
                    chapterId,
                    bookId
                } = $(this)[0].dataset;
                let data =
                    `${$(this).sortable('serialize')}&babId=${babId}&chapterId=${chapterId}&bookId=${bookId}`;

                new Promise((resolve, reject) => {
                    let url = `{{ route('bab.sort', ['id' => ':id']) }}`
                    url = url.replace(':id', babId)

                    $axios.defaults.headers.common['X-CSRF-TOKEN'] = `{{ csrf_token() }}`;
                    $axios.put(`${url}`, data).then(res => {
                        console.log(res)
                    }).catch(err => console.log(err.message))
                });

            }
        });

        let elements = document.getElementsByClassName("badge-kata");

        Array.from(elements).forEach(element => {
            element.addEventListener('contextmenu', event => {
                event.preventDefault();

                let {
                    clientX: mouseX,
                    clientY: mouseY
                } = event;

                contextMenu.style.top = `${mouseY}px`
                contextMenu.style.left = `${mouseX}px`

                contextMenu.classList.add('visible');

                $("#btn-delete-word").attr("data-kata_id", element.dataset.id)
                $("#btn-duplicate-word").attr('data-id', element.dataset.id)
                $("#btn-edit-word").attr('data-id', element.dataset.id)
            });
        });

        let scope = document.querySelector('body');
        scope.addEventListener('click', e => {
            if (e.target.offsetParent != contextMenu) {
                contextMenu.classList.remove("visible");
            }
        })
    })
</script>
