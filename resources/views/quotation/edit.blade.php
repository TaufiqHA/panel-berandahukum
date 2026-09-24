@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Edit Quotation Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Edit Quotation Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Edit Quotation</h2>
    <p class="section-lead">
        Berikut adalah data edit quotation toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Edit Quotation</h4>
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
                                    placeholder="Ref Number"
                                    value="{{ $penjualan->kode_quotation }}"
                                    readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nama Toko</label>
                                <select class="form-control" name="nama_toko" id="nama_toko"></select>
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
                                <input type="text" class="form-control" required="" id="nama_pembeli"
                                    name="nama_pembeli" value="{{ $penjualan->nama_pembeli }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli" value="{{ $penjualan->alamat_pembeli }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="{{ $penjualan->telepon }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
								<input {{ $penjualan->show_option == 1 ? "checked" : "" }} class="form-check-input" type="checkbox" value="1" name="show_option" id="show_option"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_option" style="margin-left: 1.25rem;">
                                    Option Text
                                </label>
                                <br />
                                <input type="text" class="form-control"  id="option_text"
                                    name="option_text" placeholder="Masukan option text disini"  value="{{ $penjualan->option_text }}">
                                <div class="invalid-feedback feedback-option_text">
                                    Option Text harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
								<input {{ $penjualan->show_project == 1 ? "checked" : "" }} class="form-check-input" type="checkbox" value="1" name="show_project" id="show_project"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_project" style="margin-left: 1.25rem;">
                                    Nama Project
                                </label>
                                <br />
                                <input type="text" class="form-control" id="nama_project" name="nama_project" placeholder="Masukan nama project disini" value="{{ $penjualan->nama_project }}">
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
                            <div class="form-group col-md-12">
                                <input {{ $penjualan->ppn != 0 ? "checked" : "" }} class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    PPN
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan_pembayaran" id="keterangan_pembayaran"
                                    style="height: 150px;">{{ $penjualan->keterangan }}</textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                                <br />
                                <input {{ $penjualan->show_infopembayaran == 1 ? "checked" : "" }} class="form-check-input" type="checkbox" value="1" name="show_infopembayaran" id="show_infopembayaran"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_infopembayaran" style="margin-left: 1.25rem;">
                                    Cara Pembayaran
                                </label>
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
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-danger" type="button" id="batal">Batal</button>
                    <button class="btn btn-info" type="button" id="draft">Draft</button>
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
        $.each(data.detail_quotation, function (i,v) {
            $('#detail-barang').html('');
            let discount;
            if(v.discount == null){
                discount = '';
            }else{
                discount = v.discount;
            }
            let keterangan = v.keterangan;
            let detail_barang_id = v.id;
            // let id_barang = data.barang[i].id;
            // let nama_barang = data.barang[i].nama_product;
            if(keterangan == null){
                keterangan = '';
            }
            // let url_toko_edit = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            // let url_toko = "{{ url('barang/toko') }}/"+$('#nama_toko').val();
            let url_toko = "{{ url('barang/select') }}";
            let subtotal;
            if(v.discount != null || v.discount != 0){
                let discount = (v.price * v.jumlah) * v.discount /100;
                subtotal = (v.price * v.jumlah) - discount;
            }else{
                subtotal = v.price * v.jumlah;
            }

            detail_barang += '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<div class="form-group col-md-1">'+
                    '<label>&nbsp;</label><br>'+
                    '<button type="button" class="btn btn-light btn-move-barang" style="cursor: move;"><i class="fas fa-arrows-alt"></i></button>'+
                '</div>'+
                '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.id+'" class="edit_detail_barang">'+
                '<div class="form-group col-md-2">'+
                    '<label>Pilih Barang</label>'+
                    '<select class="form-control edit_detail_barang nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required=""></select>'+
                    '<div class="invalid-feedback feedback-nama_barang_'+row_edit+'">'+
                        'Barang harus dipilih.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Qty</label>'+
                    '<input value="'+v.jumlah+'" type="number" min="0" value="0" name="jumlah" id="jumlah_'+row_edit+'" class="form-control edit_detail_barang jumlah" required data-id="'+row_edit+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Harga</label>'+
                    '<input value="'+v.price.toLocaleString('en-US')+'" data-id="'+row_edit+'" type="text" name="harga" id="harga_'+row_edit+'" class="form-control edit_detail_barang money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+row_edit+'">'+
                        'Harga harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input value="'+discount+'" data-id="'+row_edit+'" type="text" name="discount" id="discount_'+row_edit+'" class="form-control edit_detail_barang discount">'+
                    '<div class="invalid-feedback feedback-discount_'+row_edit+'">'+
                        'Discount harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Sub Total</label>'+
                    '<input type="text" name="subtotal" id="subtotal_'+row_edit+'" class="form-control edit_detail_barang subtotal" value="'+subtotal.toLocaleString('en-US')+'" readonly>'+
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
            '</div>';

            row_edit ++;
        });
        $('#detail-barang').append(detail_barang);
        $('#save').attr('data-id', id);
        $('#draft').attr('data-id', id);
    }

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

    // $(document).on("select2:select","#nama_toko", function(e){
    //     $("#nama_toko_to").select2({
    //         placeholder: "Pilih Toko",
    //         ajax: {
    //             url: "{{ url('toko/except') }}/"+$(this).val(),
    //             dataType: "json",
    //             cache: true,
    //             data: function(params) {
    //                 return {
    //                     term: params.term || "",
    //                     page: params.page || 1
    //                 }
    //             },
    //         }
    //     });
    //     $(".nama_barang, .serial_number").val(null).trigger("change");
    //     $(".stock_toko_awal, .stock_toko_tujuan, .jumlah").val('');
    //     $(".nama_barang").select2({
    //         placeholder: "Pilih Barang",
    //         ajax: {
    //             url: "{{ url('barang/toko') }}/"+$(this).val(),
    //             dataType: "json",
    //             cache: true,
    //             data: function(params) {
    //                 return {
    //                     term: params.term || "",
    //                     page: params.page || 1
    //                 }
    //             },
    //         }
    //     });
    // });

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
        
        $.ajax({
            url: "{{ url('barang') }}/" + $(this).val(),
            type: "get",
            dataType: "json",
            success: function(response){
                let harga = 0;
                if(response && response.harga !== null && response.harga !== undefined && response.harga !== ''){
                    let cleanHarga = response.harga.toString().replace(/,/g, '');
                    harga = parseInt(cleanHarga) || 0;
                }
                $("#harga_"+id).val(harga.toLocaleString('en-US'));
                $("#harga_"+id).trigger('change');
            },
            error: function(){
                $("#harga_"+id).val(0);
                $("#harga_"+id).trigger('change');
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
        //$('.needs-validation').addClass('was-validated');
        //if($('.invalid-feedback:visible').length == 0){
        if($('#nama_toko').val() > 0){
            let url_toko = "{{ url('barang/select') }}";
            $('#detail-barang').append(
            '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'">'+
                '<div class="form-group col-md-1">'+
                    '<label>&nbsp;</label><br>'+
                    '<button type="button" class="btn btn-light btn-move-barang" style="cursor: move;"><i class="fas fa-arrows-alt"></i></button>'+
                '</div>'+
                '<div class="form-group col-md-2">'+
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
                '$("#nama_barang_'+row_edit+'").select2({'+
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
        }else{
		   Swal.fire({
            title: 'Warning',
            text: "Harap pilih toko terlebih dahulu, untuk proses cek stock barang",
            icon: 'error'        });
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
                        url: "{{ url('quotation/delete-barang') }}",
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

            $('.row-detail-barang').each(function(){
                let row = [];
                $(this).find('.edit_detail_barang, .detail_barang_new').each(function(){
                    row.push($(this).val());
                });
                databarang.push(row);
            });

            $('.needs-validation-payment').find('.form-control').each(function(){
                if($("#"+$(this).attr('id')).attr("name") != 'keterangan_pembayaran'){
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val().replace(/,/g , '');
                }else{
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
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
            if(databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang yang akan dijual!',
                })
            }else{
                let id = $(this).data('id');
                data.databarang = databarang;
                $.ajax({
                    url: "{{ url('quotation/update') }}/"+id,
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        if (response.status == 'error') {
                            $('#create-modal').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }else{
                            Swal.fire({
                                title: 'Success',
                                icon: 'success',
                                text: 'Data has been updated.',
                            }).then((result) => {
                                window.location = "{{ url('quotation') }}";
                            });
                        }
                       
                    },
                    error: function(response){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
            }
        }
    });
    
    $(document).on('click','#draft', function(e) {
        e.stopPropagation();
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

            $('.row-detail-barang').each(function(){
                let row = [];
                $(this).find('.edit_detail_barang, .detail_barang_new').each(function(){
                    row.push($(this).val());
                });
                databarang.push(row);
            });

            $('.needs-validation-payment').find('.form-control').each(function(){
                if($("#"+$(this).attr('id')).attr("name") != 'keterangan_pembayaran'){
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val().replace(/,/g , '');
                }else{
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
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
                let id = $(this).data('id');
                data.databarang = databarang;
                $.ajax({
                    url: "{{ url('quotation/update') }}/"+id,
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        if (response.status == 'error') {
                            $('#create-modal').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }else{
                            Swal.fire({
                                title: 'Success',
                                icon: 'success',
                                text: 'Data has been updated.',
                            }).then((result) => {
                                window.location = "{{ url('quotation') }}";
                            });
                        }
                    },
                    error: function(response){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                });
           
       
    });
    
    $(document).on('click','#batal', function(e) {
		window.location = "{{ url('quotation') }}";
	 });

    // Mengaktifkan fitur drag and drop pada container detail barang
    var el = document.getElementById('detail-barang');
    if(el) {
        var sortable = Sortable.create(el, {
            animation: 150, // Animasi saat digeser (ms)
            ghostClass: 'bg-light', // Warna background saat item di-drag
            handle: '.btn-move-barang', // Area/Tombol yang bisa di-drag
        });
    }
	 
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection
@endsection
