@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Edit Barang Keluar Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Edit Barang Keluar Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Edit Barang Keluar</h2>
    <p class="section-lead">
        Berikut adalah data edit barang keluar toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Pindah Barang</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control datepicker" required="" id="date" name="date"
                                    placeholder="Tanggal" min="1" value="{{ date('d-m-Y', strtotime($pindahGudang->date))}}">
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number" value="{{ $pindahGudang->no_ref }}" readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Toko Awal</label>
                                <select class="form-control" name="nama_toko_from" id="nama_toko_from" required=""></select>
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Nama Toko awal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Toko Tujuan</label>
                                <select class="form-control" name="nama_toko_to" id="nama_toko_to" required=""></select>
                                <div class="invalid-feedback feedback-nama_toko_to">
                                    Nama Toko tujuan harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- <button class="btn btn-warning float-right" id="edit-add-barang" type="button"><i class="fas fa-plus"></i>
                        Barang</button> -->
                    <hr>
                    <br>
                    <div id="edit-detail-barang">
                        <form class="needs-validation-barang" novalidate="" id="form-detail-barang">
                            <div id="detail-barang">

                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="button" id="update" data-id="{{$pindahGudang->id}}">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('modal')
<!-- Modal -->
@endsection
@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });
    let data = @json($pindahGudang);
    let row_edit = 1;

    $('#nama_toko_from').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    $('#nama_toko_to').empty().append('<option selected value="'+data.toko_to.id+'">'+data.toko_to.nama_toko+'</option>');

    @if (Auth::user()->status == 2)
        $('#nama_toko_from').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
        $('#nama_toko_from').prop('disabled', true);
        $("#nama_toko_to").select2({
            placeholder: "Pilih Toko",
            ajax: {
                url: "{{ url('toko/except') }}/"+$('#nama_toko_from').val(),
                dataType: "json",
                cache: true,
                data: function(params) {
                    return {
                        term: params.term || "",
                        page: params.page || 1
                    }
                },
            }
        });
    @else
        $("#nama_toko_to").select2({
            placeholder: "Pilih Toko",
        });
    @endif

    getDetailBarang();

    function getDetailBarang() {
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id =  {{$pindahGudang->id}}
        let detail_barang = '';
        $.each(data.detail_barang_keluar, function (i,v) {
            $('#detail-barang').html('');
            let id_serial_number = data.data_detail_serial_number[i].id;
            let name_serial_number = data.data_detail_serial_number[i].serial_number;
            if(name_serial_number == null){
                name_serial_number = 'No S/N';
            }
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            let id_barang = data.data_detail_barang[i].id;
            let nama_barang = data.data_detail_barang[i].nama_product;
            if(keterangan == null){
                keterangan = '';
            }
            let disabled = '';
            if(data.data_detail_barang[i].wajib_serial_number != 1){
                disabled = 'disabled';
                name_serial_number = '';
            }
            let url_toko_edit = "{{ url('barang/select') }}";
            let url_serial_number = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko_from').val()+"/id_barang/"+id_barang;
            detail_barang += '<div class="form-row edit-data" id="row_barang_edit_'+row_edit+'" data-id="'+row_edit+'">'+
                                '<input type="hidden" name="id_detail_barang" id="id_detail_barang_'+row_edit+'" class="form-control id_detail_barang edit_detail_barang" value="'+detail_barang_id+'">'+
                                '<div class="form-group col-md-3">'+
                                    '<label>Pilih Barang</label>'+
                                    '<select class="form-control edit_nama_barang edit_detail_barang" data-id="'+row_edit+'" name="edit_nama_barang" id="edit_nama_barang_'+row_edit+'" required=""></select>'+
                                    '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                        'Barang harus dipilih.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-1">'+
                                    '<label>Stock Awal</label>'+
                                    '<input type="text" data-id="'+row_edit+'" name="stock_toko_awal" id="stock_toko_awal_'+row_edit+'" class="form-control  edit_detail_barang" readonly>'+
                                    '<div class="invalid-feedback feedback-stock_toko_awal_'+row_edit+'">'+
                                        'Stock Toko Awal harus dipilih.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-1">'+
                                    '<label>Stock Tujuan</label>'+
                                    '<input type="text" data-id="'+row_edit+'" name="stock_toko_tujuan" id="stock_toko_tujuan_'+row_edit+'" class="form-control stock_toko_tujuan edit_detail_barang" readonly>'+
                                    '<div class="invalid-feedback feedback-stock_toko_tujuan_'+row_edit+'">'+
                                        'Stock Toko Tujuan harus dipilih.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-1">'+
                                    '<label>Jumlah</label>'+
                                    '<input value="1" data-id="'+row_edit+'" type="text" name="jumlah" id="jumlah_'+row_edit+'" class="form-control jumlah edit_detail_barang" required readonly>'+
                                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                                        'Jumlah harus diisi.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-3">'+
                                    '<label>Pilih Serial Number</label>'+
                                    '<select '+disabled+' class="form-control edit_serial_number edit_detail_barang" name="edit_serial_number" id="edit_serial_number_'+row_edit+'" required="" data-id="'+row_edit+'"></select>'+
                                    '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                        'Serial Number harus dipilih.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-2">'+
                                    '<label>Keterangan</label>'+
                                    '<input data-id="'+row_edit+'" type="text" name="edit_keterangan" id="edit_keterangan_'+row_edit+'" class="form-control edit_keterangan edit_detail_barang" value="'+keterangan+'">'+
                                    '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                        'Keterangan harus dipilih.'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group col-md-1">'+
                                    '<label>Action</label>'+
                                '<br>'+
                                    '<button data-id="'+detail_barang_id+'" data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang-edit"><i class="far fas fa-trash"></i></button>'+
                                '</div>'+
                                '<script type="text/javascript">'+
                                    '$("#edit_nama_barang_'+row_edit+'").empty().append("<option selected value='+id_barang+'>'+nama_barang+'  </option>");'+
                                    '$("#edit_serial_number_'+row_edit+'").empty().append("<option selected value='+id_serial_number+'>'+name_serial_number+'  </option>");'+
                                    '$(".edit_nama_barang").select2({'+
                                        'placeholder:"Pilih Barang",'+
                                        'tags: true,'+
                                        'ajax: {'+
                                            'url: "'+url_toko_edit+'",'+
                                            'dataType: "json",'+
                                            'cache: true,'+
                                            'data: function(params) {'+
                                                'return {'+
                                                    'term: params.term || "",'+
                                                    'page: params.page || 1'+
                                                '}'+
                                            '},'+
                                        '}'+
                                    '});'+
                                    '$("#edit_serial_number_'+row_edit+'").select2({'+
                                        'placeholder:"Pilih Serial Number",'+
                                        'tags: true,'+
                                        'ajax: {'+
                                            'url: "'+url_serial_number+'",'+
                                            'dataType: "json",'+
                                            'cache: true,'+
                                            'data: function(params) {'+
                                                'return {'+
                                                    'term: params.term || "",'+
                                                    'page: params.page || 1'+
                                                '}'+
                                            '},'+
                                        '}'+
                                    '});'+
                                    '$(".select2-container").css("width","100%");'+
                                '</script'+'>'+
                            '</div>';
            
            let databarangid = {}
            databarangid.id_toko = $('#nama_toko_from').val();
            databarangid.id_barang = id_barang;
            getTotalBarangByTokoFrom(databarangid, row_edit);

            let data_tobarangid = {}
            data_tobarangid.id_toko = $('#nama_toko_to').val();
            data_tobarangid.id_barang = id_barang;
            getTotalBarangByTokoTo(data_tobarangid, row_edit);

            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        swal.close();   
        $('#update').attr('data-id', id);
    }

    function getTotalBarangByTokoFrom(data, row) {
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko-new') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+row).val(response);
            }
        });
    }

    function getTotalBarangByTokoTo(data, row) {
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko-new') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_tujuan_"+row).val(response);
            }
        });
    }

    $('#nama_toko_from').select2({
        placeholder:'Pilih Toko',
        ajax: {
            url: '{{ url("toko/select") }}',
            dataType: 'json',
            cache: true,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
        }
    });

    $('#nama_toko_to').select2({
        placeholder:'Pilih Toko',
        ajax: {
            url: "{{ url('toko/except') }}/"+$('#nama_toko_from').val(),
            dataType: 'json',
            cache: true,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
        }
    });

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
    });

    $(document).on("select2:select","#nama_toko_from", function(e){
        $("#nama_toko_to").select2({
            placeholder: "Pilih Toko",
            ajax: {
                url: "{{ url('toko/except') }}/"+$(this).val(),
                dataType: "json",
                cache: true,
                data: function(params) {
                    return {
                        term: params.term || "",
                        page: params.page || 1
                    }
                },
            }
        });
        $(".nama_barang, .serial_number").val(null).trigger("change");
        $(".stock_toko_awal, .stock_toko_tujuan, .jumlah").val('');
        $(".nama_barang").select2({
            placeholder: "Pilih Barang",
            ajax: {
                url: "{{ url('barang/toko') }}/"+$(this).val(),
                dataType: "json",
                cache: true,
                data: function(params) {
                    return {
                        term: params.term || "",
                        page: params.page || 1
                    }
                },
            }
        });
    });

    $(document).on("select2:select",".nama_barang", function(e){
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id = $(this).data('id');
        let data = {}
        data.id_toko = $('#nama_toko_from').val();
        data.id_barang = $(this).val();
        
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko-new') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+id).val(response);
            }
        });

        let data_to = {}
        data_to.id_toko = $('#nama_toko_to').val();
        data_to.id_barang = $(this).val();

        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko-new') }}",
            type: "post",
            data: JSON.stringify(data_to),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_tujuan_"+id).val(response);
                Swal.close();
            }
        });

         $.ajax({
            url: "{{ url('barang') }}/"+$(this).val(),
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                if(response.wajib_serial_number == 1){
                    $('#serial_number_'+id).prop('disabled', false);
                }else{
                    $('#serial_number_'+id).prop('disabled', true);
                }
            }
        });


        let url = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko_from').val()+"/id_barang/"+$(this).val();
        $("#serial_number_"+$(this).data('id')).select2({
            placeholder: "Pilih Serial Number",
            ajax: {
                url: url,
                dataType: "json",
                cache: true,
                data: function(params) {
                    return {
                        term: params.term || "",
                        page: params.page || 1
                    }
                },
            }
        });
    });

    $(document).on("select2:select",".edit_nama_barang", function(e){
        let url = "{{ url('serial-number/id_toko/') }}/"+$('#edit_nama_toko_from').val()+"/id_barang/"+$(this).val();
        $("#serial_number_"+$(this).data('id')).select2({
            placeholder: "Pilih Serial Number",
            ajax: {
                url: url,
                dataType: "json",
                cache: true,
                data: function(params) {
                    return {
                        term: params.term || "",
                        page: params.page || 1
                    }
                },
            }
        });
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('pindah-toko/data-out') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'date', name: 'date' },
            { data: 'no_ref', name: 'no_ref' },
            { data: 'toko.nama_toko', name: 'toko.nama_toko' },
            { data: 'toko_to.nama_toko', name: 'toko_to.nama_toko' },
            { data: 'status_name', name: 'status_name' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    function validationBarang() {
        $('.edit-data').each(function() {
            let id = $(this).data('id');
            if($('#stock_toko_awal_'+id).val() != 0 && $('#jumlah_'+id).val() != 0){
                if($('#jumlah_'+id).val() > $('#stock_toko_awal_'+id).val()){
                    $('.feedback-jumlah_'+id).show();
                    $('.feedback-jumlah_'+id).html('Sesuaikan jumlah barang');
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Jumlah lebih besar dari stock',
                    });
                }
                if($('#serial_number_'+id).prop("disabled") != true){
                    if($('#jumlah_'+id).val() != $('#serial_number_'+id).val().length){
                        $('.feedback-serial_number_'+id).show();
                        $('.feedback-serial_number_'+id).html('Sesuaikan Jumlah Serial Number');
                        $('#serial_number_'+id).focus();
                        return Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Jumlah barang tidak sesuai dengan jumlah serial number, jumlah barang '+$('#jumlah_'+id).val()+' sementara jumlah serial number '+$('#serial_number_'+id).val().length,
                        });
                    }else{
                        $('.feedback-serial_number_'+id).hide();
                    }
                }
            }else{
                if($('#stock_toko_awal_'+id).val() == '0'){
                    $('.feedback-stock_toko_awal_'+id).show();
                    $('.feedback-stock_toko_awal_'+id).html('Stock Kosong');
                }
                if($('#jumlah_'+id).val() == '0'){
                    $('.feedback-jumlah_'+id).show();
                    $('.feedback-jumlah_'+id).html('Masukkan Jumlah');
                    $('#jumlah'+id).focus();
                }
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Periksa Stock Barang dan Jumlah Barang',
                });
            }
        })
    }

    Array.prototype.chunk = function(n) {
        if (!this.length) {
            return [];
        }
        return [this.slice(0, n)].concat(this.slice(n).chunk(n));
    };

    $(document).on('click', '#edit-add-barang', function() {
        $('.edit-needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row edit-data" id="row_barang_edit_'+row_edit+'">'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control detail_barang_new nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-data_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock Awal</label>'+
                    '<input type="text" name="stock_toko_awal" id="stock_toko_awal_'+row_edit+'" class="form-control detail_barang_new stock_toko_awal" readonly>'+
                    '<div class="invalid-feedback feedback-stock_toko_awal_'+row_edit+'">'+
                        'Stock Toko Awal harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock Tujuan</label>'+
                    '<input type="text" name="stock_toko_tujuan" id="stock_toko_tujuan_'+row_edit+'" class="form-control detail_barang_new stock_toko_tujuan" readonly>'+
                    '<div class="invalid-feedback feedback-stock_toko_tujuan_'+row_edit+'">'+
                        'Stock Toko Tujuan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Jumlah</label>'+
                    '<input type="number" name="jumlah" id="jumlah_'+row_edit+'" class="form-control detail_barang_new jumlah" required >'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control detail_barang_new serial_number" name="serial_number" id="serial_number_'+row_edit+'" multiple=""></select>'+
                    '<div class="invalid-feedback feedback-data_serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Keterangan</label>'+
                    '<input type="text" name="keterangan" id="keterangan_'+row_edit+'" class="form-control detail_barang_new keterangan">'+
                    '<div class="invalid-feedback feedback-data_keterangan_'+row_edit+'">'+
                        'Keterangan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Action</label>'+
                '<br>'+
                    '<button data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang-edit"><i class="far fas fa-trash"></i></button>'+
                '</div>'+
                '<script type="text/javascript">'+
                '$(".nama_barang").select2({'+
                    'placeholder: {'+
                        'id: -1,'+
                        'text: "Pilih Barang"'+
                    '},'+
                    'minimumInputLength: 3,'+
                    'ajax: {'+
                        'url: "'+url_toko+'",'+
                        'dataType: "json",'+
                        'cache: true,'+
                        'async: false,'+
                        'data: function(params) {'+
                            'return {'+
                                'term: params.term || "",'+
                                'page: params.page || 1'+
                            '}'+
                        '},'+
                    '}'+
                '});'+
                '$(".select2-container").css("width","100%");'+
                '$(".serial_number").select2({'+
                    'placeholder:"Pilih Serial Number",'+
                '});'+
            '</script'+'>'+
            '</div>');
        row_edit ++;
        }
    });

    $(document).on('click','.btn-delete-barang-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        let row = $(this).data('row');
        if(id == undefined){
            $('#row_barang_edit_'+row).remove();
        }else{
            let data = {}
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('pindah-toko/out/delete-detail') }}/"+id,
                        type: "get",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(result){
                            $('#row_barang_edit_'+row).remove();
                        }
                    });
                }
            })
        }
    });

    $(document).on('click','#update', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        $('.needs-validation-barang').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            let databarangedit = []
            let databarangnew = []
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.edit_detail_barang').each(function(){
                databarangedit.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-barang').find('.detail_barang_new').each(function(){
                databarangnew.push($("#"+$(this).attr('id')).val())
            });

            if(databarangedit.length == 0 && databarangnew.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang yang akan dipindahkan!',
                })
            }else{
                let id = $(this).data('id');
                data.databarangedit = databarangedit;
                data.databarangnew = databarangnew;
                $.ajax({
                    url: "{{ url('pindah-toko/out/update') }}/"+id,
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Data has been updated.',
                        }).then((result) => {
                            window.location = '/pindah-toko/out';
                        });
                    },
                    error: function(error){
                        Swal.fire({
                            title: 'Error !',
                            icon: 'error',
                            text: error.responseJSON.message,
                        })
                    }
                });
            }
        }
    });
});
</script>
@endsection
@endsection