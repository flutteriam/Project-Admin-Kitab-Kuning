@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Kitab</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select name="category" id="category" autocomplete="off" class="form-control">
                                <option value="" selected disabled>== Pilih Kategori ==</option>
                                @foreach ($categories as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ isset($active_category) && $active_category->id == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ml-auto">
                            <button class="btn btn-primary float-right" id="tambah-data"><i class="fa fa-plus"></i>
                                Tambah</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table-data" class="display datatables">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Kategori</th>
                                    <th>Judul</th>
                                    <th>Terjemahan Judul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
                <form id="form-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <input type="hidden" name="category_id" id="fieldCategory"> -->
                    <input type="hidden" name="id" id="fieldId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">Kategori</label>
                                <select name="category_id" id="fieldCategory" class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="">Judul</label>
                                <input type="text" placeholder="Judul" name="title" id="fieldTitle"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">Utama</label>
                                <select name="type" id="fieldType" class="form-control">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="">Terjemahan Judul</label>
                                <input type="text" placeholder="Terjemahan Judul" name="description" id="fieldTransTitle"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Penjelasan</label>
                            <textarea name="content" id="fieldContent" cols="30" rows="5" class="form-control" placeholder="Penjelasan"
                                required></textarea>
                        </div>
                        <div class="text-center" id="teksImage">
                            <p class="text-danger">Kosongkan jika tidak ingin merubah gambar</p>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="">Gambar</label>
                                <input type="file" name="cover" id="fieldImage" accept="image/*" class="form-control"
                                    required>
                            </div>
                            <div class="col" id="fieldOldImage" style="display: none">
                                <img src="" alt="" id="sourceOldImage" class="img-fluid w-200">
                            </div>
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
        const PATH_IMAGE = `{{ url('/') }}`
        let table
        let type
        $(document).ready(() => {
            CKEDITOR.replace('content')
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
                    url: "{{ route('api.book_datatable') }}",
                    type: "POST",
                    data: data => {
                        data.category = $("#category").val()
                    }
                },
                columns: [{
                        data: "id",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let url = `{{ route('bab.index', ':id') }}`
                            url = url.replace(':id', data.id);

                            let html = `<img src="{{ asset('storage') }}/${data.cover}" alt="{{ asset('storage') }}/${data.cover}" class="img-fluid w-50">
                            <a href="${url}" class="btn btn-primary btn-sm"><i class="fa fa-chevron-right"></i></a>`
                            return html;
                        }
                    },
                    {
                        data: "category.name"
                    },
                    {
                        data: "title"
                    },
                    {
                        data: "description"
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ],
                rowId: function(a) {
                    return a;
                },
                rowCallback: function(row, data, iDisplayIndex) {
                    let info = this.fnPagingInfo();
                    let page = info.iPage;
                    let length = info.iLength;
                    let index = page * length + (iDisplayIndex + 1);
                    $("td:eq(0)", row).html(index);
                }
            })

            $("#category").on('change', () => {
                table.ajax.reload()
            })

            $("#tambah-data").on('click', () => {
                let category = $("#category").val()
                if (!category) {
                    return $swal.fire('Gagal', 'Silahkan pilih kategori terlebih dahulu', 'error')
                }
                $("#modal-data-label").html("Tambah Kitab")
                $('#fieldImage').prop('required', true)
                $('#fieldType').val('0')
                $("#btn-submit").html("Simpan")
                $("#form-data")[0].reset()
                CKEDITOR.instances['fieldContent'].setData('')
                $("#fieldOldImage").hide()
                $("#teksImage").hide()
                type = 'POST'
                $("#modal-data").modal('show')
                $("#fieldCategory").val(category)
            })

            $("#form-data").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                let FD = new FormData($("#form-data")[0])
                FD.append('content', CKEDITOR.instances['fieldContent'].getData())
                FD.append('cover', document.querySelector('#fieldImage').files[0])
                if (type == "POST") {
                    new Promise((resolve, reject) => {
                        $axios.post(`{{ route('book.store') }}`, FD, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(({
                                data
                            }) => {
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
                } else if (type == 'PUT') {
                    new Promise((resolve, reject) => {
                        let url = `{{ route('book.update', ['book' => ':id']) }}`
                        url = url.replace(':id', $("#fieldId").val())
                        FD.append('_method', 'PUT')
                        $axios.post(`${url}`, FD, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(({
                                data
                            }) => {
                                $('#modal-data').modal('hide')
                                loading('hide', $(".modal-content"))
                                table.ajax.reload()
                                $("#fieldImage").val('')
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

        const editData = (id, el) => {
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('book.edit', ['book' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        type = 'PUT'
                        let post = data.data
                        $("#modal-data-label").html("Update Kitab")
                        $('#fieldImage').prop('required', false)
                        $("#btn-submit").html("Update")
                        $("#fieldCategory").val(post.category.id)
                        $("#fieldId").val(post.id)
                        $('#fieldType').val(post.type)
                        $("#fieldTitle").val(post.title)
                        $("#fieldTransTitle").val(post.description)
                        $("#teksImage").show()
                        CKEDITOR.instances['fieldContent'].setData(post.content)
                        if (post.image) {
                            $('#fieldOldImage').show()
                            $("#sourceOldImage").attr('src', `${PATH_IMAGE}/${post.image}`)
                            $("#sourceOldImage").attr('alt', `${PATH_IMAGE}/${post.image}`)
                        } else {
                            $('#fieldOldImage').hide()
                        }
                        loading('hide', el)
                        $("#modal-data").modal('show')
                    })
            })
        }

        const deleteData = (id, el) => {
            $swal.fire({
                    title: 'Yakin ?',
                    text: "Ingin menghapus Kitab ini!",
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
                            let url = `{{ route('book.destroy', ['book' => ':id']) }}`
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
                                    table.ajax.reload()
                                })
                        })
                    }
                })
        }
        const detailData = id => {
            let url = `{{ route('bab.index', ['id' => ':id']) }}`
            url = url.replace(':id', id)
            window.location.href = `${url}`
        }
    </script>
@endsection
