@extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>Edit Barang Keluar</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Edit Barang Keluar</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Edit Barang Keluar</h2>
    <p class="section-lead">
        Berikut adalah data edit barang keluar toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Edit Barang Keluar</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control datepicker" required="" id="date" name="date"
                                    placeholder="Tanggal" value="{{ date('d-m-Y', strtotime($penjualan->date)) }}">
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number" value="{{ $penjualan->kode_barang_keluar }}" readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Toko</label>
                                <select class="form-control" name="nama_toko" id="nama_toko" required=""></select>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Toko harus diisi.
                                </div>
                            </div>
                             <div class="form-group col-md-3">
                                <label>Nama Sales</label>
                                @php $all_signatures = \App\Models\Signature::all(); @endphp
                                <select class="form-control select2" required="" id="nama_sales" name="nama_sales">
                                    <option value="">Pilih Sales</option>
                                    @foreach($all_signatures as $signature)
                                        <option value="{{ $signature->name }}" {{ $penjualan->nama_sales == $signature->name ? 'selected' : '' }}>{{ $signature->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback feedback-nama_sales">
                                    Nama Sales harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Nama Pembeli</label>
                                <input type="text" class="form-control" required="" id="nama_penerima"
                                    name="nama_penerima" value="{{ $penjualan->nama_penerima }}">
                                <div class="invalid-feedback feedback-nama_penerima">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_penerima" name="alamat_penerima"
                                    value="{{ $penjualan->alamat_penerima }}">
                                <div class="invalid-feedback feedback-alamat_penerima">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon_penerima" name="telepon_penerima"
                                    value="{{ $penjualan->telepon_penerima }}">
                                <div class="invalid-feedback feedback-telepon">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Detail Barang</h4>
                    <div class="card-header-action">
                        <button class="btn btn-success float-right" id="add-barang" type="button"><i
                                class="fas fa-plus"></i>
                            Barang</button>
                    </div>
                </div>
                <div class="card-body">
                    <form class="needs-validation-barang" novalidate="">
                        <div id="detail-barang">

                        </div>
                    </form>
                </div>
            </div>
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Keterangan</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation-payment" novalidate="">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan"
                                    style="height: 150px;">{{ $penjualan->keterangan }}</textarea>
                                <div class="invalid-feedback feedback-keterangan">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
                    <!-- <button class="btn btn-warning save" data-button="draft" type="button" id="draft">Save as
                        Draft</button> -->
                    <button class="btn btn-primary save" data-button="save" type="button" id="save">Simpan</button>
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
    let data = @json($penjualan);
    let row_edit = 1;
    $('#nama_toko').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    @if (Auth::user()->status == 2)
        $('#nama_toko').prop('disabled', true);
    @endif
    getDetailBarang();
    function getDetailBarang() {
        let id =  {{$penjualan->id}}
        let detail_barang = '';
        $.each(data.data_barang_keluar, function (i,v) {
            $('#detail-barang').html('');
            let id_serial_number = data.barang[i].id;
            let name_serial_number = data.barang[i].serial_number;
            let disabled = 'disabled';
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            let id_barang = data.detail_barang[i].id;
            let nama_barang = data.detail_barang[i].nama_product;
            let url_toko_edit = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_serial_number = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+id_barang;
            // let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_toko = "{{ url('barang/select') }}";
            detail_barang += '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'" data-edit="'+row_edit+'">'+
                '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.id+'" class="edit_detail_barang barang_existing">'+
                '<div class="form-group col-md-4">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control edit_detail_barang nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock</label>'+
                    '<input type="text" name="stock_toko_awal" id="stock_toko_awal_'+row_edit+'" class="form-control edit_detail_barang stock_toko_awal" readonly>'+
                    '<div class="invalid-feedback feedback-stock_toko_awal_'+row_edit+'">'+
                        'Stock Toko Awal harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Jumlah</label>'+
                    '<input value="1" type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control edit_detail_barang jumlah" required data-id="'+row_edit+'" readonly>'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-4">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control edit_detail_barang serial_number" name="serial_number" id="serial_number_'+row_edit+'" required="" '+disabled+'></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Action</label>'+
                '<br>'+
                    '<button data-id="'+v.id+'" data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang"><i class="far fas fa-trash"></i></button>'+
                '</div>'+
                '<script type="text/javascript">'+
                    '$("#nama_barang_'+row_edit+'").empty().append("<option selected value='+id_barang+'>'+nama_barang+'  </option>");'+
                    '$("#serial_number_'+row_edit+'").empty().append("<option selected value='+id_serial_number+'>'+name_serial_number+'  </option>");'+
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
                '$("#serial_number_'+row_edit+'").select2({'+
                    'placeholder:"Pilih Serial Number",'+
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
            databarangid.id_toko = $('#nama_toko').val();
            databarangid.id_barang = id_barang;
            getTotalBarang(databarangid, row_edit);

            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        $('#save').attr('data-id', id);
    }

    $('#nama_toko').select2({
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

    function getTotalBarang(data, row){
         $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+row).val(parseInt(response)+1);
                Swal.close();
            }
        });
    }

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
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
        data.id_toko = $('#nama_toko').val();
        data.id_barang = $(this).val();
        let url_barang = '';
        let user_status = {{ Auth::user()->status }};
        // if(user_status == 1){
        //     url_barang = "{{ url('barang/stock') }}";
        // }else{
            url_barang = "{{ url('barang/get-total-barang-by-toko-new') }}";
        // }
        
        $.ajax({
            url: url_barang,
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                if(response.total !== 0){
                    $(".feedback-stock_toko_awal_"+id).hide();
                }
                $("#stock_toko_awal_"+id).val(response);
                Swal.close();
            }
        });

        $("#serial_number_"+$(this).data('id')).val(null).trigger("change");
        let url = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+$(this).val();
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
        
        let barang = {}
        barang.id = $(this).val();

        $.ajax({
            url: "{{ url('stock-in/price-list') }}",
            type: "post",
            data: JSON.stringify(barang),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                if(response.length > 0){
                    $("#harga_"+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
                    $('#subtotal_'+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
                }else{
                    $("#harga_"+id).val(0);
                    $('#subtotal_'+id).val(0);
                }
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

        // let sum  = 0;
        // $('.subtotal').each(function() {
        //     console.log($(this).val());
        //     sum += parseFloat($(this).val().split(",").join(""));  
        // });
        // if($('#flexCheckDefault').is(':checked')){
        //     $('#input_total').val(sum.toLocaleString('en-US'));
        //     let grandtotal = parseFloat(sum) + parseFloat(sum * 11 / 100);
        //     $('#grand_total').val(grandtotal.toLocaleString('en-US'));
        //     if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
        //         let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
        //         $('#sisa').val(sisa.toLocaleString('en-US'));
        //     }
        //     $('#input_ppn').val((sum * 11 / 100).toLocaleString('en-US'));
        // }else{
        //     $('#sub_total').val(sum.toLocaleString('en-US'));
        //     let grandtotal = $('#sub_total').val().split(",").join("");
        //     if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
        //         let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
        //         $('#sisa').val(sisa.toLocaleString('en-US'));
        //     }
        //     $('#input_ppn').val(0);
        // }

    });

    $(document).on('click', '#add-barang', function(e) {
        $('.invalid-feedback').hide();
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<div class="form-group col-md-4">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control detail_barang_new nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock</label>'+
                    '<input type="text" name="stock_toko_awal" id="stock_toko_awal_'+row_edit+'" class="form-control detail_barang_new stock_toko_awal" readonly>'+
                    '<div class="invalid-feedback feedback-stock_toko_awal_'+row_edit+'">'+
                        'Stock Toko Awal harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Qty</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control detail_barang_new jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-4">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control detail_barang_new serial_number" name="serial_number" id="serial_number_'+row_edit+'" required="" multiple></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Action</label>'+
                '<br>'+
                    '<button data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang"><i class="far fas fa-trash"></i></button>'+
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
                '$("#serial_number_'+row_edit+'").select2({'+
                    'placeholder:"Pilih Serial Number",'+
                '});'+
                '$(".select2-container").css("width","100%");'+
            '</script'+'>'+
            '</div>');
        row_edit ++;
        } 
    });

    $(document).on("select2:select",".edit_nama_barang", function(e){
        let url = "{{ url('serial-number/id_toko/') }}/"+$('#edit_nama_toko').val()+"/id_barang/"+$(this).val();
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

    $(document).on('change', '.harga', function() {
        let id = $(this).data('id');
        let jumlah = $('#jumlah_'+id).val().split(",").join("");
        let total;
        if($('#discount_'+id).val() != ''){
            let subtotal = $(this).val().split(",").join("") * jumlah;
            let discount = subtotal * $('#discount_'+id).val() /100;
            total = subtotal - discount;
        }else{
            total = $(this).val().split(",").join("") * jumlah;

        }
        $('#subtotal_'+id).val(total.toLocaleString('en-US'));

        let sum  = 0;
        $('.subtotal').each(function() {
            sum += parseFloat($(this).val().split(",").join(""));  
        });
        if($('#flexCheckDefault').is(':checked')){
            $('#input_total').val(sum.toLocaleString('en-US'));
            let grandtotal = parseFloat(sum) + parseFloat(sum * 11 / 100);
            $('#grand_total').val(grandtotal.toLocaleString('en-US'));
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val((sum * 11 / 100).toLocaleString('en-US'));
        }else{
            $('#sub_total').val(sum.toLocaleString('en-US'));
            let grandtotal = $('#sub_total').val().split(",").join("");
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val(0);
        }
    });

    function validationBarang() {
        $('.row-detail-barang').each(function() {
            let id = $(this).data('id');
            //existing barang
            if($('#stock_toko_awal_'+id).val() != 0 && $('#jumlah_'+id).val() != 0){
                if($('#serial_number_'+id).prop("disabled") != true){
                    if($('#serial_number_'+id).prop('multiple')){
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
                    }else{
                        console.log($('#serial_number_'+id).val());
                        if($('#serial_number_'+id).val() == null){
                            $('.feedback-serial_number_'+id).show();
                            $('.feedback-serial_number_'+id).html('Pilih Serial Number');
                            $('#serial_number_'+id).focus();
                        }else{
                            $('.feedback-serial_number_'+id).hide();
                        }
                    }
                }
            }else{
                if($(this).data('edit') == undefined){
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
            }
        });
    }

    $(document).on('change', '.discount', function() {
        let id = $(this).data('id');
        let harga = $('#harga_'+id).val().split(",").join("");
        let total;
        if($(this).val() > 100){
            $('#discount_'+id).val(100);
        }
        if($('#discount_'+id).val() != ''){
            let subtotal = $('#harga_'+id).val().split(",").join("") * $('#jumlah_'+id).val();
            let discount = subtotal * $(this).val() /100;
            total = subtotal - discount;
        }else{
            total = $('#harga_'+id).val().split(",").join("") * $('#jumlah_'+id).val();

        }
        $('#subtotal_'+id).val(total.toLocaleString('en-US'));
        let sum  = 0;
        $('.subtotal').each(function() {
            sum += parseFloat($(this).val().split(",").join(""));  
        });
        $('#sub_total').val(sum.toLocaleString('en-US'));
        if($('#flexCheckDefault').is(':checked')){
            $('#input_total').val(sum.toLocaleString('en-US'));
            let grandtotal = parseFloat(sum) + parseFloat(sum * 11 / 100);
            $('#grand_total').val(grandtotal.toLocaleString('en-US'));
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val((sum * 11 / 100).toLocaleString('en-US'));
        }else{
            $('#sub_total').val(sum.toLocaleString('en-US'));
            let grandtotal = $('#sub_total').val().split(",").join("");
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val(0);
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
        let row = $(this).data('row');
        let idp = "{{ $penjualan->id }}";
        if(id == undefined){
            $('#row_barang_'+row).remove();
        }else{
            let data = {}
            data.id_penjualan = idp;
            data.id = id;
            if($('#flexCheckDefault').is(':checked')){
                data.ppn = $('#input_ppn').val();
            }else{
                data.ppn = 0;
            }
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
                        url: "{{ url('penjualan/delete-barang') }}",
                        type: "post",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(result){
                            location.reload();
                        }
                    });
                }
            })
        }
    });

    $(document).on('change', '#status_pembayaran', function() {
        if($(this).val() === 'Tempo'){
            $('#waktu-bulan').prop('required', true);
            $('#waktu-bulan').show();
            $('#dp-payment').show();
            $('#dp-payment').prop('required', true);
            let total = 'sub_total';
            if($('#flexCheckDefault').is(':checked')){
                total = 'grand_total';
            }
            let sisa = $('#'+total).val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            if($('#waktu-bulan').is(":hidden")){
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-4');
            }else{
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-3');
            }
        }else if($(this).val() === 'DP'){
            $('#waktu-bulan').prop('required', false);
            $('#waktu-bulan').hide();
            $('#dp-payment').show();
            $('#dp-payment').prop('required', true);
            let total = 'sub_total';
            if($('#flexCheckDefault').is(':checked')){
                total = 'grand_total';
            }
            if($('#waktu-bulan').is(":hidden")){
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-4');
            }else{
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-3');
            }
        }else{
            $('#waktu-bulan').prop('required', false);
            $('#dp_payment').val('');
            $('#dp-payment').prop('required', false);
            $('#sisa').val(0);
            $('#waktu-bulan').hide();
            if($('#waktu-bulan').is(":hidden")){
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-4');
            }else{
                $('.row_pertama').removeClass('col-md-3');
                $('.row_pertama').removeClass('col-md-4');
                $('.row_pertama').addClass('col-md-3');
            }
            $('#dp-payment').hide();
        }
    });

    $(document).on('change', '#dp_payment', function() {
        let sisa;
        if($('#flexCheckDefault').is(':checked')){
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                sisa = $('#grand_total').val().split(",").join("") - $(this).val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
        }else{
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                sisa = $('#sub_total').val().split(",").join("") - $(this).val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
        }
    });

    $('#flexCheckDefault').click(function () {
        let sisa;
        if(this.checked == true){
            let total = $('#sub_total').val().split(",").join("");
            let disc = total * 11/100;
            let subtotal = parseFloat(total)+parseFloat(disc);
            $('#input_ppn').val(disc.toLocaleString('en-US'));
            $('#input_total').val(parseFloat(total).toLocaleString('en-US'));
            $('#grand_total').val(subtotal.toLocaleString('en-US'));
            $('#grand-total').hide();
            $('.div_ppn').show();
            if($('#status_pembayaran').val() == "Tempo" || $('#status_pembayaran').val() == "DP"){
                sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val(disc.toLocaleString('en-US'));
        }else{
            let sum  = 0;
            $('.subtotal').each(function() {
                sum += parseFloat($(this).val().split(",").join(""));  
            });
            $('#sub_total').val(sum.toLocaleString('en-US'));
            $('#input_total').val(0);
            $('#input_ppn').val(0);
            $('.div_ppn').hide();
            $('#grand-total').show();
            if($('#status_pembayaran').val() == "Tempo" || $('#status_pembayaran').val() == "Tempo"){
                sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
        }
    });

    $(document).on('click','.save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        $('.needs-validation-barang').addClass('was-validated');
        $('.needs-validation-payment').addClass('was-validated');
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
            data.button = $(this).data('button');
            let databarangedit = []
            let databarangnew = []
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.edit_detail_barang').each(function(){
                databarangedit.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-barang').find('.detail_barang_new').each(function(){
                if($("#"+$(this).attr('id')).attr("name") == 'harga' || $("#"+$(this).attr('id')).attr("name") == 'subtotal'){
                    databarangnew.push($("#"+$(this).attr('id')).val().split(",").join(""));
                }else{
                    databarangnew.push($("#"+$(this).attr('id')).val());
                }
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                if($("#"+$(this).attr('id')).attr("name") == 'grand_total' || $("#"+$(this).attr('id')).attr("name") == 'input_ppn' || $("#"+$(this).attr('id')).attr("name") == 'input_total' || $("#"+$(this).attr('id')).attr("name") == 'sub_total' || $("#"+$(this).attr('id')).attr("name") == 'dp_payment' || $("#"+$(this).attr('id')).attr("name") == 'sisa'){
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val().split(",").join("");
                }else{
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });
            if(databarangedit.length == 0 && databarangnew.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang yang akan dijual!',
                })
            }else{
                let id = $(this).data('id');
                data.databarangedit = databarangedit;
                data.databarangnew = databarangnew;
                $.ajax({
                    url: "{{ url('barang-keluar/update') }}/"+id,
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
                            window.location = '/barang-keluar';
                        });
                    },
                    error: function(errors){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errors.responseJSON.message,
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