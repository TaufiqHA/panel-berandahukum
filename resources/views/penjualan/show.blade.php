@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Detail Penjualan Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Detail Penjualan Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Detail Penjualan</h2>
    <p class="section-lead">
        Berikut adalah data detail penjualan toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Detail Penjualan</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control" required="" id="date" name="date"
                                    placeholder="Tanggal" value="{{ date('d F Y', strtotime($penjualan->date)) }}"
                                    readonly>
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number" value="{{ $penjualan->kode_penjualan }}" readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Toko</label>
                                <select class="form-control" name="nama_toko" id="nama_toko" required=""
                                    disabled></select>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Toko harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Sales</label>
                                @php $all_signatures = \App\Models\Signature::all(); @endphp
                                <select disabled class="form-control select2" required="" id="nama_sales" name="nama_sales">
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
                                <input type="text" class="form-control" required="" id="nama_pembeli"
                                    name="nama_pembeli" value="{{ $penjualan->nama_pembeli }}" readonly>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli"
                                    value="{{ $penjualan->alamat_pembeli }}" readonly>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon" name="telepon"
                                    value="{{ $penjualan->telepon }}" readonly>
                                <div class="invalid-feedback feedback-nama_toko">
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
                    <h4>Payment Detail</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation-payment" novalidate="">
                        <div class="row">
                            <div class="form-group {{ $penjualan->waktu != "" ? "col-md-3" : "col-md-4" }} row_pertama">
                                <label>Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" disabled>
                                    <option value="Tunai"
                                        {{ $penjualan->metode_pembayaran === "Tunai" ? "selected" : "" }}>Tunai</option>
                                    <option value="Transfer"
                                        {{ $penjualan->metode_pembayaran === "Transfer" ? "selected" : "" }}>Transfer
                                    </option>
                                    <option value="Giro"
                                        {{ $penjualan->metode_pembayaran === "Giro" ? "selected" : "" }}>Giro</option>
                                </select>
                                <div class="invalid-feedback feedback-metode_pembayaran">
                                    Metode Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $penjualan->waktu != "" ? "col-md-3" : "col-md-4" }} row_pertama">
                                <label>Status Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-control" disabled>
                                    <option value="Cash Before Delivery (CBD)" {{ $penjualan->payment_status === "Cash Before Delivery (CBD)" ? "selected" : "" }}>Cash Before Delivery (CBD)</option>
                                    <option value="Cash On Delivery (COD)" {{ $penjualan->payment_status === "Cash On Delivery (COD)" ? "selected" : "" }}>Cash On Delivery (COD)</option>
                                    <option value="Tempo"
                                        {{ $penjualan->payment_status === "Tempo" ? "selected" : "" }}>Tempo</option>
                                    <option value="DP" {{ $penjualan->payment_status === "DP" ? "selected" : "" }}>DP
                                    </option>
                                </select>
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Status Pembayaran harus diisi.
                                </div>
                            </div>
                            @if ($penjualan->waktu != '')
                            <div class="form-group {{ $penjualan->waktu != "" ? "col-md-3" : "col-md-4" }} row_pertama"
                                id="waktu-bulan">
                                <label>Waktu / Bulan</label>
                                <input type="text" class="form-control" required="" id="tempo_waktu" name="tempo_waktu"
                                    required value="{{ $penjualan->waktu }}" disabled>
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Lama Waktu Pembayaran harus diisi.
                                </div>
                            </div>
                            @endif
                            @if ($penjualan->ppn == 0)
                            <div class="form-group {{ $penjualan->waktu != "" ? "col-md-3" : "col-md-4" }} row_pertama"
                                id="grand-total">
                                <label>Grand Total</label>
                                <input type="text" class="form-control" required="" name="sub_total" id="sub_total"
                                    value="{{ number_format($penjualan->subtotal) }}" readonly>
                                <div class="invalid-feedback feedback-sub_total">
                                    Grand Total harus diisi.
                                </div>
                            </div>
                            @else
                            <div
                                class="form-group {{ $penjualan->waktu != "" ? "col-md-3" : "col-md-4" }} row_pertama div_ppn">
                                <label>Total</label>
                                <input type="text" class="form-control" required="" name="input_total" id="input_total"
                                    value="{{ number_format($penjualan->subtotal) }}" readonly>
                                <div class="invalid-feedback feedback-input_total">
                                    Total harus diisi.
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan_pembayaran" id="keterangan_pembayaran"
                                    style="height: 150px;" readonly>{{$penjualan->keterangan }}</textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                            <div class="col-md-6 mt-4">
                                @if ($penjualan->ppn != '')
                                <div class="form-group row div_ppn" id="div-ppn">
                                    <label class="col-sm-3 col-form-label">PPN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="input_ppn" id="input_ppn"
                                            value="{{ number_format($penjualan->ppn) }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Input PPN harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row div_ppn" id="div-ppn">
                                    <label class="col-sm-3 col-form-label">Grand Total</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" required="" name="grand_total"
                                            id="grand_total"
                                            value="{{ number_format($penjualan->subtotal + ($penjualan->subtotal * 11 / 100)) }}"
                                            readonly>
                                        <div class="invalid-feedback feedback-grand_total">
                                            Total harus diisi.
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if ($penjualan->payment_status == 'Tempo' || $penjualan->payment_status == 'DP')
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">DP</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control money-format" name="dp_payment"
                                            id="dp_payment" value="{{ number_format($penjualan->dp_payment) }}" readonly>
                                        <div class="invalid-feedback feedback-dp_payment">
                                            Dp harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sisa</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" required="" name="sisa" id="sisa"
                                            value="{{ number_format($penjualan->sisa) }}" readonly>
                                        <div class="invalid-feedback feedback-sisa">
                                            Sisa harus diisi.
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </form>
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
    getDetailBarang();
    function getDetailBarang() {
        let id =  {{$penjualan->id}}
        let detail_barang = '';
        $.each(data.detail_penjualan, function (i,v) {
            $('#detail-barang').html('');
            let discount;
            if(v.discount == null){
                discount = '';
            }else{
                discount = v.discount;
            }
            let id_serial_number = data.barang[i].id;
            let name_serial_number = data.barang[i].serial_number;
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            let id_barang = data.detail_barang[i].id;
            let nama_barang = data.detail_barang[i].nama_product;
            if(keterangan == null){
                keterangan = '';
            }
            let url_toko_edit = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_serial_number = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+id_barang;
            let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let data_subtotal = v.price;
            if(v.discount != ''){
                data_subtotal = v.price - (v.price * v.discount / 100);
            }
            detail_barang += '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.id+'" class="edit_detail_barang">'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Barang</label>'+
                    '<select disabled class="form-control edit_detail_barang nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Qty</label>'+
                    '<input value="1" type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control edit_detail_barang jumlah" required data-id="'+row_edit+'" readonly>'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select disabled class="form-control edit_detail_barang serial_number" name="serial_number" id="serial_number_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Harga</label>'+
                    '<input value="'+v.price.toLocaleString('en-US')+'" data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control edit_detail_barang money-format harga" required disabled>'+
                    '<div class="invalid-feedback feedback-harga_'+row_edit+'">'+
                        'Harga harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input value="'+discount+'" data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control edit_detail_barang discount" readonly>'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Jumlah</label>'+
                    '<input type="text" name="subtotal" id="subtotal_'+row_edit+'" class="form-control edit_detail_barang subtotal" value="'+data_subtotal.toLocaleString('en-US')+'" readonly>'+
                    '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                        'Keterangan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<script type="text/javascript">'+
                    '$("#nama_barang_'+row_edit+'").empty().append("<option selected value='+id_barang+'>'+nama_barang+'</option>");'+
                    '$("#serial_number_'+row_edit+'").empty().append("<option selected value='+id_serial_number+'>'+name_serial_number+'  </option>");'+
                '$(".nama_barang").select2({'+
                    'placeholder: {'+
                        'id: -1,'+
                        'text: "Pilih Barang"'+
                    '},'+
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
            getTotalBarangByTokoFrom(databarangid, row_edit);

            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        $('#save').attr('data-id', id);
    }

    function getTotalBarangByTokoFrom(data, row) {
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+row).val(response.total);
            }
        });
    }

    function getTotalBarangByTokoTo(data, row) {
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_tujuan_"+row).val(response.total);
            }
        });
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

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
    });

    $(document).on("select2:select","#nama_toko", function(e){
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
        let id = $(this).data('id');
        let data = {}
        data.id_toko = $('#nama_toko').val();
        data.id_barang = $(this).val();
        
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+id).val(response.total);
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
                if(response[0].price_list == ''){
                    $("#harga_"+id).val(0);
                }else{
                    $("#harga_"+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
                }
            }
        });
    });

    $(document).on('change', '.jumlah', function() {
        let id = $(this).data('id');
        let harga = $('#harga_'+id).val().split(",").join("");
        let total;
        if($('#discount_'+id).val() != ''){
            let subtotal = $('#harga_'+id).val().split(",").join("") * $(this).val();
            let discount = subtotal * $('#discount_'+id).val() /100;
            total = subtotal - discount;
        }else{
            total = $('#harga_'+id).val().split(",").join("") * $(this).val();

        }
        $('#subtotal_'+id).val(total.toLocaleString('en-US'));
        let sum  = 0;
        $('.subtotal').each(function() {
            sum += parseFloat($(this).val().split(",").join(""));  
        });
        $('#sub_total').val(sum.toLocaleString('en-US'));
        if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
            let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
        }
    });

    $(document).on('click', '#add-barang', function(e) {
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<div class="form-group col-md-2">'+
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
                    '<label>Jumlah</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control detail_barang_new jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control detail_barang_new serial_number" name="serial_number" id="serial_number_'+row_edit+'" required="" multiple></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Harga</label>'+
                    '<input data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control detail_barang_new money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control detail_barang_new discount">'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Jumlah</label>'+
                    '<input type="text" value="0" name="subtotal" id="subtotal_'+row_edit+'" class="form-control detail_barang_new subtotal" readonly>'+
                    '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                        'Keterangan harus dipilih.'+
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
        $('#sub_total').val(sum.toLocaleString('en-US'));
        if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
            let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
        }
    });

    function validationBarang() {
        $('.row-detail-barang').each(function() {
            let id = $(this).data('id');
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
        })
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
        if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
            let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
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
            $('#div-tempo').show();
            $('#dp-payment').show();
        }else if($(this).val() === 'DP'){
            $('#div-tempo').hide();
            $('#dp-payment').show();
        }else{
            $('#dp_payment').val('');
            $('#sisa').val('');
            $('#div-tempo').hide();
            $('#dp-payment').hide();
        }
    });

    $(document).on('change', '#dp_payment', function() {
        let sisa = $('#sub_total').val().split(",").join("") - $(this).val().split(",").join("");
        $('#sisa').val(sisa.toLocaleString('en-US'));
    })

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        $('.needs-validation-barang').addClass('was-validated');
        validationBarang();
        if($('.invalid-feedback:visible').length == 0){
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
            $('.needs-validation-payment').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
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
                    url: "{{ url('penjualan/update') }}/"+id,
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
                            window.location = '/penjualan';
                        });
                    }
                });
            }
        }
    });
});
</script>
@endsection
@endsection