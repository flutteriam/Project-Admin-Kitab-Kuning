@extends('layouts.master')

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
                    <h5>Bab</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary float-right" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                    <div class="row mb-2">
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
                            <select name="post" id="post" autocomplete="off" class="form-control">
                                <option value="" selected disabled>== Pilih Post ==</option>
                                @if ($selectedBook)
                                    @foreach ($books as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $selectedBook->id == $value->id ? 'selected' : '' }}>{{ $value->title }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldId">
                    <input type="hidden" name="category" id="inputCategory">
                    <input type="hidden" name="book_id" id="inputPost"
                        value="{{ $selectedBook != null ? $selectedBook->id : '' }}">
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="">Judul</label>
                            <input type="text" placeholder="Judul" name="title" id="fieldTitle" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Terjemahan Judul</label>
                            <textarea type="text" placeholder="Terjemahan Judul" name="translate_title" id="fieldTransTitle"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary" id="btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- BAIT/KALIMAT --}}
    <div class="modal fade" id="modal-data-bait" tabindex="-1" role="dialog" aria-labelledby="modal-dataLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-data-label-bait"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data-bait" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldIdBait">
                    <div class="modal-body">
                        <input type="hidden" name="category" id="fieldCategory" value="">
                        <input type="hidden" name="book_id" id="fieldPost" value="">
                        <input type="hidden" name="bab_id" id="fieldBab" value="">

                        <div class="form-group" style="display:none;">
                            <label for="">Full Bait <small>Non Harokat</small></label>
                            <textarea name="full_bait" id="fieldFullBait" cols="30" rows="5" class="form-control"
                                placeholder="Full Bait"></textarea>
                        </div>
                        <div class="form-group" style="display:none;">
                            <label for="">Full Bait <small>Harokat</small></label>
                            <textarea name="full_bait_harokat" id="fieldFullBaitHarokat" cols="30" rows="5" class="form-control"
                                placeholder="Full Bait"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Terjemahan</label>
                            <textarea name="translate" id="fieldTitleBait" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <textarea name="description" id="fieldDescription" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary" id="btn-submit-bait">Simpan</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data-word" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="fieldIdWord">
                    <div class="modal-body">

                        <input type="hidden" name="category" id="fieldCategoryWord">
                        <input type="hidden" name="book_id" id="fieldPostWord">
                        <input type="hidden" name="bab_id" id="fieldBabWord">
                        <input type="hidden" name="bait_id" id="fieldBaitWord">

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
                            <textarea name="translate_word" id="fieldTranslate" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label for="">Kata Dasar</label>
                            <textarea name="basic_word" id="fieldBasicWrod" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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
            get_bab_by_post($("#post").val(), 'container-detail')

            CKEDITOR.replace('translate_title')

            $("#tambah-data").on('click', () => {
                let post = $("#post").val()
                let category = $('#category').val()
                if (!post || !category) {
                    return $swal.fire('Gagal', 'Silahkan pilih kategori dan post terlebih dahuli', 'error')
                }
                $("#modal-data-label").html("Tambah Bab")
                $("#btn-submit").html("Simpan")
                // $("#form-data")[0].reset()
                CKEDITOR.instances['fieldTransTitle'].setData('')
                $("#fieldTitle").val('')
                $("#fieldTransTitle").val('')
                $("#fieldPost").html(`<option selected disabled>== Pilih Post ==</option>`)
                type = 'POST'
                $("#modal-data").modal('show')
            })

            $("#fieldCategory").on('change', () => {
                loading('show', $("#fieldPost"))
                get_post($("#fieldCategory").val(), 'fieldPost')
                    .then(() => loading('hide', $("#fieldPost")))
            })

            $("#category").on('change', () => {
                loading('show', $("#post"))
                get_post($("#category").val(), 'post')
                    .then(() => loading('hide', $("#post")))
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

                                $("#post").trigger('change')
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

                                $("#post").trigger('change')
                            })
                            .catch(err => {
                                loading('hide', $(".modal-content"))
                                throwErr(err)
                            })
                    })
                }
            })

            $("#post").on('change', () => {
                get_bab_by_post($("#post").val(), 'container-detail')

                $("#inputCategory").val($("#category").val())
                $("#inputPost").val($("#post").val())
                // table.ajax.reload()
            })
        })

        const get_bab_by_post = (id_post, element) => {
            return new Promise((resolve, reject) => {
                let url = `{{ route('post.ajax.detail', ['id' => ':id']) }}`
                url = url.replace(':id', id_post)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        $(`#${element}`).html(data)
                        resolve(data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            })
        }

        const get_post = (id_category, element) => {
            return new Promise((resolve, reject) => {
                let url = `{{ route('book.show', ['id' => ':id']) }}`
                url = url.replace(':id', id_category)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let post = data.data
                        let html = `<option value="" selected disabled>== Pilih Kitab ==</option>`
                        $.each(post, (i, e) => {
                            html += `<option value="${e.id}">${e.title}</option>`
                        })
                        $(`#${element}`).html(html)
                        resolve(post)
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
                        $("#inputPost").val(bab.book.id)
                        $("#fieldTitle").val(bab.title)
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

                                    $("#post").trigger('change')

                                    // table.ajax.reload()
                                })
                        })
                    }
                })
        }

        const detailData = id => {
            let url = `{{ route('bait.index', ['id' => ':id']) }}`
            url = url.replace(':id', id)
            window.location.href = `${url}`
        }
    </script>
    {{-- BAIT/KALIMAT --}}
    <script>
        let typeBait
        $(document).ready(() => {

            fullbait2 = document.getElementById('fieldFullBait')
            fullbaitHarokat2 = document.getElementById('fieldFullBaitHarokat')
            translate2 = document.getElementById('fieldTitleBait')
            description2 = document.getElementById('fieldDescription')
            CKEDITOR.replace(fullbait2)
            CKEDITOR.replace(fullbaitHarokat2)
            CKEDITOR.replace(translate2)
            CKEDITOR.replace(description2)

            $("#form-data-bait").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                if (typeBait == "POST") {
                    // let FD = new FormData($("#formPostData")[0])
                    let FD = new FormData($("#form-data-bait")[0])
                    FD.set('full_bait', CKEDITOR.instances['fieldFullBait'].getData())
                    FD.set('full_bait_harokat', CKEDITOR.instances['fieldFullBaitHarokat'].getData())
                    FD.set('translate', CKEDITOR.instances['fieldTitleBait'].getData())
                    FD.set('description', CKEDITOR.instances['fieldDescription'].getData())
                    new Promise((resolve, reject) => {
                        $axios.post(`{{ route('bait.store') }}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-bait').modal('hide')
                                loading('hide', $(".modal-content"))

                                // table.ajax.reload()
                                $("#post").trigger('change')

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
                    let FD = new FormData($("#form-data-bait")[0])
                    FD.set('full_bait', CKEDITOR.instances['fieldFullBait'].getData())
                    FD.set('full_bait_harokat', CKEDITOR.instances['fieldFullBaitHarokat'].getData())
                    FD.set('translate', CKEDITOR.instances['fieldTitleBait'].getData())
                    FD.set('description', CKEDITOR.instances['fieldDescription'].getData())
                    FD.set('_method', 'PUT')
                    new Promise((resolve, reject) => {
                        let url = `{{ route('bait.update', ['bait' => ':id']) }}`
                        url = url.replace(':id', $("#fieldIdBait").val())
                        $axios.post(`${url}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-bait').modal('hide')
                                loading('hide', $(".modal-content"))

                                // table.ajax.reload()
                                $("#post").trigger('change')

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

            $("#modal-data-label-bait").html("Tambah Bait")
            $("#btn-submit-bait").html("Simpan")
            $("#form-data-bait")[0].reset()
            CKEDITOR.instances['fieldFullBait'].setData('text')
            CKEDITOR.instances['fieldFullBaitHarokat'].setData('text')
            CKEDITOR.instances['fieldTitleBait'].setData('')
            CKEDITOR.instances['fieldDescription'].setData('')

            $("#fieldIdBait").val('')

            $("#fieldCategory").val($('#category').val())
            $("#fieldPost").val($('#post').val())
            $("#fieldBab").val(bab_id)

            typeBait = 'POST'
            $("#modal-data-bait").modal('show')
            loading('hide', el)
        }

        const editDataBait = (id, el) => {
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('bait.edit', ['bait' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        let bait = data.data.bait
                        let category = data.data.category
                        let post = data.data.post
                        let bab = data.data.bab
                        $("#fieldCategory").val(category)
                        $("#fieldPost").val(bait.book_id)
                        $("#fieldBab").val(bait.bab_id)
                        $("#fieldIdBait").val(bait.id)

                        $("#modal-data-bait").modal('show')

                        CKEDITOR.instances['fieldFullBait'].setData(bait.full_bait)
                        CKEDITOR.instances['fieldFullBaitHarokat'].setData(bait.full_bait_harokat)
                        CKEDITOR.instances['fieldTitleBait'].setData(bait.translate)
                        CKEDITOR.instances['fieldDescription'].setData(bait.description)
                        typeBait = 'PUT'
                        $("#modal-data-label-bait").html("Update Bait")
                        $("#btn-submit-bait").html("Update")
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
                            let url = `{{ route('bait.destroy', ['bait' => ':id']) }}`
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
                                    $("#post").trigger('change')
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
                        FD.set('translate_word', CKEDITOR.instances['fieldTranslate'].getData())
                        FD.set('basic_word', CKEDITOR.instances['fieldBasicWrod'].getData())

                        // $axios.post(`{{ route('word.store') }}`, $("#form-data-word").serialize())
                        $axios.post(`{{ route('word.store') }}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-word').modal('hide')
                                loading('hide', $(".modal-content"))

                                // table.ajax.reload()
                                // show_full_bait($("#inputNumberBait").val())
                                $("#post").trigger('change')

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
                        FD.set('translate_word', CKEDITOR.instances['fieldTranslate'].getData())
                        FD.set('basic_word', CKEDITOR.instances['fieldBasicWrod'].getData())
                        FD.append('_method', "PUT")
                        $axios.post(`${url}`, FD)
                            .then(({
                                data
                            }) => {
                                $('#modal-data-word').modal('hide')
                                loading('hide', $(".modal-content"))

                                // table.ajax.reload()
                                // show_full_bait($("#inputNumberBait").val())
                                $("#post").trigger('change')

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

            new Promise((resolve, reject) => {
                $axios.get(`{{ route('api.template_show') }}`)
                    .then(({
                        data
                    }) => {
                        let template_kata = data.data
                        let html =
                            `<option value="" disabled selected>== Pilih Template Kata ==</option>`
                        template_kata.forEach(e => {
                            html +=
                                `<option data-arab="${e.arab}" data-arabharokat="${e.arab_harokat}" data-translate="${e.translate}" data-basicword="${e.basic_word}">${e.arab}</option>`
                        })
                        $("#template_kata").html(html)
                    })
            })

            $("#template_kata").on('change', () => {
                let data = $("#template_kata").find(':selected')
                let arab = data.data('arab')
                let arab_harokat = data.data('arabharokat')
                let translate = data.data('translate')
                let basic_word = data.data('basicword')
                $("#fieldArab").val(arab)
                $("#fieldArabHarokat").val(arab_harokat)
                CKEDITOR.instances['fieldTranslate'].setData(translate)
                CKEDITOR.instances['fieldBasicWrod'].setData(basic_word)
            })
        })

        const addDataWord = (bab_id, bait_id, el) => {
            loading('show', el)
            $("#modal-data-label-word").html("Tambah Kata")
            $("#btn-submit-word").html("Simpan")
            $("#form-data-word")[0].reset()
            $("#fieldCategoryWord").val($('#category').val())
            $("#fieldPostWord").val($('#post').val())
            $("#fieldBabWord").val(bab_id)
            $("#fieldBaitWord").val(bait_id)

            $("#fieldArab").val('')
            $("#fieldArabHarokat").val('')
            CKEDITOR.instances['fieldTranslate'].setData('')
            CKEDITOR.instances['fieldBasicWrod'].setData('text')
            $("#template_kata").val('').trigger('change')

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

                                    $("#post").trigger('change')
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
                        let chapter = data.data

                        $("#btn-del-word").attr("data-kata_id", word.id)
                        $("#btn-del-word").show()

                        $("#fieldCategoryWord").val(word.post.category_id)
                        $("#fieldIdWord").val(word.id)

                        $("#fieldPostWord").val(word.book_id)
                        $("#fieldBabWord").val(word.bab_id)
                        $("#fieldBaitWord").val(word.bait_id)

                        $("#modal-data-word").modal('show')

                        $("#fieldArab").val(word.arab)
                        $("#fieldArabHarokat").val(word.arab_harokat)
                        CKEDITOR.instances['fieldTranslate'].setData(word.translate_word)
                        CKEDITOR.instances['fieldBasicWrod'].setData(word.basic_word)

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
                        $("#post").trigger('change')

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
                        show_full_bait($("#inputNumberBait").val())
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
            $(`#text-bait-${id}`).toggle();
            $(`#form-bait-${id}`).toggle();
        }

        const saveEditBait = (element) => {
            let {
                id,
                babId,
                postId
            } = $(element).data();
            const FD = new FormData();

            FD.append('_method', 'PUT')
            FD.append('translate', $(element).val())
            FD.append('book_id', postId)
            FD.append('bab_id', babId)

            new Promise((resolve, reject) => {
                let url = `{{ route('bait.update', ['bait' => ':id']) }}`
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

                        $("#post").trigger('change')
                    })
                    .catch(err => {
                        throwErr(err)
                    })
            })
        }
    </script>
@endsection
