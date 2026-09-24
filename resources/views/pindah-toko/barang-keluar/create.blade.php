@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Barang Keluar Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Barang Keluar Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Barang Keluar</h2>
    <p class="section-lead">
        Berikut adalah data barang keluar toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pindah Barang</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control datepicker" required="" id="date" name="date"
                                    placeholder="Tanggal" min="1">
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number" value="PT - {{  sprintf('%07d', $pindahGudang === null ? "1" : $pindahGudang->id+1) }}">
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
                    <button class="btn btn-warning float-right" id="add-barang" type="button"><i class="fas fa-plus"></i>
                        Barang</button>
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
                    <button class="btn btn-primary" type="button" id="save">Simpan</button>
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

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
    });

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

    let index = 0;
    $('#add-barang').click(function() {
        $('.needs-validation').addClass('was-validated');
        $('.invalid-feeback-barang').hide();
        if($('.invalid-feedback:visible').length == 0){
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+index+'" data-id="'+index+'">'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control nama_barang" data-id="'+index+'" name="nama_barang" id="nama_barang_'+index+'" required=""></select>'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-nama_barang_'+index+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock Awal</label>'+
                    '<input type="text" name="stock_toko_awal" id="stock_toko_awal_'+index+'" class="form-control stock_toko_awal" readonly>'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-stock_toko_awal_'+index+'">'+
                        'Stock Toko Awal harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock Tujuan</label>'+
                    '<input type="text" name="stock_toko_tujuan" id="stock_toko_tujuan_'+index+'" class="form-control stock_toko_tujuan" readonly>'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-stock_toko_tujuan_'+index+'">'+
                        'Stock Toko Tujuan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Jumlah</label>'+
                    '<input type="number" min="0" data-id="'+index+'" name="jumlah" id="jumlah_'+index+'" class="form-control jumlah" required>'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-jumlah_'+index+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control serial_number" data-id="'+index+'" name="serial_number" id="serial_number_'+index+'" required="" multiple></select>'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-serial_number_'+index+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Keterangan</label>'+
                    '<input type="text" name="keterangan" id="keterangan_'+index+'" class="form-control keterangan">'+
                    '<div class="invalid-feedback invalid-feeback-barang feedback-keterangan_'+index+'">'+
                        'Keterangan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Action</label>'+
                '<br>'+
                    '<button data-id="'+index+'" type="button" class="btn btn-icon btn-danger btn-delete-barang"><i class="far fas fa-trash"></i></button>'+
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
            '</script'+'>'+
            '</div>');
            let id_toko = "{{ url('barang/toko') }}/"+$('#nama_toko_from').val();
        index ++;
        }
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

    $(document).on('change', '.serial_number', function() {
        let id = $(this).data('id');
        $('.feedback-serial_number_'+id).hide();
    });

    $(document).on('change', '.jumlah', function() {
        let id = $(this).data('id');
        $('.feedback-jumlah_'+id).hide();
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
        $('.row-detail-barang').each(function() {
            let id = $(this).data('id');
            if($('#stock_toko_awal_'+id).val() != 0 && $('#jumlah_'+id).val() != 0){
                if(parseInt($('#jumlah_'+id).val()) > parseInt($('#stock_toko_awal_'+id).val())){
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

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        $('.needs-validation-barang').addClass('was-validated');
        validationBarang();
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            let databarang = []
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.form-control').each(function(){
                databarang.push($("#"+$(this).attr('id')).val())
            });
            if(databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang yang akan dipindahkan!',
                });
            }else{
                $.ajax({
                    url: "{{ url('pindah-toko') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        $.ajax({
                            url: "{{ url('pindah-toko/barang') }}/"+response.id,
                            type: "post",
                            data: JSON.stringify(databarang),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function(result){
                                $('#create-modal').modal('hide');
                                Swal.fire({
                                    title: 'Success',
                                    icon: 'success',
                                    text: 'Data has been saved.',
                                }).then((result) => {
                                    window.location = '/pindah-toko/out';
                                });
                            }
                        });
                    }
                });
            }
        }
    });

    Array.prototype.chunk = function(n) {
        if (!this.length) {
            return [];
        }
        return [this.slice(0, n)].concat(this.slice(n).chunk(n));
    };

    $(document).on('click','.btn-delete-barang', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $('#row_barang_'+id).remove();
    });

});
</script>
@endsection
@endsection