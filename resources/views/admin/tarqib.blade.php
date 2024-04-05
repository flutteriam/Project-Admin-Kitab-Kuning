@extends('layouts.master')

@section('content')
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin: 5px !important;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>TARQIB</h5>
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
                <input type="hidden" name="id" id="fieldKataId">

                <input type="hidden" name="fieldTarqibStepStart" id="fieldTarqibStepStart">
                <input type="hidden" name="fieldTarqibStepEnd" id="fieldTarqibStepEnd">
                <input type="hidden" name="fieldTarqibStepNext" id="fieldTarqibStepNext">

                <input type="hidden" name="fieldColumnStore" id="fieldColumnStore">

                <div class="modal-body">

                    <h6 id="tarqibStepContainer" class="text-center"><span id="tarqibStepStart">0</span>/<span id="tarqibStepEnd">0</span> Pertanyaan</h6>
                    <div id="tarqibProgressContainer" class="progress">
                        <div id="tarqibProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div id="tarqib-log-container" class="m-t-20 p-10" style="width: 100%; border: 2px dashed #c4c4c4; background-color: #fff6e3;">
                        <h6 id="tarqib-log" class="m-b-0 f-w-600">Fiil &rarr; Madi &rarr; Mujjarod</h6>
                    </div>

                    <h6 id="tarqibOptionTitle" class="text-center m-t-20">Tentukan</h6>
                    <select name="fieldTarqibOption" id="fieldTarqibOption" autocomplete="off" class="form-control m-t-10">
                    </select>

                    <div id="tarqibSubmitContainer" class="m-t-20 text-center">
                        <button type="submit" class="btn btn-secondary" id="btn-submit">Selanjutnya</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="row">
                        <div class="col-6 col-lg-6 col-md-6">
                            <button type="button" onclick="deleteData(this)" class="btn btn-danger" id="delete-tarqib" style="float:left" >Hapus</button>
                        </div>
                        <div class="col-6 col-lg-6 col-md-6">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('vendor/select2/select2.full.min.js') }}"></script>
<script>
    let table
    let type
    const USER_KEY = `{{ session('key') }}`
    $(document).ready(() => {
        $(".select2").select2()

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

        $("#bab").on('change', () => {
            get_bait($("#bab").val(), 'container-detail')
        })

        $("#form-data").on('submit', e => {
            loading('show', $(".modal-content"))
            e.preventDefault()

            new Promise((resolve, reject) => {
                let kataId = $("#fieldKataId").val()

                let url = `{{ route('tarqib.update', ['tarqib' => ':id']) }}`
                url = url.replace(':id', kataId)
                let FD = new FormData($("#form-data")[0])
                FD.append('_method', "PUT")

                $axios.post(`${url}`, FD)
                    .then(({data}) => {
                        // $('#modal-data').modal('hide')
                        loading('hide', $(".modal-content"))
                        $swal.fire({
                            icon: 'success',
                            title: data.message.head,
                            text: data.message.body
                        })

                        progressTarqibAjax(kataId)
                    })
                    .catch(err => {
                        loading('hide', $(".modal-content"))
                        throwErr(err)
                    })
            })
        })

        $('#modal-data').on('hidden.bs.modal', function (e) {
            $("#bab").trigger('change')
        })
    })

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

    const get_bait = (id_bab, el) => {
        return new Promise((resolve, reject) => {
            let url = `{{ route('tarqib.bab.ajax.detail', ['id' => ':id']) }}`
            url = url.replace(':id', id_bab)
            $axios.get(`${url}`)
                .then(({data}) => {
                    $(`#${el}`).html(data)
                    resolve(data)
                })
                .catch(err => {
                    reject(err)
                })
        })
    }

    const editData = (id, el) => {
        loading('show', el)

        // $('#container-input-data').html('')
        $("#fieldId").val('')

        new Promise((resolve, reject) => {
            let url = `{{ route('tarqib.edit', ['tarqib' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    // let nahwu = data.data

                    // $("#fieldId").val(nahwu.id)

                    progressTarqib(data.data)

                    $("#modal-data").modal('show')

                    // nahwu.vars.forEach(nahwu => {
                    //     let arr_tag_id = nahwu.tags.map(tag => tag.id)
                    //     addInputData(document.getElementById('btn-add-input-data'), nahwu, arr_tag_id, nahwu.suggested_by)
                    // });

                    $("#modal-data").modal('show')

                    // type = nahwu.vars.length == 0 ? 'POST' : 'PUT'
                    $("#modal-data-label").html("Tarqib")
                    // $("#btn-submit").html("Save")
                    loading('hide', el)
                })
        })
    }

    const deleteData = (el) => {
        $swal.fire({
            title: 'Yakin ?',
            text: "Ingin menghapus Taqrib ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Ini!',
            cancelButtonText: 'Tidak'
        })
        .then(res => {
            if(res.isConfirmed) {
                id = $("#fieldKataId").val();
                loading('show', el)
        
                // $('#container-input-data').html('')
                $("#fieldId").val('')
        
                new Promise((resolve, reject) => {
                    let url = `{{ route('tarqib.destroy', ['tarqib' => ':id']) }}`
                    url = url.replace(':id', id)
                    $axios.delete(`${url}`)
                        .then(({data}) => {
                            get_bait($("#bab").val(), 'container-detail');
        
                            $("#modal-data").modal('hide')
        
                            loading('hide', el)
                        })
                })
            }
        })
    }

    const progressTarqibAjax = (kataId) => {
        new Promise((resolve, reject) => {
            let url = `{{ route('tarqib.edit', ['tarqib' => ':id']) }}`
            url = url.replace(':id', kataId)
            $axios.get(`${url}`)
                .then(({data}) => {
                    progressTarqib(data.data)
                })
        })
    }

    const progressTarqib = (tarqibData) => {
        $("#fieldKataId").val(tarqibData.kata_id)

        $('#fieldTarqibStepStart').val(tarqibData.step_current)
        $('#fieldTarqibStepEnd').val(tarqibData.step_end)
        $('#fieldTarqibStepNext').val(tarqibData.step_next)
        $('#fieldColumnStore').val(tarqibData.column_store)

        $('#tarqibStepStart').html(tarqibData.step_current)
        $('#tarqibStepEnd').html(tarqibData.step_end)

        $('#tarqibProgress').css('width', `${(tarqibData.step_current / tarqibData.step_end) * 100}%`)

        // if (tarqibData.step_current == 0) $('#tarqibStepContainer, #tarqibProgressContainer, #tarqib-log-container').addClass('d-none')
        // else  $('#tarqibStepContainer, #tarqibProgressContainer, #tarqib-log-container').removeClass('d-none')
        if (tarqibData.step_current == 1) $('#tarqib-log-container').addClass('d-none')
        else  $('#tarqib-log-container').removeClass('d-none')

        $('#tarqib-log').html(tarqibData.tarqib_log.join(' &rarr; '))

        if (tarqibData.tarqib_option.length == 0) $('#tarqibOptionTitle, #fieldTarqibOption, #tarqibSubmitContainer').addClass('d-none')
        else $('#tarqibOptionTitle, #fieldTarqibOption, #tarqibSubmitContainer').removeClass('d-none')
        let fieldTarqibOption = ``
        $.each(tarqibData.tarqib_option, (i, e) => {
            fieldTarqibOption += `<option value="${e}">${i}</option>`
        })
        $('#fieldTarqibOption').html(fieldTarqibOption)
    }

</script>
@endsection
