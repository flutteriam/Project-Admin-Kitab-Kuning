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
                <h5>Post / Nahwu</h5>
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
                <input type="hidden" name="id" id="fieldId">
                <div class="modal-body">

                    <button type="button" class="btn btn-success" id="btn-add-input-data" onclick="addInputData(this)">Tambah</button>

                    <hr>

                    <div id="container-input-data"></div>

                    {{-- <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="">Var</label>
                                <input type="text" name="arab" id="fieldArab" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="">Tags</label>
                                <select name="tags" id="fieldTag" class="form-control select2" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-block" onclick="delInputData(this)"><i class="fa fa-trash"></i></button>
                        </div>
                    </div> --}}
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
                let url = `{{ route('nahwu.update', ['nahwu' => ':id']) }}`
                url = url.replace(':id', $("#fieldId").val())
                let FD = new FormData($("#form-data")[0])
                FD.append('_method', "PUT")

                FD.delete('tags[]')
                $('.fieldTag').each((i, el_select_tag) => {
                    FD.append('tags[]', $(el_select_tag).select2("val"))
                })

                $axios.post(`${url}`, FD)
                    .then(({data}) => {
                        $('#modal-data').modal('hide')
                        loading('hide', $(".modal-content"))
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
            let url = `{{ route('nahwu.bab.ajax.detail', ['id' => ':id']) }}`
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

    const addInputData = async (el, nahwu = '', arr_tags = [], suggested_by = null) => {

        let option_select2 = ``
        let var_nawhu = ''
        let sugestBy = ""
        if(nahwu) {
            var_nawhu = nahwu.var
        }

        if(nahwu.sugest_admin) sugestBy = nahwu.sugest_admin.username
        if(nahwu.sugest_user) sugestBy = nahwu.sugest_user.name

        await $axios.get(`{{ route('tag.show') }}`)
                .then(({data}) => {
                    let tags = data.data
                    $.each(tags, (i, e) => {
                        let selected = arr_tags.includes(e.id) ? 'selected' : ''
                        option_select2 += `<option value="${e.id}" ${selected}>${e.nama}</option>`
                    })
                    // resolve(tags)
                })
                .catch(err => {
                    console.log('gagal mendapatkan tag')
                    // reject(err)
                })

        let input_add = `<div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Var</label>
                                <input type="text" name="var[]" id="fieldVar" class="form-control" value="${var_nawhu}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Tags</label>
                                <select name="tags[]" id="fieldTag" class="form-control select2 fieldTag" multiple>
                                    ${option_select2}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <p>Sugest By : </p>
                            <p>${sugestBy}</p>
                        </div>`

        @if (session('role') == 'admin')
            input_add += `<div class="col-md-2">`
            if(!nahwu.verified_at) {
                input_add += `<button type="button" class="btn btn-success" onclick="verified(this, ${nahwu.id})"><i class="fa fa-check-circle" aria-hidden="true"></i></button>`
            }
            input_add += `<button type="button" class="btn btn-danger" onclick="delInputData(this)"><i class="fa fa-trash"></i></button></div>`
        @endif

        @if (session('role') == 'contributor')
            if(USER_KEY == suggested_by) {
                input_add += `<div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-block" onclick="delInputData(this)"><i class="fa fa-trash"></i></button>
                        </div>`
            }
        @endif

        input_add += `</div>`

        $(el).parents('#modal-data').find('#container-input-data').append(input_add)
        $('.select2').select2()
    }

    const delInputData = (el) => {
        loading('show', el)
        $(el).parents('.row').remove()
        loading('hide', el)
    }

    const editData = (id, el) => {
        loading('show', el)

        $('#container-input-data').html('')
        $("#fieldId").val('')

        new Promise((resolve, reject) => {
            let url = `{{ route('nahwu.edit', ['nahwu' => ':id']) }}`
            url = url.replace(':id', id)
            $axios.get(`${url}`)
                .then(({data}) => {
                    let nahwu = data.data

                    $("#fieldId").val(nahwu.id)

                    $("#modal-data").modal('show')

                    nahwu.vars.forEach(nahwu => {
                        let arr_tag_id = nahwu.tags.map(tag => tag.id)
                        addInputData(document.getElementById('btn-add-input-data'), nahwu, arr_tag_id, nahwu.suggested_by)
                    });

                    $("#modal-data").modal('show')

                    // type = nahwu.vars.length == 0 ? 'POST' : 'PUT'
                    $("#modal-data-label").html("Nahwu")
                    $("#btn-submit").html("Save")
                    loading('hide', el)
                })
        })
    }

    const verified = (el, id) => {
        $axios.put(`{{ route('nahwu.verified') }}`, {id})
            .then(({data}) => {
                $swal.fire({
                    icon: 'success',
                    title: data.message.head,
                    text: data.message.body
                })
                $(el).remove()
            })
    }

    // const setDir = (id, type, el) => {
    //     new Promise((resolve, reject) => {
    //         let url = `{{ route('chapter.patch', ['id' => ':id', 'type' => ':type']) }}`
    //         url = url.replace(':id', id)
    //         url = url.replace(':type', type)
    //         $axios.patch(`${url}`)
    //             .then(({data}) => {
    //                 show_full_bait($("#inputNumberBait").val())
    //                 table.ajax.reload()
    //                 $swal.fire({
    //                     icon: 'success',
    //                     title: data.message.head,
    //                     text: data.message.body
    //                 })
    //             })
    //             .catch(err => throwErr(err))
    //     })
    // }

</script>
@endsection
