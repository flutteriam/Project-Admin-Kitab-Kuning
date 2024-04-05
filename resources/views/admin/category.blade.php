@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Post / Kategori</h5>
            </div>
            <div class="card-body">
                <div class="float-right mb-3">
                    <button class="btn btn-primary" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="table-responsive">
                    <table id="table-data" class="display datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-data" tabindex="-1" role="dialog" aria-labelledby="modal-dataLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Nama Kategori</label>
                        <input type="text" placeholder="Nama Kategori" name="name" id="fieldNama" class="form-control">
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
<script>
    let table
    let type
    $(document).ready(() => {
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
            columns : [
                { data: "id", orderable: false, searchable: false },
                { data: "name" },
                { data: "aksi", orderable: false, searchable: false }
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

        $("#tambah-data").on('click', () => {
            $("#modal-data-label").html("Tambah Kategori")
            $("#btn-submit").html("Simpan")
            $("#form-data")[0].reset()
            type = 'POST'
            $("#modal-data").modal('show')
        })

        $("#form-data").on('submit', e => {
            loading('show', $(".modal-content"))
            e.preventDefault()
            if(type == "POST") {
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('category.store') }}`, $("#form-data").serialize())
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
            } else if(type == 'PUT') {
                new Promise((resolve, reject) => {
                    let url = `{{ route('category.update', ['category' => ':id']) }}`
                    url = url.replace(':id', $("#fieldId").val())
                    $axios.put(`${url}`, $("#form-data").serialize())
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
    })

    const editData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('category.edit', ['category' => ":id"]) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    type = 'PUT'
                    let category = data.data
                    $("#modal-data-label").html("Update Kategori")
                    $("#btn-submit").html("Update")
                    $("#fieldId").val(category.id)
                    $("#fieldNama").val(category.name)
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
            if(res.isConfirmed) {
                loading('show', el)
                new Promise((resolve, reject) => {
                    let url = `{{ route('category.destroy', ['category' => ':id']) }}`
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
        let url = `{{ route('post.index', ['id' => ":id"]) }}`
        url = url.replace(':id', id)
        window.location.href = `${url}`
    }
</script>
@endsection
