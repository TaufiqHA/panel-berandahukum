@extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>Invoice</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Invoice</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Invoice</h2>
    <p class="section-lead">
        Berikut adalah data invoice melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Invoice</h4>
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
                                    placeholder="Ref Number"
                                    value="INV - {{  sprintf('%07d', $invoice === null ? "1" : $invoice->id+1) }}">
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
                                        <option value="{{ $signature->name }}">{{ $signature->name }}</option>
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
                                    name="nama_pembeli">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon_pembeli" name="telepon_pembeli">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Telepon Pembeli harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
								<input class="form-check-input" type="checkbox" value="1" name="show_option" id="show_option"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_option" style="margin-left: 1.25rem;">
                                    Option Text
                                </label>
                                <br />
								<input type="text" class="form-control" id="option_text"
                                    name="option_text" placeholder="Masukan option text disini">
                                <div class="invalid-feedback feedback-option_text">
                                    Option Text harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
								<input class="form-check-input" type="checkbox" value="1" name="show_project" id="show_project"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_project" style="margin-left: 1.25rem;">
                                    Nama Project
                                </label>
                                <br />
                                <input type="text" class="form-control" id="nama_project" name="nama_project" placeholder="Masukan nama project disini">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Nama Project harus diisi.
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
                            <div class="form-group col-md-4 row_pertama">
                                <label>Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer</option>
                                    <option value="Giro">Giro</option>
                                </select>
                                <div class="invalid-feedback feedback-metode_pembayaran">
                                    Metode Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4 row_pertama">
                                <label>Status Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                                    <option value="Cash Before Delivery (CBD)">Cash Before Delivery (CBD)</option>
                                    <option value="Cash On Delivery (COD)">Cash On Delivery (COD)</option>
                                    <option value="Tempo">Tempo</option>
                                    <option value="DP">DP</option>
                                </select>
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Status Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4 row_pertama" id="waktu-bulan" style="display: none">
                                <label>Waktu / Bulan</label>
                                <input type="text" class="form-control" id="tempo_waktu" name="tempo_waktu"
                                    required>
                                <div class="invalid-feedback feedback-waktu">
                                    Lama Waktu Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4 row_pertama" id="grand-total">
                                <label>Grand Total</label>
                                <input type="text" class="form-control" required="" name="sub_total" id="sub_total"
                                    value="0" readonly>
                                <div class="invalid-feedback feedback-sub_total">
                                    Grand Total harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4 row_pertama div_ppn" style="display: none">
                                <label>Total</label>
                                <input type="text" class="form-control" required="" name="input_total" id="input_total"
                                    value="0" readonly>
                                <div class="invalid-feedback feedback-input_total">
                                    Total harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;">
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
                                <br />
                                <input class="form-check-input" type="checkbox" value="1" name="show_infopembayaran" id="show_infopembayaran"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_infopembayaran" style="margin-left: 1.25rem;">
                                    Cara Pembayaran
                                </label>
                            </div>
                            <div class="col-md-6 mt-4">
                                <div class="form-group row div_ppn" id="div-ppn" style="display: none;">
                                    <label class="col-sm-3 col-form-label">PPN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="input_ppn" id="input_ppn"
                                            value="0" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Input PPN harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row div_ppn" id="div-ppn" style="display: none;">
                                    <label class="col-sm-3 col-form-label">Grand Total</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" required="" name="grand_total"
                                            id="grand_total" value="0" readonly>
                                        <div class="invalid-feedback feedback-grand_total">
                                            Total harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div id="dp-payment" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">DP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control money-format" name="dp_payment"
                                                id="dp_payment" value="0">
                                            <div class="invalid-feedback feedback-dp_payment">
                                                Dp harus diisi.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Sisa</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" required="" name="sisa" id="sisa"
                                                value="0" readonly>
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
					<button class="btn btn-danger" data-id="batal" type="button" id="batal">Batal</button>
                    <button class="btn btn-info" data-id="draft" type="button" id="draft">Draft</button>
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
    @if (Auth::user()->status == 2)
        $('#nama_toko').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
        $('#nama_toko').prop('disabled', true);
    @endif

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
    });

    let index = 0;
    $(document).on('click', '#add-barang', function(e) {
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            // let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+index+'" data-id="'+index+'">'+
                '<div class="form-group col-md-3">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control nama_barang" data-id="'+index+'" name="nama_barang" id="nama_barang_'+index+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+index+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Stock</label>'+
                    '<input type="text" name="stock_toko_awal" id="stock_toko_awal_'+index+'" class="form-control stock_toko_awal" readonly>'+
                    '<div class="invalid-feedback feedback-stock_toko_awal_'+index+'">'+
                        'Stock Toko Awal harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Qty</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+index+'" class="form-control jumlah" required data-id="'+index+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+index+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
                    '<label>Harga</label>'+
                    '<input data-id="'+index+'" type="text" name="harga" id="harga_'+index+'" class="form-control money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+index+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input data-id="'+index+'" type="number" max="100" name="discount" id="discount_'+index+'" class="form-control discount">'+
                    '<div class="invalid-feedback feedback-discount_'+index+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Jumlah</label>'+
                    '<input type="text" value="0" name="subtotal" id="subtotal_'+index+'" class="form-control subtotal" readonly>'+
                    '<div class="invalid-feedback feedback-keterangan_'+index+'">'+
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
        index ++;
        } 
    });

    $(document).on("select2:select","#nama_toko", function(e){
        $(".nama_barang").val(null).trigger("change");
        $(".stock_toko_awal").val('');
        $(".jumlah").val('');
        $(".harga").val('');
        $(".discount").val('');
        $(".subtotal").val('');
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
        $.ajax({
            url: "{{ url('barang/get-total-barang-by-toko-new') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $("#stock_toko_awal_"+id).val(response);
                Swal.close();
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
            if($('#jumlah_'+id).val() == '0'){
                $('.feedback-jumlah_'+id).show();
                $('.feedback-jumlah_'+id).html('Masukkan Jumlah');
                $('#jumlah'+id).focus();
            }
        });
    }

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
            data.button = $(this).data('id');
            data.databarang = []
            let datapayment = {}
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.form-control').each(function(){
                data.databarang.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            if($('#show_infopembayaran').is(':checked')){
				data['show_infopembayaran'] = $("#show_infopembayaran").val();
			}else{
				data['show_infopembayaran'] =  0;
			}
			if($('#show_option').is(':checked')){
				data['show_option'] = $("#show_option").val();
			}else{
				data['show_option'] =  0;
			}
			if($('#show_project').is(':checked')){
				data['show_project'] = $("#show_project").val();
			}else{
				data['show_project'] =  0;
			}
			data['option_text'] = $('#option_text').val();
			data['nama_project'] = $('#nama_project').val();
			data['status'] = 1;
            if(data.databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang !',
                });
            }else{
                $.ajax({
                    url: "{{ url('invoice') }}",
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
                            window.location = '/invoice/';
                        });
                    }
                });
            }
        }
    });
    
    $(document).on('click','#draft', function(e) {
        e.stopPropagation();
       if($('#nama_toko').val() > 0 ){
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
                data.databarang.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            if($('#show_infopembayaran').is(':checked')){
				data['show_infopembayaran'] = $("#show_infopembayaran").val();
			}else{
				data['show_infopembayaran'] =  0;
			}
			if($('#show_option').is(':checked')){
				data['show_option'] = $("#show_option").val();
			}else{
				data['show_option'] =  0;
			}
			if($('#show_project').is(':checked')){
				data['show_project'] = $("#show_project").val();
			}else{
				data['show_project'] =  0;
			}
			data['option_text'] = $('#option_text').val();
			data['nama_project'] = $('#nama_project').val();
			data['status'] = 2;
            
                $.ajax({
                    url: "{{ url('invoice') }}",
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
                            window.location = "{{ url('invoice') }}";
                        });
                    }
                });
           
        }else{
			return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Nama Toko Harus di isi !',
                });
		}
    });
    
     $(document).on('click','#batal', function(e) {
		window.location = "{{ url('invoice') }}";
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

    $(document).on('change', '.discount', function() {
        let id = $(this).data('id');
        let harga = $('#harga_'+id).val().split(",").join("");
        let total;
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

    $(document).on('change', '.jumlah', function() {
        let id = $(this).data('id');
        $('.feedback-jumlah_'+id).hide();
        let harga = $('#harga_'+id).val().split(",").join("");
        console.log(harga);
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

    $(document).on('change', '#discount_type', function() {
        if($('#discount_value').val() == ''){
            $('#discount_value').val(0);
        }
        if($(this).val() == "None"){
            $('#discount_value').prop('readonly', true);
        }else if($(this).val() == "Fixed"){
            let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
        }else if($(this).val() == "Percentage"){
            $('#discount_value').prop('readonly', false);
            let total_discount = $('#sub_total').val().split(",").join("") * $('#discount_value').val() / 100;
            let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
            $('#sisa').val(sisa.toLocaleString('en-US'));
        }
    })

    $(document).on('change', '#discount_value', function() {
        if($('#discount_type').val() == "Percentage"){
            if($('#discount_value').val().split(",").join("") > 100){
                $('#discount_value').val(100);
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Percentage is more than 100',
                });
            }
            let total_discount = $('#sub_total').val().split(",").join("") * $('#discount_value').val() / 100;
            $('#discount').val(total_discount.toLocaleString('en-US'));
            let grandtotal = $('#sub_total').val().split(",").join("");
            $('#grand_total').val(grandtotal.toLocaleString('en-US'));
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }

        }else if($('#discount_type').val() == 'Fixed'){
            $('#discount').val($('#discount_value').val());
            let grandtotal = $('#sub_total').val().split(",").join("");
            $('#grand_total').val(grandtotal.toLocaleString('en-US'));
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#grand_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
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
    })

});
</script>
@endsection
@endsection
