@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Convert Invoice ke Penjualan Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Convert Invoice ke Penjualan Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Convert Penjualan</h2>
    <p class="section-lead">
        Berikut adalah data convert penjualan toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Convert Penjualan</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control datepicker" required="" id="date" name="date"
                                    placeholder="Tanggal" value="{{ date('d-m-Y', strtotime($quotation->date)) }}">
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number"
                                    value="PJ - {{  sprintf('%07d', $penjualan === null ? "1" : $penjualan->id+1) }}"
                                    readonly>
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
                                        <option value="{{ $signature->name }}" {{ $quotation->nama_sales == $signature->name ? 'selected' : '' }}>{{ $signature->name }}</option>
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
                                    name="nama_pembeli" value="{{ $quotation->nama_pembeli }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli" value="{{ $quotation->alamat_pembeli }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="{{ $quotation->telepon }}">
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
                    <h4>Payment Detail</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation-payment" novalidate="">
                        <div class="row">
                            <div class="form-group {{ $quotation->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama">
                                <label>Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                                    <option {{ $quotation->metode_pembayaran == "Tunai" ? "selected" : "" }} value="Tunai">Tunai</option>
                                    <option {{ $quotation->metode_pembayaran == "Transfer" ? "selected" : "" }} value="Transfer">Transfer</option>
                                    <option {{ $quotation->metode_pembayaran == "Giro" ? "selected" : "" }} value="Giro">Giro</option>
                                </select>
                                <div class="invalid-feedback feedback-metode_pembayaran">
                                    Metode Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $quotation->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama">
                                <label>Cara Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                                    <option {{ $quotation->payment_status == "DP" ? "selected" : "" }} value="Lunas">Lunas</option>
                                    <option {{ $quotation->payment_status == "Cash Before Delivery (CBD)" ? "selected" : "" }} value="Cash Before Delivery (CBD)">Cash Before Delivery (CBD)</option>
                                    <option {{ $quotation->payment_status == "Cash On Delivery (COD)" ? "selected" : "" }} value="Cash On Delivery (COD)">Cash On Delivery (COD)</option>
                                    <option {{ $quotation->payment_status == "Tempo" ? "selected" : "" }} value="Tempo">Tempo</option>
                                    <option {{ $quotation->payment_status == "DP" ? "selected" : "" }} value="DP">DP</option>
                                </select>
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Cara Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $quotation->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama" id="waktu-bulan" style="{{ $quotation->waktu == "" ? "display: none" : "" }}">
                                <label>Waktu / Bulan</label>
                                <input type="text" class="form-control" id="tempo_waktu" name="tempo_waktu"
                                    required>
                                <div class="invalid-feedback feedback-waktu">
                                    Lama Waktu Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $quotation->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama" id="grand-total" style="{{ $quotation->ppn != "" ? "display: none" : "" }}">
                                <label>Grand Total</label>
                                <input type="text" class="form-control" required="" name="sub_total" id="sub_total"
                                    value="{{ number_format($quotation->subtotal) }}" readonly>
                                <div class="invalid-feedback feedback-sub_total">
                                    Grand Total harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $quotation->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama div_ppn"style="{{ $quotation->ppn != "" ? "" : "display: none" }}">
                                <label>Total</label>
                                <input type="text" class="form-control" required="" name="input_total" id="input_total"
                                    value="{{ number_format($quotation->subtotal) }}" readonly>
                                <div class="invalid-feedback feedback-input_total">
                                    Total harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;" {{ $quotation->ppn != "0" ? "checked" : "" }}>
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    PPN
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan_pembayaran" id="keterangan_pembayaran"
                                    style="height: 150px;"></textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                            <div class="col-md-6 mt-4">
                                <div class="form-group row div_ppn" id="div-ppn" style="{{ $quotation->ppn != '0' ? '' : 'display: none;' }}">
                                    <label class="col-sm-3 col-form-label">PPN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="input_ppn" id="input_ppn"
                                            value="{{ number_format($quotation->ppn) }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Input PPN harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row div_ppn" id="div-ppn" style="{{ $quotation->ppn != '0' ? '' : 'display: none;' }}">
                                    <label class="col-sm-3 col-form-label">Grand Total</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" required="" name="grand_total"
                                            id="grand_total" value="{{ number_format($quotation->subtotal + ($quotation->subtotal * 11 / 100)) }}" readonly>
                                        <div class="invalid-feedback feedback-grand_total">
                                            Grand Total harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div id="dp-payment" style="{{ $quotation->payment_status == 'DP' ? '' : 'display: none;' }}">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">DP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control money-format" name="dp_payment"
                                                id="dp_payment" value="{{ number_format($quotation->dp_payment) }}">
                                            <div class="invalid-feedback feedback-dp_payment">
                                                Dp harus diisi.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Sisa</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" required="" name="sisa" id="sisa"
                                                value="{{ number_format($quotation->sisa) }}" readonly>
                                            <div class="invalid-feedback feedback-sisa">
                                                Sisa harus diisi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
                    {{--<button class="btn btn-warning save" data-id="draft" type="button" id="draft">Save as Draft</button>--}}
                    <button class="btn btn-primary save" data-id="save" type="button" id="save">Simpan</button>
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
    let data = @json($quotation);
    let row_edit = 1;
    if(data.toko_id != null){
        $('#nama_toko').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    }
    @if (Auth::user()->status == 2)
        $('#nama_toko').prop('disabled', true);
    @endif
    getDetailBarang();
    function getDetailBarang() {
        let id =  {{$quotation->id}}
        let detail_barang = '';
        $.each(data.detail_invoice, function (i,v) {
            let wajib_serial_number = 'required';
            if(v.barang.wajib_serial_number != 1){
                wajib_serial_number = 'disabled';
            }
            $('#detail-barang').html('');
            let discount;
            if(v.discount == null){
                discount = '';
            }else{
                discount = v.discount;
            }
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            let id_barang = v.barang.id;
            let nama_barang = v.barang.nama_product;
            if(keterangan == null){
                keterangan = '';
            }
            let url_toko_edit = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_serial_number = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+id_barang;
            let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            detail_barang += '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.id+'" class="edit_detail_barang">'+
                '<div class="form-group col-md-2">'+
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
                    '<input value="'+v.jumlah+'" type="number" min="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control edit_detail_barang jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control edit_detail_barang serial_number" name="serial_number" id="serial_number_'+row_edit+'" '+wajib_serial_number+' multiple></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+row_edit+'">'+
                        'Serial Number harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Harga</label>'+
                    '<input value="'+v.price.toLocaleString('en-US')+'" data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control edit_detail_barang money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+row_edit+'">'+
                        'Harga harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Discount %</label>'+
                    '<input value="'+discount+'" data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control edit_detail_barang discount">'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Sub Total</label>'+
                    '<input type="text" name="subtotal" id="subtotal_'+row_edit+'" class="form-control edit_detail_barang subtotal" value="'+v.subtotal.toLocaleString('en-US')+'" readonly>'+
                    '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                        'Keterangan harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Action</label>'+
                '<br>'+
                    '<button data-id="'+v.id+'" data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang"><i class="far fas fa-trash"></i></button>'+
                '</div>'+
                '<script type="text/javascript">'+
                    '$("#nama_barang_'+row_edit+'").empty().append("<option selected value='+id_barang+'>'+nama_barang+'  </option>");'+
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
            getTotalBarangByTokoFrom(databarangid, row_edit);

            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        $('#save').attr('data-id', id);
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
        if(user_status == 1){
            url_barang = "{{ url('barang/stock') }}";
        }else{
            url_barang = "{{ url('barang/get-total-barang-by-toko') }}";
        }
        
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
                $("#stock_toko_awal_"+id).val(response.total);
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
                }else{
                    $("#harga_"+id).val(0);
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
    });

    $(document).on("select2:select",".serial_number", function(e){
         $('.feedback-'+$(this).attr('id')).hide();
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
            $('#input_ppn').val(0);
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val(0);
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
                    '<label>Qty</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control detail_barang_new jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Qty harus diisi.'+
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
                    '<label>Discount %</label>'+
                    '<input data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control detail_barang_new discount">'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Jumlah</label>'+
                    '<input type="text" value="0" name="subtotal" id="subtotal_'+row_edit+'" class="form-control detail_barang_new subtotal" readonly>'+
                    '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                        'Jumlah harus dipilih.'+
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
            if($('#stock_toko_awal_'+id).val() != 0 && $('#jumlah_'+id).val() != 0){
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
        $('#row_barang_'+id).remove();
        let sum  = 0;
        $('.subtotal').each(function() {
            sum += parseFloat($(this).val().split(",").join(""));  
        });
        if($('#flexCheckDefault').is(':checked')){
            $('#input_total').val(sum.toLocaleString('en-US'));
            let grandtotal = $('#input_total').val().split(",").join("") + (sum * 11 / 100);
            $('#grand_total').val(grandtotal.toLocaleString('en-US'));
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#input_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
        }else{
            $('#sub_total').val(sum.toLocaleString('en-US'));
            let grandtotal = $('#sub_total').val().split(",").join("");
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
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
            if($('#status_pembayaran').val() == "Tempo" || $('#status_pembayaran').val() == "DP"){
                sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
        }
    });

    $(document).on('click','#save', function(e) {
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
            data.button = $(this).data('id');
            data.databarang = []
            let datapayment = {}
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.form-control').each(function(){
                if($("#"+$(this).attr('id')).attr("name") == 'harga' || $("#"+$(this).attr('id')).attr("name") == 'subtotal'){
                    data.databarang.push($("#"+$(this).attr('id')).val().split(",").join(""));
                }else{
                    data.databarang.push($("#"+$(this).attr('id')).val());
                }
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                if($("#"+$(this).attr('id')).attr("name") == 'grand_total' || $("#"+$(this).attr('id')).attr("name") == 'input_ppn' || $("#"+$(this).attr('id')).attr("name") == 'input_total' || $("#"+$(this).attr('id')).attr("name") == 'sub_total' || $("#"+$(this).attr('id')).attr("name") == 'dp_payment' || $("#"+$(this).attr('id')).attr("name") == 'sisa'){
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val().split(",").join("");
                }else{
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });
            if(data.databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang !',
                });
            }else{
                $.ajax({
                    url: "{{ url('penjualan') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        $('#create-modal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Data has been saved.',
                        }).then((result) => {
                            window.location = '/penjualan/';
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