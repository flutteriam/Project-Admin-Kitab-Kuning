@extends('layouts.master', ['title' => 'Bab'])

@section('css')
    <style>
        #context-menu {
            position: fixed;
            z-index: 10000;
            width: 150px;
            background: #fff;
            border-radius: 8px;
            display: none;
            box-shadow: 7px 10px 22px 1px rgba(0, 0, 0, 0.32);
            -webkit-box-shadow: 7px 10px 22px 1px rgba(0, 0, 0, 0.32);
            -moz-box-shadow: 7px 10px 22px 1px rgba(0, 0, 0, 0.32);
        }

        #context-menu.visible {
            display: block;
        }

        #context-menu .item {
            padding: 8px 10px;
            font-size: 15px;
            cursor: pointer;
        }

        #context-menu .item:hover {
            background: #f7f7f7;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    <div id="context-menu">
        <div class="item" id="btn-delete-word" data-kata_id="" onclick="deleteDataWord(this)"><i class="fa fa-trash"></i> Hapus
        </div>
        <div class="item" id="btn-duplicate-word" onclick="btnDuplicate(this)" data-id=""><i class="fa fa-copy"></i>
            Duplikat</div>
        <div class="item" id="btn-edit-word" onclick="editDataWord(this)" data-id=""><i class="fa fa-edit"></i> Edit
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="category" id="category" autocomplete="off" class="form-control">
                                        <option value="" selected disabled>== Pilih Kategori ==</option>
                                        @foreach ($categories as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ isset($selectedBook) && $selectedBook->category_id == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="kitab" id="kitab" autocomplete="off" class="form-control">
                                        <option value="" selected disabled>== Pilih Kitab ==</option>
                                        @if ($selectedBook)
                                            @foreach ($books as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $selectedBook->id == $value->id ? 'selected' : '' }}>
                                                    {{ $value->title }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-primary" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="container-detail"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-data" tabindex="-1" role="dialog" aria-labelledby="modal-dataLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-data-label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldId">
                    <input type="hidden" name="category" id="inputCategory">
                    <input type="hidden" name="book_id" id="inputKitab"
                        value="{{ $selectedBook != null ? $selectedBook->id : '' }}">
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="">Judul</label>
                            <input type="text" placeholder="Judul" name="title" id="fieldTitle" class="form-control">
                        </div>
                        <div class="form-group" id="orderForm" style="display:none">
                            <label for="">Urutan</label>
                            <br>
                            <select name="order" id="fieldOrder">
                                @for ($i = 1; $i <= $count; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Terjemahan Judul</label>
                            <textarea type="text" placeholder="Terjemahan Judul" name="translate_title" id="fieldTransTitle"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-secondary" id="btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- BAIT/KALIMAT --}}
    <div class="modal fade" id="modal-data-chapter" tabindex="-1" role="dialog" aria-labelledby="modal-dataLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-data-label-chapter"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data-chapter" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldIdBait">
                    <div class="modal-body">
                        <input type="hidden" name="category" id="fieldCategory" value="">
                        <input type="hidden" name="book_id" id="fieldKitab" value="">
                        <input type="hidden" name="bab_id" id="fieldBab" value="">
                        <div class="form-group">
                            <label for="">Terjemahan</label>
                            <textarea name="translate" id="fieldTitleBait" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group" id="orderFormBait" style="display:none">
                            <label for="">Urutan</label>
                            <br>
                            <select name="order" id="fieldOrderBait">
                                @for ($i = 1; $i <= $count; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <textarea name="description" id="fieldDescription" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-secondary" id="btn-submit-chapter">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- KATA --}}
    <div class="modal fade" id="modal-data-word" tabindex="-1" role="dialog" aria-labelledby="modal-dataLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-data-label-word"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data-word" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldIdWord">
                    <div class="modal-body">

                        <input type="hidden" name="category_id" id="fieldCategoryWord">
                        <input type="hidden" name="book_id" id="fieldKitabWord">
                        <input type="hidden" name="bab_id" id="fieldBabWord">
                        <input type="hidden" name="chapter_id" id="fieldBaitWord">

                        <div class="form-group">
                            <label for="">Arab</label>
                            <input type="text" name="arab" id="fieldArab" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="">Arab Harokat</label>
                            <input type="text" name="arab_harokat" id="fieldArabHarokat" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="">Terjemahan Kata</label>
                            <textarea name="translate" id="fieldTranslate" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label for="">Kata Dasar</label>
                            <textarea name="basic" id="fieldBasicWrod" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-secondary" id="btn-submit-word">Simpan</button>
                        <button type="button" class="btn btn-danger m-r-10" id="btn-del-word" data-kata_id=""
                            onclick="deleteDataWord(this)">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('vendor/ckeditor/styles.js') }}"></script>
    <script src="{{ asset('vendor/ckeditor/adapters/jquery.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        let contextMenu = document.getElementById('context-menu');

        let table
        let type
        $(document).ready(() => {
            get_bab_by_kitab($("#kitab").val(), 'container-detail')

            CKEDITOR.replace('translate_title')

            $("#tambah-data").on('click', () => {
                let kitab = $("#kitab").val()
                let category = $('#category').val()
                if (!kitab || !category) {
                    return $swal.fire('Gagal', 'Silahkan pilih kategori dan kitab terlebih dahulu', 'error')
                }
                $('#orderForm').css('display', 'none')
                $("#modal-data-label").html("Tambah Bab")
                $("#btn-submit").html("Simpan")
                // $("#form-data")[0].reset()
                CKEDITOR.instances['fieldTransTitle'].setData('')
                $("#fieldTitle").val('')
                $("#fieldTransTitle").val('')
                $("#fieldKitab").html(`<option selected disabled>== Pilih Kitab ==</option>`)
                type = 'POST'
                $("#modal-data").modal('show')
            })

            $("#fieldCategory").on('change', () => {
                loading('show', $("#fieldKitab"))
                get_kitab($("#fieldCategory").val(), 'fieldKitab')
                    .then(() => loading('hide', $("#fieldKitab")))
            })

            $("#category").on('change', () => {
                loading('show', $("#kitab"))
                get_kitab($("#category").val(), 'kitab')
                    .then(() => loading('hide', $("#kitab")))
            })

            $("#form-data").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                let FD = new FormData($("#form-data")[0])
                FD.set('translate_title', CKEDITOR.instances['fieldTransTitle'].getData())
                if (type == "POST") {
                    new Promise((resolve, reject) => {
                        $axios.post(`{{ route('bab.store') }}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data').modal('hide')
                                loading('hide', $(".modal-content"))

                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })

                                $("#kitab").trigger('change')
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                } else if (type == 'PUT') {
                    FD.append('_method', 'PUT')
                    new Promise((resolve, reject) => {
                        let url = `{{ route('bab.update', ['bab' => ':id']) }}`
                        url = url.replace(':id', $("#fieldId").val())
                        $axios.post(`${url}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data').modal('hide')
                                loading('hide', $(".modal-content"))
                                // table.ajax.reload()
                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })

                                $("#kitab").trigger('change')
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                }
            })

            $("#kitab").on('change', () => {
                get_bab_by_kitab($("#kitab").val(), 'container-detail')

                $("#inputCategory").val($("#category").val())
                $("#inputKitab").val($("#kitab").val())
            })
        })

        const get_bab_by_kitab = (id_kitab, element) => {
            return new Promise((resolve, reject) => {
                let url = `{{ route('bab.index', ['id' => ':id']) }}`
                url = url.replace(':id', id_kitab)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        $(`#${element}`).html(data.view)
                        resolve(data.view)
                        $('#fieldOrder').empty();

                        for (var i = 1; i <= data.count; i++) {
                            $('#fieldOrder').append($('<option>', {
                                value: i,
                                text: i
                            }));
                        }
                    })
                    .catch(err => {
                        reject(err)
                    })
            })
        }

        const get_kitab = (id_category, element) => {
            return new Promise((resolve, reject) => {
                let url = `{{ route('kitab.show', ['kitab' => ':id']) }}`
                url = url.replace(':id', id_category)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let kitab = data.data
                        let html = `<option value="" selected disabled>== Pilih Kitab ==</option>`
                        $.each(kitab, (i, e) => {
                            html += `<option value="${e.id}">${e.title}</option>`
                        })
                        $(`#${element}`).html(html)
                        resolve(kitab)
                    })
                    .catch(err => {
                        reject(err)
                    })
            })
        }

        const editData = (id, el) => {
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('bab.edit', ['bab' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let bab = data.data
                        $("#fieldId").val(bab.id)
                        $("#inputCategory").val(bab.book.category_id)
                        $("#inputKitab").val(bab.book.id)
                        $("#fieldTitle").val(bab.title)
                        $("#fieldOrder").val(bab.order)
                        $('#orderForm').css('display', 'block')
                        CKEDITOR.instances['fieldTransTitle'].setData(bab.translate_title)
                        type = 'PUT'
                        loading('hide', el)
                        $("#modal-data-label").html("Update Bab")
                        $("#btn-submit").html("Update")
                        $("#modal-data").modal('show')
                    })
            })
        }

        const deleteData = (id, el) => {
            $swal.fire({
                    title: 'Yakin ?',
                    text: "Ingin menghapus Bab ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Ini!',
                    cancelButtonText: 'Tidak'
                })
                .then(res => {
                    if (res.isConfirmed) {
                        loading('show', el)
                        new Promise((resolve, reject) => {
                            let url = `{{ route('bab.destroy', ['bab' => ':id']) }}`
                            url = url.replace(':id', id)
                            $axios.delete(`${url}`)
                                .then(({
                                    data
                                }) => {
                                    loading('hide', el)
                                    $swal.fire({
                                        icon: 'success',
                                        title: data.message.head,
                                        text: data.message.body
                                    })

                                    $("#kitab").trigger('change')

                                    // table.ajax.reload()
                                })
                        })
                    }
                })
        }

        const detailData = id => {
            let url = `{{ route('chapter.index', ['id' => ':id']) }}`
            url = url.replace(':id', id)
            window.location.href = `${url}`
        }
    </script>
    {{-- BAIT/KALIMAT --}}
    <script>
        let typeBait
        $(document).ready(() => {

            translate2 = document.getElementById('fieldTitleBait')
            description2 = document.getElementById('fieldDescription')
            CKEDITOR.replace(translate2)
            CKEDITOR.replace(description2)

            $("#form-data-chapter").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                if (typeBait == "POST") {
                    let FD = new FormData($("#form-data-chapter")[0])
                    FD.set('translate', CKEDITOR.instances['fieldTitleBait'].getData())
                    FD.set('description', CKEDITOR.instances['fieldDescription'].getData())
                    new Promise((resolve, reject) => {
                        $axios.post(`{{ route('chapter.store') }}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-chapter').modal('hide')
                                loading('hide', $(".modal-content"))

                                // table.ajax.reload()
                                $("#kitab").trigger('change')

                                CKEDITOR.instances['fieldTitleBait'].setData('')
                                CKEDITOR.instances['fieldDescription'].setData('')
                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                } else if (typeBait == 'PUT') {
                    let FD = new FormData($("#form-data-chapter")[0])
                    FD.set('translate', CKEDITOR.instances['fieldTitleBait'].getData())
                    FD.set('description', CKEDITOR.instances['fieldDescription'].getData())
                    FD.set('_method', 'PUT')
                    new Promise((resolve, reject) => {
                        let url = `{{ route('chapter.update', ['chapter' => ':id']) }}`
                        url = url.replace(':id', $("#fieldIdBait").val())
                        $axios.post(`${url}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-chapter').modal('hide')
                                loading('hide', $(".modal-content"))

                                $("#kitab").trigger('change')

                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                }
            })

        })

        const addDataBait = (bab_id, el) => {
            loading('show', el)

            $("#modal-data-label-chapter").html("Tambah Bait")
            $("#btn-submit-chapter").html("Simpan")
            $("#form-data-chapter")[0].reset()
            CKEDITOR.instances['fieldTitleBait'].setData('')
            CKEDITOR.instances['fieldDescription'].setData('')

            $("#fieldIdBait").val('')

            $('#orderFormBait').css('display', 'none')

            $("#fieldCategory").val($('#category').val())
            $("#fieldKitab").val($('#kitab').val())
            $("#fieldBab").val(bab_id)

            typeBait = 'POST'
            $("#modal-data-chapter").modal('show')
            loading('hide', el)
        }

        const editDataBait = (id, count, el) => {
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('chapter.edit', ['chapter' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let chapter = data.data.chapter
                        let category = data.data.category
                        let bab = data.data.bab
                        $("#fieldCategory").val(category)
                        $("#fieldKitab").val(chapter.book_id)
                        $("#fieldBab").val(chapter.bab_id)
                        $("#fieldIdBait").val(chapter.id)
                        $("#fieldOrderBait").val(chapter.order)
                        $('#orderFormBait').css('display', 'block')

                        $("#modal-data-chapter").modal('show')


                        $('#fieldOrderBait').empty();

                        for (var i = 1; i <= count; i++) {
                            $('#fieldOrderBait').append($('<option>', {
                                value: i,
                                text: i
                            }));
                        }

                        CKEDITOR.instances['fieldTitleBait'].setData(chapter.translate)
                        CKEDITOR.instances['fieldDescription'].setData(chapter.description)
                        typeBait = 'PUT'
                        $("#modal-data-label-chapter").html("Update Bait")
                        $("#btn-submit-chapter").html("Update")
                        loading('hide', el)
                    })
            })
        }

        const deleteDataBait = (id, el) => {
            $swal.fire({
                    title: 'Yakin ?',
                    text: "Ingin menghapus Bait ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Ini!',
                    cancelButtonText: 'Tidak'
                })
                .then(res => {
                    if (res.isConfirmed) {
                        loading('show', el)
                        new Promise((resolve, reject) => {
                            let url = `{{ route('chapter.destroy', ['chapter' => ':id']) }}`
                            url = url.replace(':id', id)
                            $axios.delete(`${url}`)
                                .then(({
                                    data
                                }) => {
                                    loading('hide', el)
                                    $swal.fire({
                                        icon: 'success',
                                        title: data.message.head,
                                        text: data.message.body
                                    })

                                    // table.ajax.reload()
                                    $("#kitab").trigger('change')
                                })
                        })
                    }
                })
        }

        const detailDataBait = id => {
            let url = `{{ route('word.index', ['id' => ':id']) }}`
            url = url.replace(':id', id)
            window.location.href = `${url}`
        }
    </script>
    {{-- KATA --}}
    <script>
        let typeWord
        $(document).ready(() => {
            $(".select2").select2()

            translate2 = document.getElementById('fieldTranslate')
            word2 = document.getElementById('fieldBasicWrod')
            CKEDITOR.replace(translate2)
            CKEDITOR.replace(word2)


            $("#form-data-word").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                if (typeWord == "POST") {
                    new Promise((resolve, reject) => {

                        let FD = new FormData($("#form-data-word")[0])
                        FD.set('translate', CKEDITOR.instances['fieldTranslate'].getData())
                        FD.set('basic', CKEDITOR.instances['fieldBasicWrod'].getData())

                        $axios.post(`{{ route('word.store') }}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-word').modal('hide')
                                loading('hide', $(".modal-content"))
                                $("#kitab").trigger('change')

                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })
                                // load_paging()
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                } else if (typeWord == 'PUT') {
                    new Promise((resolve, reject) => {
                        let url = `{{ route('word.update', ['word' => ':id']) }}`
                        url = url.replace(':id', $("#fieldIdWord").val())
                        let FD = new FormData($("#form-data-word")[0])
                        FD.set('translate', CKEDITOR.instances['fieldTranslate'].getData())
                        FD.set('basic', CKEDITOR.instances['fieldBasicWrod'].getData())
                        FD.append('_method', "PUT")
                        $axios.post(`${url}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-word').modal('hide')
                                loading('hide', $(".modal-content"))
                                $("#kitab").trigger('change')

                                $swal.fire({
                                    icon: 'success',
                                    title: data.message.head,
                                    text: data.message.body
                                })
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                }
            })
        });

        const addDataWord = (bab_id, chapter_id, el) => {
            loading('show', el)
            $("#modal-data-label-word").html("Tambah Kata")
            $("#btn-submit-word").html("Simpan")
            $("#form-data-word")[0].reset()
            $("#fieldCategoryWord").val($('#category').val())
            $("#fieldKitabWord").val($('#kitab').val())
            $("#fieldBabWord").val(bab_id)
            $("#fieldBaitWord").val(chapter_id)

            $("#fieldArab").val('')
            $("#fieldArabHarokat").val('')
            CKEDITOR.instances['fieldTranslate'].setData('')
            CKEDITOR.instances['fieldBasicWrod'].setData('text')

            $("#btn-del-word").hide()

            typeWord = 'POST'
            $("#modal-data-word").modal('show')
            loading('hide', el)
        }

        const deleteDataWord = (el) => {
            contextMenu.classList.remove("visible");

            let id = el.dataset.kata_id

            $swal.fire({
                    title: 'Yakin ?',
                    text: "Ingin menghapus Kata ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Ini!',
                    cancelButtonText: 'Tidak'
                })
                .then(res => {
                    if (res.isConfirmed) {
                        loading('show', el)
                        new Promise((resolve, reject) => {
                            let url = `{{ route('word.destroy', ['word' => ':id']) }}`
                            url = url.replace(':id', id)
                            $axios.delete(`${url}`)
                                .then(({
                                    data
                                }) => {
                                    loading('hide', el)
                                    $swal.fire({
                                        icon: 'success',
                                        title: data.message.head,
                                        text: data.message.body
                                    })
                                    // table.ajax.reload()

                                    $("#kitab").trigger('change')
                                    $('#modal-data-word').modal('hide')
                                })
                        })
                    }
                })
        }

        const editDataWord = (el) => {
            contextMenu.classList.remove("visible");
            let {
                id
            } = el.dataset;
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('word.edit', ['word' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let word = data.data

                        $("#btn-del-word").attr("data-kata_id", word.id)
                        $("#btn-del-word").show()

                        $("#fieldCategoryWord").val(word.book.category_id)
                        $("#fieldIdWord").val(word.id)

                        $("#fieldKitabWord").val(word.book_id)
                        $("#fieldBabWord").val(word.bab_id)
                        $("#fieldBaitWord").val(word.chapter_id)

                        $("#modal-data-word").modal('show')

                        $("#fieldArab").val(word.arab)
                        $("#fieldArabHarokat").val(word.arab_harokat)
                        CKEDITOR.instances['fieldTranslate'].setData(word.translate)
                        CKEDITOR.instances['fieldBasicWrod'].setData(word.basic)

                        typeWord = 'PUT'
                        $("#modal-data-label-word").html("Update Bait")
                        $("#btn-submit-word").html("Update")
                        loading('hide', el)
                    })
            })
        }

        const btnDuplicate = async (el) => {
            contextMenu.classList.remove("visible");

            let {
                id
            } = el.dataset;
            try {
                await duplicateWord(id);
                loading('hide', el)
            } catch (e) {
                console.log(e.message)
            }
        }

        const duplicateWord = (id) => {
            const FD = new FormData();
            FD.append('id', id);

            new Promise((resolve, reject) => {
                $axios.post(`{{ route('word.duplicate') }}`, FD)
                    .then(({
                        data
                    }) => {
                        $("#kitab").trigger('change')

                        $swal.fire({
                            icon: 'success',
                            title: data.message.head,
                            text: data.message.body
                        })
                    })
                    .catch(err => {
                        throwErr(err)
                    })
            })
        }

        const setDir = (id, type, el) => {
            new Promise((resolve, reject) => {
                let url = `{{ route('word.patch', ['id' => ':id', 'type' => ':type']) }}`
                url = url.replace(':id', id)
                url = url.replace(':type', type)
                $axios.patch(`${url}`)
                    .then(({
                        data
                    }) => {
                        show_full_chapter($("#inputNumberBait").val())
                        table.ajax.reload()
                        $swal.fire({
                            icon: 'success',
                            title: data.message.head,
                            text: data.message.body
                        })
                    })
                    .catch(err => throwErr(err))
            })
        }

        const editTranslate = (id) => {
            $(`#text-chapter-${id}`).toggle();
            $(`#form-chapter-${id}`).toggle();
        }

        const saveEditBait = (element) => {
            let {
                id,
                babId,
                bookId
            } = $(element).data();
            const FD = new FormData();

            FD.append('_method', 'PUT')
            FD.append('translate', $(element).val())
            FD.append('book_id', bookId)
            FD.append('bab_id', babId)

            new Promise((resolve, reject) => {
                let url = `{{ route('chapter.update', ['chapter' => ':id']) }}`
                url = url.replace(':id', id)

                $axios.defaults.headers.common['X-CSRF-TOKEN'] = `{{ csrf_token() }}`;
                $axios.post(`${url}`, FD)
                    .then(({
                        data
                    }) => {
                        $swal.fire({
                            icon: 'success',
                            title: data.message.head,
                            text: data.message.body
                        })

                        $("#kitab").trigger('change')
                    })
                    .catch(err => {
                        throwErr(err)
                    })
            })
        }
    </script>
@endsection
