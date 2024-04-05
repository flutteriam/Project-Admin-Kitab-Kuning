@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Post / Tag</h5>
            </div>
            <div class="card-body">
                <div class="float-right mb-3">
                    <button class="btn btn-primary" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="table-responsive table-striped">
                    <table id="table-data" class="display datatables">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
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
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Nama Tag</label>
                        <input type="text" placeholder="Nama Tag" name="nama" id="fieldNama" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Deskripsi Tag</label>
                        <textarea type="text" placeholder="Deskripsi Tag" name="deskripsi" id="fieldDescription" class="form-control"></textarea>
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

<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="modal-viewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-view-label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-data" method="POST">
                @csrf
                <input type="hidden" name="id" id="modal-view-fieldId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Nama Tag</label>
                        <input type="text" placeholder="Nama Tag" name="nama" disabled="disabled" id="modal-view-fieldNama" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Deskripsi Tag</label>
                        <p id="modal-view-description"></p>
                    </div>
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
        CKEDITOR.replace('deskripsi')

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
                url: "{{ route('api.tag_datatable') }}",
                type: "POST"
            },
            columns : [
                { data: "id", orderable: false, searchable: false },
                // { data: "nama" },
                { data: 'nama', name: 'nama',
                            render:function (data) {
                                // console.log(full);
                                return `<span style="font-size:larger">${data}</span>`
                                // return '<a href="'+loc+'/'+data+'" data-popup="lightbox"> <img src="'+loc+'/'+data+'" class="img-rounded img-preview" </a>';
                            }
                },
                { data: "deskripsi", visible:false, searchable: false },
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
            $("#modal-data-label").html("Tambah Tag")
            $("#btn-submit").html("Simpan")
            $("#form-data")[0].reset()
            CKEDITOR.instances['fieldDescription'].setData('')
            type = 'POST'
            $("#modal-data").modal('show')
        })

        $("#form-data").on('submit', e => {
            loading('show', $(".modal-content"))
            e.preventDefault()

            let FD = new FormData($("#form-data")[0])
            FD.set('deskripsi', CKEDITOR.instances['fieldDescription'].getData())

            if(type == "POST") {
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('tag.store') }}`, FD)
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
                FD.append('_method', 'PUT')
                new Promise((resolve, reject) => {
                    let url = `{{ route('tag.update', ['tag' => ':id']) }}`
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
    })

    const editData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('tag.edit', ['tag' => ":id"]) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    type = 'PUT'
                    let tag = data.data
                    $("#modal-data-label").html("Update Tag")
                    $("#btn-submit").html("Update")
                    $("#fieldId").val(tag.id)
                    $("#fieldNama").val(tag.nama)
                    CKEDITOR.instances['fieldDescription'].setData(tag.deskripsi)
                    loading('hide', el)
                    $("#modal-data").modal('show')
                })
        })
    }

    const viewData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('tag.edit', ['tag' => ":id"]) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    type = 'PUT'
                    let tag = data.data
                    $("#modal-view-label").html("Detail Tag")
                    $("#modal-view-fieldId").val(tag.id)
                    $("#modal-view-fieldNama").val(tag.nama)
                    $("#modal-view-description").html(tag.deskripsi)
                    
                    loading('hide', el)
                    $("#modal-view").modal('show')
                })
        })
    }

    const deleteData = (id, el) => {
        $swal.fire({
            title: 'Yakin ?',
            text: "Ingin menghapus tag ini!",
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
                    let url = `{{ route('tag.destroy', ['tag' => ':id']) }}`
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

    const copyUrlTag = el => {
        navigator.clipboard.writeText($(el).data('url_copy'));

        $swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Mecopy URL tag'
        })
    }
</script>
@endsection
