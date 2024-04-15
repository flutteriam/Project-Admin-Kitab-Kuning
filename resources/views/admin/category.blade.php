@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="float-right mb-3">
                        <button class="btn btn-primary" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table id="table-data" class="display datatables">
                            <thead>
                                <tr>
                                    <th>Urutan</th>
                                    <th>Nama</th>
                                    <th>Cover</th>
                                    <th>Status</th>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-data-label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-data" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="fieldId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Nama Kategori</label>
                            <input type="text" placeholder="Nama Kategori" name="name" id="fieldNama"
                                class="form-control">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <br>
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
                            <label for="">Cover</label>
                            <br>
                            <input type="file" name="image" id="fieldImage" accept="image/*">
                            <br>
                            <img id="imagePreview" src="#" alt="Image Preview"
                                style="display: none; width: 100px; height: 100px;">
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
    <script>
        let table
        let type
        $(document).ready(() => {
            $('#fieldImage').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });
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
                    url: "{{ route('api.category_datatable') }}",
                    type: "POST"
                },
                order: [0, 'asc'],
                columns: [{
                        data: "order"
                    },
                    {
                        data: "name"
                    },
                    {
                        data: 'cover',
                        name: 'cover',
                        render: function(data, type, full, meta) {
                            return '<img src="' + '{{ asset('storage') }}/' + data +
                                '" width="100" height="100">';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, full, meta) {
                            if (data === 1) {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-danger">Inactive</span>';
                            }
                        }
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ],
            })

            $("#tambah-data").on('click', () => {
                $("#modal-data-label").html("Tambah Kategori")
                $("#btn-submit").html("Simpan")
                $('#fieldImage').prop('required', true)
                $('#status').prop('checked', true);
                $('#orderForm').css('display', 'none')
                $('#imagePreview').css('display', 'none')
                $("#form-data")[0].reset()
                type = 'POST'
                $("#modal-data").modal('show')
            })

            $("#form-data").on('submit', e => {
                loading('show', $(".modal-content"))
                e.preventDefault()
                if (type == "POST") {
                    new Promise((resolve, reject) => {
                        var formData = new FormData($('#form-data')[0]);
                        $axios.post(`{{ route('category.store') }}`, formData)
                            .then(({
                                data
                            }) => {
                                $('#modal-data').modal('hide')
                                loading('hide', $(".modal-content"))
                                $('#fieldOrder').append(
                                    '<option value="' + data.count + '">' + data.count +
                                    '</option>');
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
                        var formData = new FormData($('#form-data')[0]);
                        let url = `{{ route('category.update', ['category' => ':id']) }}`
                        url = url.replace(':id', $("#fieldId").val())
                        $axios.post(`${url}?_method=PUT`, formData)
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
                }
            })
        })

        const editData = (id, el) => {
            loading('show', el)
            new Promise((resolve, reject) => {
                let url = `{{ route('category.edit', ['category' => ':id']) }}`
                url = url.replace(':id', id)
                $axios.get(`${url}`)
                    .then(({
                        data
                    }) => {
                        type = 'PUT'
                        let category = data.data
                        $("#modal-data-label").html("Update Kategori")
                        $("#form-data")[0].reset()
                        $("#btn-submit").html("Update")
                        $("#fieldId").val(category.id)
                        $("#fieldNama").val(category.name)
                        $('#status').prop('checked', category.status == 1);
                        $('#orderForm').css('display', 'block')
                        $('#fieldOrder').val(category.order)
                        $('#fieldImage').prop('required', false)
                        $('#imagePreview').attr('src', `{{ asset('storage') }}/` + category.cover);
                        $('#imagePreview').css('display', 'block');
                        loading('hide', el)
                        $("#modal-data").modal('show')
                    })
            })
        }

        const deleteData = (id, el) => {
            $swal.fire({
                    title: 'Yakin ?',
                    text: "Ingin menghapus kategori ini!",
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
                            let url = `{{ route('category.destroy', ['category' => ':id']) }}`
                            url = url.replace(':id', id)
                            $axios.delete(`${url}`)
                                .then(({
                                    data
                                }) => {
                                    loading('hide', el)
                                    $('#fieldOrder option:last-child').remove();
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
    </script>
@endsection
