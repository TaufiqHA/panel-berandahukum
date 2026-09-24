@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Terima PO Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Terima PO Toko</a></div>
    </div>
</div>
<div class="section-body">
    <h2 class="section-title">Terima PO</h2>
    <p class="section-lead">
        Berikut adalah data terima po toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Terima PO</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Tanggal Diterima</label>
                                <input type="text" class="form-control datepicker terima-barang" required="" id="date" name="date" placeholder="Tanggal" value="{{ date('d-m-Y') }}">
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input readonly type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number"
                                    value="{{ $penjualan->kode_po }}"
                                    readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Toko</label>
                                <select disabled class="form-control terima-barang" name="nama_toko" id="nama_toko"></select>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Toko harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Purchasing</label>
                                @php $all_signatures = \App\Models\Signature::all(); @endphp
                                <select disabled class="form-control select2" id="nama_purchase" name="nama_purchase">
                                    <option value="">Pilih Purchasing</option>
                                    @foreach($all_signatures as $signature)
                                        <option value="{{ $signature->name }}" {{ $penjualan->nama_purchase == $signature->name ? 'selected' : '' }}>{{ $signature->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback feedback-nama_sales">
                                    Nama Purchasing harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Nama Supplier</label>
                                <input readonly type="text" class="form-control terima-barang" required="" id="nama_supplier"
                                    name="nama_supplier" value="{{ $penjualan->nama_supplier }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Supplier harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Supplier</label>
                                <input readonly type="text" class="form-control" id="alamat_supplier" name="alamat_supplier" value="{{ $penjualan->alamat_supplier }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Alamat Supplier harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Supplier</label>
                                <input readonly type="text" class="form-control" id="telepon" name="telepon" value="{{ $penjualan->telepon }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Telepon Supplier harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <form class="needs-validation-barang" novalidate="">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Detail Barang</h4>
                    </div>
                </div>
                <div id="detail-barang">
                    
                </div>
            </form>

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Payment Detail</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation-payment" novalidate="">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input disabled {{ $penjualan->ppn != 0 ? "checked" : "" }} class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    PPN
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control terima-barang" name="keterangan" id="keterangan"
                                    style="height: 150px;">{{ $penjualan->keterangan }}</textarea>
                                <div class="invalid-feedback feedback-keterangan">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="mt-1">
                                    <div class="mt-1 div_ppn" style="{{ $penjualan->ppn != 0 ? "" : "display: none;" }}">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Total</label>
                                        <input type="text" class="form-control" required="" name="input_total"
                                            id="input_total" value="{{ $penjualan->ppn != 0 ? number_format($penjualan->subtotal) : "0" }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Grand Total harus diisi.
                                        </div>
                                    </div>
                                    <div style="{{ $penjualan->ppn != 0 ? "" : "display: none;" }}" class="mt-1 div_ppn">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">PPN</label>
                                        <input type="text" class="form-control" required="" name="input_ppn" id="input_ppn"
                                            value="{{ number_format($penjualan->ppn) }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            PPN harus diisi.
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Grand
                                            Total</label>
                                        <input type="text" class="form-control" required="" name="sub_total" id="sub_total"
                                            value="{{ $penjualan->ppn != 0 ? number_format($penjualan->subtotal + ($penjualan->subtotal * 11 /100)) : number_format($penjualan->subtotal) }}" readonly>
                                        <div class="invalid-feedback feedback-sub_total">
                                            Total harus diisi.
                                        </div>
                                    </div>
                                    <div style="{{ $penjualan->po_dp > 0 ? "" : "display: none;" }}" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Uang Muka</label>
                                        <input type="text" class="form-control money-format uangmuka" readonly required="" name="input_dp" id="input_dp"
                                            value="{{ number_format($penjualan->po_dp) }}" >
                                        <div class="invalid-feedback feedback-input_dp">
                                            Uang Muka harus diisi.
                                        </div>
                                    </div>

                                    <div style="{{ $penjualan->po_dp > 0 ? "" : "display: none;" }}" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Sisa Pembayaran</label>
                                        <input type="text" class="form-control money-format sisapem" required="" readonly name="input_sisa" id="input_sisa"
                                            value="{{ $penjualan->ppn != 0 ? number_format( ($penjualan->subtotal + ($penjualan->subtotal * 11 /100)) - $penjualan->po_dp ) : number_format($penjualan->subtotal - $penjualan->po_dp) }}" readonly>
                                        <div class="invalid-feedback feedback-input_dp">
                                            Sisa Pembayaran harus diisi.
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
                    {{-- <button class="btn btn-warning" type="button" id="draft">Save as Draft</button> --}}
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
    let data = @json($penjualan);
    let row_edit = 1;
    if(data.toko_id !== null){
        $('#nama_toko').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    }
    @if (Auth::user()->status == 2)
        $('#nama_toko').prop('disabled', true);
    @endif
    getDetailBarang();
    function getDetailBarang() {
        let id =  data.id;
        let detail_barang = '';
        $.each(data.detail_po, function (i,v) {
            $('#detail-barang').html('');
            let discount;
            if(v.discount == null){
                discount = '';
            }else{
                discount = v.discount;
            }
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            if(keterangan == null){
                keterangan = '';
            }
            let url_toko = "{{ url('barang/select') }}";
            let subtotal;
            if(v.discount != null || v.discount != 0){
                let discount = (v.price * v.jumlah) * v.discount /100;
                subtotal = (v.price * v.jumlah) - discount;
            }else{
                subtotal = v.price * v.jumlah;
            }
            let wajib_serial_number = '';
            let col_made_in = 4;
            if(v.barang.wajib_serial_number == 1){
                col_made_in = 2;
                wajib_serial_number = '<div class="form-group col-md-2">'+
                    '<label>Serial Number</label>'+
                    '<select name="type_serial_number" id="type_serial_number_'+row_edit+'" data-id="'+row_edit+'" class="form-control terima-barang-'+row_edit+' serial-number-change" required>'+
                        '<option value="">Pilih Type</option>'+
                        '<option value="1">All</option>'+
                        '<option value="2">One by one</option>'+
                    '</select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Type Serial Number Harus Dipilih.'+
                    '</div>'+
                '</div>';
            }
            detail_barang += 
            '<div class="card">'+
                '<div class="card-body">'+
                    '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                        '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.barang.id+'" class="edit_detail_barang terima-barang-'+row_edit+'">'+
                        '<div class="form-group col-md-4">'+
                            '<label>Pilih Barang</label>'+
                            '<select class="form-control edit_detail_barang nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required="" disabled></select>'+
                            '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                                'Barang harus dipilih.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-1">'+
                            '<label>Qty</label>'+
                            '<input value="'+v.jumlah+'" type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control edit_detail_barang jumlah" required data-id="'+row_edit+'" readonly>'+
                            '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                                'Jumlah harus diisi.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                            '<label>Harga</label>'+
                            '<input value="'+v.price.toLocaleString('en-US')+'" data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control edit_detail_barang money-format harga" required readonly>'+
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
                        '<div class="form-group col-md-4">'+
                            '<label>Sub Total</label>'+
                            '<input type="text" name="subtotal" id="subtotal_'+row_edit+'" class="form-control edit_detail_barang subtotal" value="'+subtotal.toLocaleString('en-US')+'" readonly>'+
                            '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                                'Keterangan harus dipilih.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                            '<label>Jumlah Diterima</label>'+
                            '<input type="number" min="0" name="jumlah_diterima" id="jumlah_diterima_'+row_edit+'" class="form-control money-format terima-barang-'+row_edit+' diterima" required data-id="'+row_edit+'">'+
                            '<div class="invalid-feedback feedback-jumlah_diterima_'+row_edit+'">'+
                                'Jumlah Diterima Harus Diisi.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                            '<label>Harga Beli</label>'+
                            '<input type="text" min="0" name="harga_beli" id="harga_beli_'+row_edit+'" class="form-control terima-barang-'+row_edit+' money-format" required data-id="'+row_edit+'">'+
                            '<div class="invalid-feedback feedback-harga_beli_'+row_edit+'">'+
                                'Harga Beli harus diisi.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                            '<label>Harga Jual</label>'+
                            '<input type="text" min="0" name="harga_jual" id="harga_jual_'+row_edit+'" class="form-control terima-barang-'+row_edit+' money-format" required data-id="'+row_edit+'">'+
                            '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                                'Harga Jual Harus diisi.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-2">'+
                            '<label>Price List</label>'+
                            '<input type="text" min="0" name="price_list" id="price_list_'+row_edit+'" class="form-control money-format terima-barang-'+row_edit+'" required data-id="'+row_edit+'">'+
                            '<div class="invalid-feedback feedback-price_list_'+row_edit+'">'+
                                'Price List Harus diisi.'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group col-md-'+col_made_in+'">'+
                            '<label>Made In</label>'+
                            '<input type="text" name="made_in" id="made_in_'+row_edit+'" class="form-control terima-barang-'+row_edit+'" data-id="'+row_edit+'">'+
                            '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                                'Made In Harus diisi.'+
                            '</div>'+
                        '</div>'+
                        wajib_serial_number+
                        '<script type="text/javascript">'+
                            '$("#nama_barang_'+row_edit+'").empty().append("<option selected value='+v.barang.id+'>'+v.barang.nama_product+'  </option>");'+
                            '$(".nama_barang").select2({'+
                                'placeholder:"Pilih Barang",'+
                                'tags: true,'+
                                'ajax: {'+
                                    'url: "'+url_toko+'",'+
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
                    '</div>'+
                '</div>'+
            '</div>';
            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        $('#save').attr('data-id', id);
    }

    $(document).on('change', '.diterima', function(e){
        e.stopPropagation();
        let id = $(this).data('id');
        $('.serial-number-'+id).remove();
        $('#type_serial_number_'+id).prop('selectedIndex',0);
        if($(this).val() != 0){
            $('.feedback-jumlah_diterima_'+id).css('display','none');
            $('#jumlah_diterima_'+id).removeClass('requirement-validasi');
        }
    });

    $(document).on('change', '.serial-number-change', function(e){
        e.stopPropagation();
        let id = $(this).data('id');
        let val = $(this).val();
        let jumlah = $('#jumlah_diterima_'+id).val();
        $('.serial-number-'+id).remove();
        if(jumlah == 0){
            $('#jumlah_diterima_'+id).addClass('requirement-validasi');
            $('#jumlah_diterima_'+id).focus();
            $('.feedback-jumlah_diterima_'+id).css('display','block');
            return false;
        }
        if(val == 1){
            $('#row_barang_'+id).append(
                '<div class="form-group col-md-12 serial-number-'+id+'" id="serial_number_id_'+id+'">'+
                    '<label>Serial number <code>*pisahkan dengan koma tanpa spasi</code></label>'+
                    '<textarea name="data_serial_number" id="data_serial_number_'+id+'" class="form-control data_sn terima-barang-'+id+' serial-number-'+id+'" style="height: 100px;" placeholder="masukan serial number pisahkan dengan tanda koma ","" data-type="textarea" required></textarea>'+
                    '<div class="invalid-feedback feedback-data_serial_number_'+id+'">'+
                        'Serial Number harus diisi.'+
                    '</div>'+
                '</div>'
            );
        }else{
            let append = '';
            let number = 1;
            for(var i = 0; i < jumlah; i++){
                append +=
                '<div class="form-group col-md-2 serial-number-'+id+'" id="serial_number_id_'+id+'">'+
                    '<label>Serial number</label>'+
                    '<input required type="text" name="data_serial_number" id="data_serial_number_'+id+'_'+number+'" class="form-control terima-barang-'+id+' serial-number-'+id+'" required data-id="'+number+'">'+
                    '<div class="invalid-feedback feedback-data_serial_number_'+id+' feedback-data_serial_number_'+id+'_'+number+'">'+
                        'Serial Number harus diisi.'+
                    '</div>'+
                '</div>';
                number ++;
            }
            $('#row_barang_'+id).append(append);
        }
    });

    $('#flexCheckDefault').click(function () {
        if(this.checked == true){
            let total = $('#sub_total').val().split(",").join("");
            let disc = total * 11/100;
            let subtotal = parseFloat(total)+parseFloat(disc);
            $('#input_ppn').val(disc.toLocaleString('en-US'));
            $('#input_total').val(parseFloat(total).toLocaleString('en-US'));
            $('#sub_total').val(subtotal.toLocaleString('en-US'));
            $('.div_ppn').show();
        }else{
            let sum  = 0;
            $('.subtotal').each(function() {
                sum += parseFloat($(this).val().split(",").join(""));  
            });
            $('#sub_total').val(sum.toLocaleString('en-US'));
            $('#input_total').val(0);
            $('#input_ppn').val(0);
            $('.div_ppn').hide();
        }
    });

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
                    if(response.length > 0){
                        $("#harga_"+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
                        $('#subtotal_'+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
                    }else{
                        $("#harga_"+id).val(0);
                        $('#subtotal_'+id).val(0);
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
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            let ppn = sum * 11 / 100;
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
            $('#input_ppn').val(ppn.toLocaleString('en-US'));
        }else{
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#input_ppn').val(0);
        }
    });

    $(document).on('click', '#add-barang', function(e) {
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control detail_barang_new nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Qty</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control detail_barang_new jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Qty harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Harga</label>'+
                    '<input data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control detail_barang_new money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+row_edit+'">'+
                        'Harga harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control detail_barang_new discount">'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Sub Total</label>'+
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
        if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
            let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
        }
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
            let ppn = sum * 11 / 100;
            $('#input_ppn').val(ppn.toLocaleString('en-US'));
        }else{
            $('#input_ppn').val(0);
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
        }
    });

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
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
            let ppn = sum * 11 / 100;
            $('#input_ppn').val(ppn.toLocaleString('en-US'));
        }else{
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
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
                        url: "{{ url('po/delete-barang') }}",
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
    });

    function find_duplicate_in_array(arra1) {
        var object = {};
        var result = [];

        arra1.forEach(function (item) {
          if(!object[item])
              object[item] = 0;
            object[item] += 1;
        })

        for (var prop in object) {
           if(object[prop] >= 2) {
               result.push(prop);
           }
        }

        return result;

    }

    function checkSnDuplicate(data, id) {
        let datas = {}
        datas.sn = data;
        datas.barang_id = id;
        let dataArray = [];
        $.ajax({
            url: "{{ url('stock-in/duplicate') }}",
            type: "post",
            data: JSON.stringify(datas),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,
            success: function(response){
                dataArray = response;
            }
        });
        return dataArray;
    }

    function checkDuplicateData() {
        $.each(data.detail_po, function (i,v) {
            let index = i+1;
            let detailbarang = [];
            if($('#type_serial_number_'+index)){
                let array = [];
                if($('#type_serial_number_'+index).val() == 1){
                    array = $('#data_serial_number_'+index).val().split(',')
                }else{
                    array = $('.serial-number-'+index).map(function() {
                        return this.value;
                    }).get();
                }
                if($('#type_serial_number_'+index).val() == 1){
                    if($('#jumlah_diterima_'+index).val() != $('#data_serial_number_'+index).val().split(',').length){
                        $('.feedback-jumlah_diterima_'+index).show();
                        $('.feedback-jumlah_diterima_'+index).html('Sesuaikan Jumlah Dengan Serial Number');
                        $('#jumlah_diterima_'+index).focus();
                        return Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Jumlah Barang Tidak Sesuai Dengan Jumlah SN !',
                        });
                    }else{
                        $('.feedback-jumlah_diterima_'+index).hide();
                    }
                }
                let duplicate = find_duplicate_in_array(array);
                if(duplicate.length > 0){
                    $('.feedback-data_serial_number_'+index).show();
                    $('.feedback-data_serial_number_'+index).html('Serial Number Duplicate');
                    $('.serial_number-'+index).focus();
                    return Swal.fire({
                        icon: 'error',
                        title: 'Periksa serial number!',
                        text: 'Serial Number duplicate '+duplicate.toString(),
                    });
                }else{
                    $('.feedback-data_serial_number_'+index).hide();
                }
                let checkArrayDuplicate = checkSnDuplicate(array, $('#id_'+index).val());
                console.log(checkArrayDuplicate);
                let duplicateDb = find_duplicate_in_array(array.concat(checkArrayDuplicate));
                console.log(duplicateDb);
                if(duplicateDb.length > 0){
                    $('.feedback-data_serial_number_'+index).show();
                    $('.feedback-data_serial_number_'+index).html('Serial Number Duplicate');
                    $('.serial_number-'+index).focus();
                    return Swal.fire({
                        icon: 'error',
                        title: 'Periksa serial number!',
                        text: 'Serial Number duplicate '+duplicateDb.toString(),
                    });
                }else{
                    $('.feedback-data_serial_number_'+index).hide();
                }
            }
        });
    }

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        $('.needs-validation-barang').addClass('was-validated');
        checkDuplicateData();
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let datas = {}
            let databarangedit = []
            let databarang = []
            let databarangnew = []
            $('.needs-validation').find('.terima-barang').each(function(){
                datas[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $.each(data.detail_po, function (i,v) {
                let index = i+1;
                let detailbarang = [];
                $('.needs-validation-barang').find('.terima-barang-'+index).each(function(j,val){
                    if($("#"+$(this).attr('id')).attr("name") == 'jumlah_diterima' || $("#"+$(this).attr('id')).attr("name") == 'harga_beli' || $("#"+$(this).attr('id')).attr("name") == 'harga_jual' || $("#"+$(this).attr('id')).attr("name") == 'price_list'){
                        detailbarang[j] = $("#"+$(this).attr('id')).val().split(",").join("");
                    }else if($("#"+$(this).attr('id')).attr("name") == 'data_serial_number'){
                        detailbarang[j] = $("#"+$(this).attr('id')).val();
                    }else{
                        detailbarang[j] = $("#"+$(this).attr('id')).val();
                    }
                });
                databarang.push(detailbarang);
            });
            $('.needs-validation-payment').find('.terima-barang').each(function(){
                datas[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            if(databarang.length == 0 && databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang yang akan diterima!',
                });
            }else{
                let id = $(this).data('id');
                datas.databarang = databarang;
                insertTerimaBarang(datas, id);
            }
        }
    });

    function insertTerimaBarang(datas, id) {
        if($('.invalid-feedback:visible').length == 0){
            $.ajax({
                url: "{{ url('stock-in/insert-po') }}/"+id,
                type: "post",
                data: JSON.stringify(datas),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been updated.',
                    }).then((result) => {
                        window.location = '/po';
                    });
                }
            });
        }
    }
});
</script>
@endsection
@endsection