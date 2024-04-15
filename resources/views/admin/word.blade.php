@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Post / Kata</h5>
            </div>
            <div class="card-body">
                {{-- <button class="btn btn-primary float-right" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button> --}}
                <div class="row mb-2">
                    <div class="col-md-4">
                        <select name="category" id="category" autocomplete="off" class="form-control">
                            <option value="" selected disabled>== Pilih Kategori ==</option>
                            @foreach ($categories as $key => $value)
                            @if ($bab)
                            <option {{ ($active_category->id == $value->id) ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                            @else
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="post" id="post" autocomplete="off" class="form-control">
                            <option value="" selected disabled>== Pilih Post ==</option>
                            @if ($bab)
                                @foreach ($post as $value)
                                <option {{ $value->id == $temp_post->id ? "selected" : '' }} value="{{ $value->id }}">{{ $value->title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="bab" id="bab" autocomplete="off" class="form-control">
                            <option value="" selected disabled>== Pilih bab ==</option>
                            @if ($bab)
                            @foreach ($temp_post->bab()->get() as $value)
                            <option {{ $value->id == $bab->id ? "selected" : '' }} value="{{ $value->id }}">{{ $value->title }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <input type="hidden" name="chapter" id="inputNumberBait">
                    <div class="card-body digits row" id="fieldBaitNumber">
                    </div>
                </div>
                <section id="fieldPreviewBait" dir="rtl"></section>
                <div class="table-responsive">
                    <table id="table-data" class="display datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Arab</th>
                                <th>Arab Harokat</th>
                                <th>Terjemahan</th>
                                <th>Kata Dasar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="form-group">
                    <label for="">Template Kata</label>
                    <select name="template_kata" id="template_kata" class="form-control select2">
                    </select>
                </div>
                <form action="" method="post" id="formPostData">
                    @csrf
                    <input type="hidden" name="book_id" id="fieldPostIdPost" value="{{ isset($bab) ? $bab->book_id : '' }}">
                    <input type="hidden" name="bab_id" id="fieldbabIdPost" value="{{ isset($bab) ? $bab->id : '' }}">
                    <input type="hidden" name="chapter_id" id="fieldBaitIdPost" value="">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="arab" id="fieldArabPost" class="form-control" placeholder="Arab">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="arab_harokat" id="fieldArabHarokatPost" class="form-control" placeholder="Arab Harokat">
                        </div>
                        <div class="col-md-12">
                            <label for="">Terjemahan</label>
                            <textarea name="translate_word" id="fieldTranslateBaitPost" cols="30" rows="5" class="form-control" placeholder="Terjemahan"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="">Kata Dasar</label>
                            <textarea name="basic_word" id="fieldBasicWordPost" cols="30" rows="5" class="form-control" placeholder="Kata Dasar"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-2">Tambah</button>
                </form>
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
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Kategori</label>
                        <select name="category" id="fieldCategory" autocomplete="off" class="form-control" required>
                            <option value="" selected disabled>== Pilih Kategori ==</option>
                            @foreach ($categories as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Post</label>
                        <select name="book_id" id="fieldPost" autocomplete="off" class="form-control" required>
                            <option value="" selected disabled>== Pilih Post ==</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Bab</label>
                        <select name="bab_id" id="fieldBab" autocomplete="off" class="form-control" required>
                            <option value="" selected disabled>== Pilih Bab ==</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Bait</label>
                        <select name="chapter_id" id="fieldBait" autocomplete="off" class="form-control" required>
                            <option value="" selected disabled>== Pilih Bait ==</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Arab</label>
                        <input type="text" name="arab" id="fieldArab" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Arab Harokat</label>
                        <input type="text" name="arab_harokat" id="fieldArabHarokat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Terjemahan Kata</label>
                        <textarea name="translate_word" id="fieldTranslate" cols="30" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Kata Dasar</label>
                        <textarea name="basic_word" id="fieldBasicWrod" cols="30" rows="5" class="form-control" required></textarea>
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
@endsection

@section('js')
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/styles.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/adapters/jquery.js') }}"></script>
<script src="{{ asset('vendor/select2/select2.full.min.js') }}"></script>
<script>
    let table
    let type
    $(document).ready(() => {
        $(".select2").select2()
        @if ($bab)
        load_paging()
        $("#inputNumberBait").val({{ $first_chapter }})
        $("#fieldBaitIdPost").val({{ $first_chapter }})
        show_full_chapter({{ $first_chapter }})
        @endif
        translate1 = document.getElementById('fieldTranslateBaitPost')
        word1 = document.getElementById('fieldBasicWordPost')
        CKEDITOR.replace(translate1)
        CKEDITOR.replace(word1)

        translate2 = document.getElementById('fieldTranslate')
        word2 = document.getElementById('fieldBasicWrod')
        CKEDITOR.replace(translate2)
        CKEDITOR.replace(word2)

        $.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings) {
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            }
        }

        table = $("#table-data").DataTable({
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 25, 50, 100, 200, 300, 500, 1000, 800000000],
                [10, 25, 50, 100, 200, 300, 500, 1000, "All"]
            ],
            ajax: {
                url: "{{ route('api.kata_post_datatable') }}",
                type: "POST",
                data: data => {
                    data.bab = $("#bab").val()
                    data.chapter = $("#inputNumberBait").val()
                }
            },
            columns : [
                { data: "id", orderable: false, searchable: false },
                { data: "arab" },
                { data: "arab_harokat" },
                { data: "translate_word" },
                { data: "basic_word" },
                { data: "aksi", orderable: false, searchable: false }
            ],
            rowId: function(a) {
                return a
            },
            rowCallback: function(row, data, iDisplayIndex) {
                let info = this.fnPagingInfo()
                let page = info.iPage
                let length = info.iLength
                let index = page * length + (iDisplayIndex + 1)
                $("td:eq(0)", row).html(index)
            }
        })

        $("#form-data").on('submit', e => {
            loading('show', $(".modal-content"))
            e.preventDefault()
            if(type == "POST") {
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('chapter.store') }}`, $("#form-data").serialize())
                        .then(({data}) => {
                            $('#modal-data').modal('hide')
                            loading('hide', $(".modal-content"))
                            table.ajax.reload()
                            show_full_chapter($("#inputNumberBait").val())
                            $swal.fire({
                                icon: 'success',
                                title: data.message.head,
                                text: data.message.body
                            })
                            load_paging()
                        })
                        .catch(err => {
                            loading('hide', $(".modal-content"))
                            throwErr(err)
                        })
                })
            } else if(type == 'PUT') {
                new Promise((resolve, reject) => {
                    let url = `{{ route('chapter.update', ['chapter' => ':id']) }}`
                    url = url.replace(':id', $("#fieldId").val())
                    let FD = new FormData($("#form-data")[0])
                    FD.append('translate_chapter', CKEDITOR.instances['fieldTranslate'].getData())
                    FD.append('basic_word', CKEDITOR.instances['fieldBasicWrod'].getData())
                    FD.append('_method', "PUT")
                    $axios.post(`${url}`, FD)
                        .then(({data}) => {
                            $('#modal-data').modal('hide')
                            loading('hide', $(".modal-content"))
                            table.ajax.reload()
                            show_full_chapter($("#inputNumberBait").val())
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

        $("#category").on('change', () => {
            loading('show', $("#post"))
            $("#fieldPostIdPost").val('')
            $("#fieldbabIdPost").val('')
            $("#fieldBaitIdPost").val('')
            get_post($("#category").val(), 'post')
                .then(() => loading('hide', $("#post")))
        })

        $("#post").on('change', () => {
            loading('show', $("#bab"))
            $("#fieldPostIdPost").val('')
            $("#fieldbabIdPost").val('')
            $("#fieldBaitIdPost").val('')
            get_bab($("#post").val(), 'bab')
                .then(() => loading('hide', $("#bab")))
        })

        $("#tambah-data").on('click', () => {
            $("#modal-data-label").html("Tambah Kata")
            $("#btn-submit").html("Simpan")
            $("#form-data")[0].reset()
            $("#fieldPost").html(`<option selected disabled>== Pilih Post ==</option>`)
            $("#fieldBab").html(`<option selected disabled>== Pilih Bab ==</option>`)
            type = 'POST'
            $("#modal-data").modal('show')
        })

        $("#fieldCategory").on('change', () => {
            loading('show', $("#fieldPost"))
            get_post($("#fieldCategory").val(), 'fieldPost')
                .then(() => loading('hide', $("#fieldPost")))
        })

        $("#fieldPost").on('change', () => {
            loading('show', $("#fieldBab"))
            get_bab($("#fieldPost").val(), 'fieldBab')
                .then(() => loading('hide', $("#fieldBab")))
        })

        $("#fieldBab").on('change', () => {
            loading('show', $("#fieldBait"))
            get_chapter($("#fieldBab").val(), 'fieldBait')
                .then(() => loading('hide', $("#fieldBait")))
        })

        $("#bab").on('change', () => {
            load_paging()
                .then(e => {
                    if(e == 0) {
                        $("#fieldBaitIdPost").val("")
                        $("#inputNumberBait").val("")
                    } else {
                        $("#fieldBaitIdPost").val(e)
                        $("#inputNumberBait").val(e)
                    }
                    table.ajax.reload()
                })
            $("#fieldPostIdPost").val($("#post").val())
            $("#fieldbabIdPost").val($("#bab").val())
        })

        $("#formPostData").on('submit', e => {
            if($("#fieldPostIdPost").val() == '' || $("#fieldbabIdPost").val() == '' || $("#fieldBaitIdPost").val() == '') {
                $swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "Harap memilih Post, Bab dan chapter terlebih dahulu"
                })
                return false
            }
            e.preventDefault()
            let FD = new FormData($("#formPostData")[0])
            FD.append('translate_chapter', CKEDITOR.instances['fieldTranslateBaitPost'].getData())
            FD.append('basic_word', CKEDITOR.instances['fieldBasicWordPost'].getData())
            new Promise((resolve, reject) => {
                $axios.post(`{{ route('chapter.store') }}`, FD)
                    .then(({data}) => {
                        table.ajax.reload()
                        $swal.fire({
                            icon: 'success',
                            title: data.message.head,
                            text: data.message.body
                        })
                        $("#fieldArabPost").val('')
                        $("#fieldArabHarokatPost").val('')
                        CKEDITOR.instances['fieldTranslateBaitPost'].setData('')
                        CKEDITOR.instances['fieldBasicWordPost'].setData('')
                        $("#template_kata").val('').trigger('change')
                        // $("#fieldTranslateBaitPost").val('')
                        // $("#fieldBasicWordPost").val('')
                        load_paging(true)
                        show_full_chapter($("#inputNumberBait").val())
                    })
                    .catch(err => {
                        throwErr(err)
                    })
            })
        })

        new Promise((resolve, reject) => {
            $axios.get(`{{ route('api.template_show') }}`)
                .then(({data}) => {
                    let template_kata = data.data
                    let html = `<option value="" disabled selected>== Pilih Template Kata ==</option>`
                    template_kata.forEach(e => {
                        html += `<option data-arab="${e.arab}" data-arabharokat="${e.arab_harokat}" data-translate="${e.translate}" data-basicword="${e.basic_word}">${e.arab}</option>`
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
            $("#fieldArabPost").val(arab)
            $("#fieldArabHarokatPost").val(arab_harokat)
            CKEDITOR.instances['fieldTranslateBaitPost'].setData(translate)
            CKEDITOR.instances['fieldBasicWordPost'].setData(basic_word)
        })
    })

    $(document).on('click', ".card-body .digits > .chapter", e => {
        let comp = $(".chapter")
        $.each(comp, (index, element) => {
            $(element).removeClass('badge-primary')
            if(!$(element).hasClass('badge-light')) {
                $(element).addClass('badge-light')
            }
        })
        $(e.currentTarget).removeClass("badge-light")
        $(e.currentTarget).addClass("badge-primary")
        $("#inputNumberBait").val($(e.currentTarget).data('chapter'))
        $("#fieldBaitIdPost").val($(e.currentTarget).data('chapter'))
        show_full_chapter($(e.currentTarget).data('chapter'))
        table.ajax.reload()
    })

    const load_paging = (type = false) => {
        return new Promise((resolve, reject) => {
            let url = `{{ route('chapter.show', ['id' => ":id"]) }}`
            url = url.replace(':id', $("#bab").val())
            let numberBait = $("#inputNumberBait").val()
            $axios.get(`${url}`)
                .then(({data}) => {
                    $(".card-body .digits").html(``)
                    let html = `<p class="my-auto">Bait : </p>`
                    let chapter = data.data
                    if(chapter.length > 0) {
                        chapter.forEach((e, i) => {
                            if(type) {
                                if(e.id == numberBait) {
                                    html += `<a href="javascript:void(0)" class="chapter badge badge-primary mx-1 my-1" data-chapter="${e.id}">${e.no}</a>`
                                } else {
                                    html += `<a href="javascript:void(0)" class="chapter badge badge-light mx-1 my-1" data-chapter="${e.id}">${e.no}</a>`
                                }
                            } else {
                                if(i == 0) {
                                    html += `<a href="javascript:void(0)" class="chapter badge badge-primary mx-1 my-1" data-chapter="${e.id}">${e.no}</a>`
                                } else {
                                    html += `<a href="javascript:void(0)" class="chapter badge badge-light mx-1 my-1" data-chapter="${e.id}">${e.no}</a>`
                                }
                            }
                        })
                    } else {
                        html = `<p><small>Belum ada chapter</small></p>`
                    }
                    $(".card-body .digits").html(html)
                    if(chapter.length > 0) {
                        resolve(chapter[0].id)
                    } else {
                        resolve(0)
                    }
                })
        })
    }

    const get_post = (id_category, element) => {
        return new Promise((resolve, reject) => {
            let url = `{{ route('post.show', ['id' => ':id']) }}`
            url = url.replace(':id', id_category)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let post = data.data
                    let html = `<option value="" selected disabled>== Pilih Post ==</option>`
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

    const get_bab = (id_post, element) => {
        return new Promise((resolve, reject) => {
            let url = `{{ route('bab.show', ['bab' => ':id']) }}`
            url = url.replace(':id', id_post)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let bab = data.data
                    let html = `<option value="" selected disabled>== Pilih Bab ==</option>`
                    $.each(bab, (i, e) => {
                        html += `<option value="${e.id}">${e.title}</option>`
                    })
                    $(`#${element}`).html(html)
                    resolve(data)
                })
        })
    }

    const get_chapter = (id_bab, element) => {
        return new Promise((resolve, reject) => {
            let url = `{{ route('chapter.show', ['id' => ":id"]) }}`
            url = url.replace(':id', $("#fieldBab").val())
            $axios.get(`${url}`)
                .then(({data}) => {
                    let post = data.data
                    let html = `<option value="" selected disabled>== Pilih Bait ==</option>`
                    $.each(post, (i, e) => {
                        html += `<option value="${e.id}">${e.translate_chapter}</option>`
                    })
                    $(`#${element}`).html(html)
                    resolve(post)
                })
        })
    }

    const deleteData = (id, el) => {
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
            if(res.isConfirmed) {
                loading('show', el)
                new Promise((resolve, reject) => {
                    let url = `{{ route('chapter.destroy', ['chapter' => ':id']) }}`
                    url = url.replace(':id', id)
                    $axios.delete(`${url}`)
                        .then(({data}) => {
                            loading('hide', el)
                            $swal.fire({
                                icon: 'success',
                                title: data.message.head,
                                text: data.message.body
                            })
                            table.ajax.reload()
                        })
                })
            }
        })
    }

    const editData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('chapter.edit', ['chapter' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let chapter = data.data
                    $("#fieldCategory").val(chapter.post.category_id)
                    $("#fieldId").val(chapter.id)
                    loading('show', $("#fieldPost"))
                    loading('show', $("#fieldBab"))
                    loading('show', $("#fieldBait"))
                    get_post(chapter.post.category_id, 'fieldPost')
                        .then(() => {
                            loading('hide', $("#fieldPost"))
                            $("#fieldPost").val(chapter.book_id)
                            get_bab(chapter.book_id, 'fieldBab')
                                .then(() => {
                                    loading('hide', $("#fieldBab"))
                                    $("#fieldBab").val(chapter.bab_id)
                                    get_chapter(chapter.bab_id, 'fieldBait')
                                        .then(() => {
                                            loading('hide', $("#fieldBait"))
                                            $("#fieldBait").val(chapter.chapter_id)
                                            $("#modal-data").modal('show')
                                        })
                                })
                        })
                    $("#fieldArab").val(chapter.arab)
                    $("#fieldArabHarokat").val(chapter.arab_harokat)
                    CKEDITOR.instances['fieldTranslate'].setData(chapter.translate_word)
                    CKEDITOR.instances['fieldBasicWrod'].setData(chapter.basic_word)
                    // $("#fieldTranslate").val(chapter.translate_word)
                    // $("#fieldBasicWrod").val(chapter.basic_word)
                    type = 'PUT'
                    $("#modal-data-label").html("Update Bait")
                    $("#btn-submit").html("Update")
                    loading('hide', el)
                })
        })
    }

    const show_full_chapter = id => {
        new Promise((resolve, reject) => {
            let url = `{{ route('chapter.show', ['chapter' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let kata = data.data
                    let preview_chapter = ``
                    if(kata.length > 0) {
                        kata.forEach(e => {
                        preview_chapter += `<div class="badge m-1 kata" style="border-radius: 10px;padding: 10px; background-color: #eee">
                                            <h3 class="arab">${e.arab_harokat}</h3>
                                        </div>`
                        })
                    }
                    $("#fieldPreviewBait").html(preview_chapter)
                })
        })
    }

    const setDir = (id, type, el) => {
        new Promise((resolve, reject) => {
            let url = `{{ route('chapter.patch', ['id' => ':id', 'type' => ':type']) }}`
            url = url.replace(':id', id)
            url = url.replace(':type', type)
            $axios.patch(`${url}`)
                .then(({data}) => {
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

</script>
@endsection
