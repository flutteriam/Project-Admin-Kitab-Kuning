@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Post / Bait</h5>
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
                </div>
                <div class="table-responsive">
                    <button class="btn-vis btn btn-sm btn-success m-2" data-column="2">Tampilkan Bait Harokat</button>
                    <table id="table-data" class="display datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bait <small>Non Harokat</small></th>
                                <th>Bait <small>Harokat</small></th>
                                <th>Terjemahan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <form action="" method="post" id="formPostData">
                    @csrf
                    <input type="hidden" name="book_id" id="fieldPostIdPost" value="{{ isset($bab) ? $bab->book_id : '' }}">
                    <input type="hidden" name="bab_id" id="fieldbabIdPost" value="{{ isset($bab) ? $bab->id : '' }}">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Full Bait <small>Non Harokat</small></label>
                            <textarea name="full_chapter" class="form-control mb-1" id="fieldFillBaitPost" cols="30" rows="5" placeholder="Isi Bait Non Harokat"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="">Full Bait <small>Harokat</small></label>
                            <textarea name="full_chapter_harokat" class="form-control mb-1" id="fieldFillBaitHarokatPost" cols="30" rows="5" placeholder="Isi Bait Harokat"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="">Terjemahan Judul</label>
                            <textarea name="translate_chapter" id="fieldTranslateBaitPost" cols="30" rows="5" class="form-control" placeholder="Terjemahan"></textarea>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="">Deskripsi</label>
                            <textarea name="description" id="fieldDescriptionPost" cols="30" rows="5" class="form-control" placeholder="Deskripsi"></textarea>
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
                        <label for="">Full Bait <small>Non Harokat</small></label>
                        <textarea name="full_chapter" id="fieldFullBait" cols="30" rows="5" class="form-control" placeholder="Full Bait"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Full Bait <small>Harokat</small></label>
                        <textarea name="full_chapter_harokat" id="fieldFullBaitHarokat" cols="30" rows="5" class="form-control" placeholder="Full Bait"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Terjemahan Judul</label>
                        <textarea name="translate_chapter" id="fieldTitle" cols="30" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Deskripsi</label>
                        <textarea name="description" id="fieldDescription" cols="30" rows="5" class="form-control" required></textarea>
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
@endsection

@section('js')
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/styles.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/adapters/jquery.js') }}"></script>
<script>
    let table
    let type
    $(document).ready(() => {
        fullchapter1 = document.getElementById('fieldFillBaitPost')
        fullchapterHarokat1 = document.getElementById('fieldFillBaitHarokatPost')
        translate1 = document.getElementById('fieldTranslateBaitPost')
        description1 = document.getElementById('fieldDescriptionPost')
        CKEDITOR.replace(fullchapter1)
        CKEDITOR.replace(fullchapterHarokat1)
        CKEDITOR.replace(translate1)
        CKEDITOR.replace(description1)

        fullchapter2 = document.getElementById('fieldFullBait')
        fullchapterHarokat2 = document.getElementById('fieldFullBaitHarokat')
        translate2 = document.getElementById('fieldTitle')
        description2 = document.getElementById('fieldDescription')
        CKEDITOR.replace(fullchapter2)
        CKEDITOR.replace(fullchapterHarokat2)
        CKEDITOR.replace(translate2)
        CKEDITOR.replace(description2)

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
                url: "{{ route('api.chapter_datatable') }}",
                type: "POST",
                data: data => {
                    data.post = $("#post").val()
                    data.bab = $("#bab").val()
                }
            },
            columns : [
                { data: "id", orderable: false, searchable: false },
                { data: "full_chapter" },
                { data: "full_chapter_harokat", visible: false },
                { data: "translate_chapter" },
                { data: "description" },
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

        $(".btn-vis").on('click', e => {
            e.preventDefault()
            let column_non = table.column(($(e.currentTarget).data('column') - 1))
            let column_har = table.column($(e.currentTarget).data('column'))
            column_non.visible(!column_non.visible())
            column_har.visible(!column_har.visible())
        })

        $("#tambah-data").on('click', () => {
            $("#modal-data-label").html("Tambah Bait")
            $("#btn-submit").html("Simpan")
            $("#form-data")[0].reset()
            CKEDITOR.instances['fieldFillBaitPost'].setData('')
            CKEDITOR.instances['fieldFillBaitHarokatPost'].setData('')
            CKEDITOR.instances['fieldTranslateBaitPost'].setData('')
            CKEDITOR.instances['fieldDescriptionPost'].setData('')
            $("#fieldPost").html(`<option selected disabled>== Pilih Post ==</option>`)
            $("#fieldBab").html(`<option selected disabled>== Pilih Bab ==</option>`)
            type = 'POST'
            $("#modal-data").modal('show')
        })

        $("#form-data").on('submit', e => {
            loading('show', $(".modal-content"))
            e.preventDefault()
            if(type == "POST") {
                let FD = new FormData($("#formPostData")[0])
                FD.append('translate_chapter', CKEDITOR.instances['fieldTranslateBaitPost'].getData())
                FD.append('description', CKEDITOR.instances['fieldDescriptionPost'].getData())
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('chapter.store') }}`, FD)
                        .then(({data}) => {
                            $('#modal-data').modal('hide')
                            loading('hide', $(".modal-content"))
                            table.ajax.reload()
                            CKEDITOR.instances['fieldTranslateBaitPost'].setData('')
                            CKEDITOR.instances['fieldDescriptionPost'].setData('')
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
            } else if(type == 'PUT') {
                let FD = new FormData($("#form-data")[0])
                FD.append('full_chapter', CKEDITOR.instances['fieldFullBait'].getData())
                FD.append('full_chapter_harokat', CKEDITOR.instances['fieldFullBaitHarokat'].getData())
                FD.append('translate_chapter', CKEDITOR.instances['fieldTitle'].getData())
                FD.append('description', CKEDITOR.instances['fieldDescription'].getData())
                FD.append('_method', 'PUT')
                new Promise((resolve, reject) => {
                    let url = `{{ route('chapter.update', ['chapter' => ':id']) }}`
                    url = url.replace(':id', $("#fieldId").val())
                    $axios.post(`${url}`, FD)
                        .then(({data}) => {
                            $('#modal-data').modal('hide')
                            loading('hide', $(".modal-content"))
                            table.ajax.reload()
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
            get_post($("#category").val(), 'post', true)
                .then(() => loading('hide', $("#post")))
        })

        $("#post").on('change', () => {
            loading('show', $("#bab"))
            get_bab($("#post").val(), 'bab')
                .then(() => {
                    loading('hide', $("#bab"))
                    table.ajax.reload()
                })
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

        $("#bab").on('change', () => {
            $("#fieldPostIdPost").val($("#post").val())
            $("#fieldbabIdPost").val($("#bab").val())
            table.ajax.reload()
        })

        $("#formPostData").on('submit', e => {
            let post = $('#post').val()
            let bab = $("#bab").val()
            if(!post || !bab) {
                $swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "Harap memilih Post dan Bab terlebih dahulu"
                })
                return false
            }
            e.preventDefault()
            let FD = new FormData($("#formPostData")[0])
            FD.append('full_chapter', CKEDITOR.instances['fieldFillBaitPost'].getData())
            FD.append('full_chapter_harokat', CKEDITOR.instances['fieldFillBaitHarokatPost'].getData())
            FD.append('translate_chapter', CKEDITOR.instances['fieldTranslateBaitPost'].getData())
            FD.append('description', CKEDITOR.instances['fieldDescriptionPost'].getData())
            new Promise((resolve, reject) => {
                $axios.post(`{{ route('chapter.store') }}`, FD)
                    .then(({data}) => {
                        $('#modal-data').modal('hide')
                        loading('hide', $(".modal-content"))
                        table.ajax.reload()
                        $("#fieldFillBaitPost").val('')
                        CKEDITOR.instances['fieldFillBaitPost'].setData('')
                        CKEDITOR.instances['fieldFillBaitHarokatPost'].setData('')
                        CKEDITOR.instances['fieldTranslateBaitPost'].setData('')
                        CKEDITOR.instances['fieldDescriptionPost'].setData('')
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
        })

    })

    const get_post = (id_category, element, status = false) => {
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
                    if(status) $("#bab").html(`<option value="" selected disabled>== Pilih Bait ==</option>`)
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

    const editData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('chapter.edit', ['chapter' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let chapter = data.data.chapter
                    let category = data.data.category
                    $("#fieldCategory").val(category)
                    $("#fieldId").val(chapter.id)
                    get_post(category, 'fieldPost')
                        .then(() => {
                            $("#fieldPost").val(chapter.book_id)
                            get_bab(chapter.book_id, 'fieldBab')
                                .then(() => {
                                    $("#fieldBab").val(chapter.bab_id)
                                    $("#modal-data").modal('show')
                                })
                        })
                    CKEDITOR.instances['fieldFullBait'].setData(chapter.full_chapter)
                    CKEDITOR.instances['fieldFullBaitHarokat'].setData(chapter.full_chapter_harokat)
                    CKEDITOR.instances['fieldTitle'].setData(chapter.translate_chapter)
                    CKEDITOR.instances['fieldDescription'].setData(chapter.description)
                    type = 'PUT'
                    $("#modal-data-label").html("Update Bait")
                    $("#btn-submit").html("Update")
                    loading('hide', el)
                })
        })
    }

    const deleteData = (id, el) => {
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

    const detailData = id => {
        let url = `{{ route('chapter.index', ['id' => ":id"]) }}`
        url = url.replace(':id', id)
        window.location.href = `${url}`
    }
</script>
@endsection
