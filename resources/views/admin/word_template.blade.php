@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="float-left">
                    <h5>Template Kata</h5>
                </div>
                <div class="float-right">
                    <button class="btn btn-primary" id="tambah-data"><i class="fa fa-plus"></i> Tambah</button>
                </div>
            </div>
            <div class="card-body">
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
<script>
    let table
    translate1 = document.getElementById('fieldTranslate')
    word1 = document.getElementById('fieldBasicWrod')
    CKEDITOR.replace(translate1)
    CKEDITOR.replace(word1)
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
                url: "{{ route('api.word_template_datatable') }}",
                type: "POST"
            },
            columns : [
                { data: "id", orderable: false, searchable: false },
                { data: "arab" },
                { data: "arab_harokat" },
                { data: "translate" },
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
            let FD = new FormData($("#form-data")[0])
            FD.append('translate', CKEDITOR.instances['fieldTranslate'].getData())
            FD.append('basic_word', CKEDITOR.instances['fieldBasicWrod'].getData())
            if(type == "POST") {
                new Promise((resolve, reject) => {
                    $axios.post(`{{ route('template.store') }}`, FD)
                        .then(({data}) => {
                            $('#modal-data').modal('hide')
                            loading('hide', $(".modal-content"))
                            table.ajax.reload()
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
                    let url = `{{ route('template.update', ['template' => ':id']) }}`
                    url = url.replace(':id', $("#fieldId").val())
                    FD.append('_method', "PUT")
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

        $("#tambah-data").on('click', () => {
            $("#modal-data-label").html("Tambah Kata")
            $("#btn-submit").html("Simpan")
            $("#form-data")[0].reset()
            CKEDITOR.instances['fieldTranslate'].setData('')
            CKEDITOR.instances['fieldBasicWrod'].setData('')
            type = 'POST'
            $("#modal-data").modal('show')
        })
    })

    const editData = (id, el) => {
        loading('show', el)
        new Promise((resolve, reject) => {
            let url = `{{ route('template.edit', ['template' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let template = data.data
                    $("#fieldId").val(template.id)
                    $("#fieldArab").val(template.arab)
                    $("#fieldArabHarokat").val(template.arab_harokat)
                    CKEDITOR.instances['fieldTranslate'].setData(template.translate_word)
                    CKEDITOR.instances['fieldBasicWrod'].setData(template.basic_word)
                    type = 'PUT'
                    $("#modal-data-label").html("Update Kata")
                    $("#btn-submit").html("Update")
                    $("#modal-data").modal('show')
                    loading('hide', el)
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
                    let url = `{{ route('template.destroy', ['template' => ':id']) }}`
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
</script>
@endsection
