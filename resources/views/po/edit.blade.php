@extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>Edit PO Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Edit PO Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Edit PO</h2>
    <p class="section-lead">
        Berikut adalah data edit po toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Edit PO</h4>
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
                                    value="{{ $penjualan->kode_po }}"
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
                                <label>Nama Purchasing</label>
                                @php $all_signatures = \App\Models\Signature::all(); @endphp
                                <select class="form-control select2" required="" id="nama_purchase" name="nama_purchase">
                                    <option value="">Pilih Purchasing</option>
                                    @foreach($all_signatures as $signature)
                                        <option value="{{ $signature->name }}" {{ $penjualan->nama_purchase == $signature->name ? 'selected' : '' }}>{{ $signature->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback feedback-nama_sales">
                                    Nama Purchasing harus diisi.
                                </div>
                            </div>
                            					   
							<div class="form-group col-md-3">
								<label>Nama Supplier</label>
								<select class="form-control" name="nama_supplier" id="nama_supplier" required="" ></select>
								<div class="invalid-feedback feedback-nama_supplier">
									Nama Supplier harus diisi.
								</div>
							</div>
							<div class="form-group col-md-4">
								<input {{ $penjualan->show_tempo == 1 ? "checked" : "" }} class="form-check-input" type="checkbox" value="1" name="show_tempo" id="show_tempo"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="show_tempo" style="margin-left: 1.25rem;">
                                    Jatuh Tempo
                                </label>
                                <br />
                                <input type="text" class="form-control datepicker" id="jatuh_tempo" name="jatuh_tempo" placeholder="Jatuh tempo" value="{{ $penjualan->show_tempo != null ? date('d-m-Y', strtotime($penjualan->jatuh_tempo)) : "" }}">
                                <div class="invalid-feedback feedback-jatuh_tempo_from">
                                    Jatuh Tempo harus diisi.
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
                         {{--<div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Nama Pembeli</label>
                                <input type="text" class="form-control" required="" id="nama_supplier"
                                    name="nama_supplier" value="{{ $penjualan->nama_supplier }}">
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_supplier" name="alamat_supplier" value="{{ $penjualan->alamat_supplier }}">
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
                        </div>--}}
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
                            <div class="form-group col-md-6">
                                <input {{ $penjualan->ppn != 0 ? "checked" : "" }} class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    PPN
                                </label>
                            </div>
                            <div class="form-group col-md-2">
                                <input {{ $penjualan->po_dp > 0 ? "checked" : "" }} class="form-check-input" name="status_dp" type="checkbox" value="2" id="status_dp"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="status_dp" style="margin-left: 1.25rem;">
                                    DP
                                </label>
                            </div>
                            <div class="form-group col-md-2">
                                <input {{ $penjualan->status_bayar != 0 ? "checked" : "" }} name="status_bayar" class="form-check-input" type="checkbox" value="1" id="status_bayar"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    LUNAS
                                </label>
                            </div>
                            <div class="form-group col-md-2">
                                <input {{ $penjualan->status_terima != 0 ? "checked" : "" }} id="status_terima" class="form-check-input" type="checkbox" value="1" id="status_terima"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    DITERIMA
                                </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keterangan_pembayaran" id="keterangan_pembayaran"
                                    style="height: 150px;">{{ $penjualan->keterangan }}</textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="mt-1">
                                    <div class="mt-1 div_ppn" style="{{ $penjualan->ppn != 0 ? "" : "display: none;" }}">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Total</label>
                                        <input type="text" class="form-control" name="input_total"
                                            id="input_total" value="{{ $penjualan->ppn != 0 ? number_format($penjualan->subtotal) : "0" }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Grand Total harus diisi.
                                        </div>
                                    </div>
                                    <div style="{{ $penjualan->ppn != 0 ? "" : "display: none;" }}" class="mt-1 div_ppn">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">PPN</label>
                                        <input type="text" class="form-control" name="input_ppn" id="input_ppn"
                                            value="{{ number_format($penjualan->ppn) }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            PPN harus diisi.
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Grand
                                             Total</label>
                                        <input type="text" class="form-control" name="sub_total" id="sub_total"
                                            value="{{ $penjualan->ppn != 0 ? number_format($penjualan->subtotal + ($penjualan->subtotal * 11 /100)) : number_format($penjualan->subtotal) }}" readonly>
                                        <div class="invalid-feedback feedback-sub_total">
                                            Total harus diisi.
                                        </div>
                                    </div>
                                    <div style="{{ $penjualan->po_dp > 0 ? "" : "display: none;" }}" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Uang Muka</label>
                                        <input type="text" class="form-control money-format uangmuka" name="input_dp" id="input_dp"
                                            value="{{ number_format($penjualan->po_dp) }}" >
                                        <div class="invalid-feedback feedback-input_dp">
                                            Uang Muka harus diisi.
                                        </div>
                                    </div>

                                    <div style="{{ $penjualan->po_dp > 0 ? "" : "display: none;" }}" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Sisa Pembayaran</label>
                                        <input type="text" class="form-control money-format sisapem" readonly name="input_sisa" id="input_sisa"
                                            value="{{ $penjualan->ppn != 0 ? number_format( ($penjualan->subtotal + ($penjualan->subtotal * 11 /100)) - $penjualan->po_dp ) : number_format($penjualan->subtotal - $penjualan->po_dp) }}" readonly>
                                        <div class="invalid-feedback feedback-input_dp">
                                            Sisa Pembayaran harus diisi.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Alamat Pengiriman</label>
                                <textarea class="form-control" name="alamat_kirim" id="alamat_kirim"
                                    style="height: 150px;">{{ $penjualan->alamat_kirim }}</textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Alamat Pengiriman harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6"></div>
                            <div class="form-group col-md-6">
                                <label>Jenis Barang</label>
                               <input type="text" class="form-control" name="jenis_barang" id="jenis_barang"
                                            value="{{ $penjualan->jenis_barang }}" >
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Jenis barang harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-secondary" data-id="batal" type="button" id="batal">Kembali</button>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-danger" data-id="canceled" type="button" id="canceled">Canceled</button>
                            <button class="btn btn-info" data-id="draft" type="button" id="draft">Draft</button>
                            <button class="btn btn-primary" type="button" id="save">Simpan</button>
                        </div>

                    </div>
                  
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
    if(data.toko && data.toko.id){
        $('#nama_toko').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    }
    @if (Auth::user()->status == 2)
        $('#nama_toko').prop('disabled', true);
    @endif
    
    if(data.supplier && data.supplier.id){
		 $('#nama_supplier').empty().append('<option selected value="'+data.supplier.id+'">'+data.supplier.nama_supplier+' / ' + (data.supplier.sales || '') + '</option>');
	 }
    getDetailBarang();
    function getDetailBarang() {
        let id =  data.id;
        let detail_barang = '';
        //console.log(data.detail_po);
        //console.log(data.detail_po.length);
        if(data.detail_po && data.detail_po.length > 0){
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
            let detail_id_barang= 0;
            let detail_nama_barang = '';
            if(v.barang != null){
				detail_barang_id = v.barang.id;
				detail_nama_barang = v.barang.nama_product;
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
                    '$("#nama_barang_'+row_edit+'").empty().append("<option selected value='+detail_barang_id+'>'+detail_nama_barang+'  </option>");'+
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
		}//end cek jumlah detail barang
        $('#save').attr('data-id', id);
        $('#draft').attr('data-id', id);
        $('#canceled').attr('data-id', id);
    }

    $('#flexCheckDefault').click(function () {
        if(this.checked == true){
            let total = $('#sub_total').val().split(",").join("");
            let disc = total * 11/100;
            let subtotal = parseFloat(total)+parseFloat(disc);
            $('#input_ppn').val(disc.toLocaleString('en-US'));
            $('#input_total').val(parseFloat(total).toLocaleString('en-US'));
            $('#sub_total').val(subtotal.toLocaleString('en-US'));
            let harga = $('#input_dp').val().split(",").join("");
            let subtotal_sub = $('#sub_total').val().split(",").join("");
            let uangmuka = subtotal_sub - harga;
            $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
            $('.div_ppn').show();
        }else{
            let sum  = 0;
            $('.subtotal').each(function() {
                sum += parseFloat($(this).val().split(",").join(""));  
            });
            $('#sub_total').val(sum.toLocaleString('en-US'));
            $('#input_total').val(0);
            $('#input_ppn').val(0);
            let harga = $('#input_dp').val().split(",").join("");
            let subtotal_sub = $('#sub_total').val().split(",").join("");
            let uangmuka = subtotal_sub - harga;
            $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
            $('.div_ppn').hide();
        }
    });

    $('#status_dp').click(function () {
        if(this.checked == true){
            $('.div_dp').show();
            $('#status_bayar').prop('checked', false); 
        }else{
            $('.div_dp').hide();
        }
    });
    $('#status_bayar').click(function () {
        if(this.checked == true){
            $('#status_dp').prop('checked', false); 
            $('.div_dp').hide();
        }
    });
    $(document).on('change', '.uangmuka', function() {
        let harga = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
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
    
     $('#nama_supplier').select2({
        placeholder:'Pilih Supplier',
        ajax: {
            url: '{{ url("supplier/select") }}',
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
        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
    });

    $(document).on('click', '#add-barang', function(e) {
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
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
        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
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
        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
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
    })

    $(document).on('click','#save, .save', function(e) {
        e.stopPropagation();
        
        let id = $(this).attr('data-id') || $(this).data('id') || data.id;
        
        if(!$('#nama_toko').val()){
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Nama Toko harus dipilih!',
            });
        }
        
        if(!$('#date').val()){
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tanggal harus diisi!',
            });
        }

        let databarang = [];
        $('.row-detail-barang').each(function(){
            let row = [];
            $(this).find('.edit_detail_barang, .detail_barang_new').each(function(){
                row.push($(this).val());
            });
            databarang.push(row);
        });

        if(databarang.length === 0){
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Silahkan Masukan Barang!',
            });
        }

        if($('#show_tempo').is(':checked') && !$('#jatuh_tempo').val()){
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tanggal Jatuh Tempo harus di isi jika dicentang !',
            });
        }

        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });

        let postData = {};
        postData.button = $(this).data('button') || 'save';
        
        $('.needs-validation').find('.form-control').each(function(){
            if($(this).attr('id')){
                postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            }
        });

        $('.needs-validation-payment').find('.form-control').each(function(){
            if($(this).attr('id')){
                postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            }
        });

        if($('#status_bayar').is(':checked')){
            postData['status_bayar'] = $("#status_bayar").val();
        }else{
            postData['status_bayar'] = 0;
        }
        if($('#status_dp').is(':checked')){
            postData['input_dp'] = $("#input_dp").val();
        }else{
            postData['input_dp'] = 0;
        }
        if($('#status_terima').is(':checked')){
            postData['status_terima'] = $("#status_terima").val();
        }else{
            postData['status_terima'] = 0;
        }
        postData['status'] = 1;
        postData['alamat_kirim'] = $("#alamat_kirim").val();
        postData['jenis_barang'] = $("#jenis_barang").val();
        
        if($('#show_tempo').is(':checked')){
            postData['show_tempo'] = $("#show_tempo").val();
            postData['jatuh_tempo'] = $('#jatuh_tempo').val();
        }else{
            postData['show_tempo'] = 0;
            postData['jatuh_tempo'] = $('#jatuh_tempo').val();
        }

        postData.databarang = databarang;

        $.ajax({
            url: "{{ url('po/update') }}/"+id,
            type: "post",
            data: JSON.stringify(postData),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                Swal.fire({
                    title: 'Success',
                    icon: 'success',
                    text: 'Data has been updated.',
                }).then((result) => {
                    window.location = "{{ url('po') }}";
                });
            },
            error: function(errors){
                let msg = (errors.responseJSON && errors.responseJSON.message) ? errors.responseJSON.message : 'Terjadi kesalahan pada server.';
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: msg,
                });
            }
        });
    });
    
    $(document).on('click','#draft', function(e) {
        e.stopPropagation();
        let id = $(this).attr('data-id') || $(this).data('id') || data.id;

        if($('#nama_toko').val()){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let postData = {};
            postData.button = $(this).data('button') || 'draft';
            let databarang = [];
            
            $('.needs-validation').find('.form-control').each(function(){
                if($(this).attr('id')){
                    postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });

            $('.row-detail-barang').each(function(){
                let row = [];
                $(this).find('.edit_detail_barang, .detail_barang_new').each(function(){
                    row.push($(this).val());
                });
                databarang.push(row);
            });

            $('.needs-validation-payment').find('.form-control').each(function(){
                if($(this).attr('id')){
                    postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });

            if($('#status_bayar').is(':checked')){
				postData['status_bayar'] = $("#status_bayar").val();
			}else{
				postData['status_bayar'] = 0;
			}
            if($('#status_dp').is(':checked')){
				postData['input_dp'] = $("#input_dp").val();
			}else{
				postData['input_dp'] = 0;
			}
			if($('#status_terima').is(':checked')){
				postData['status_terima'] = $("#status_terima").val();
			}else{
				postData['status_terima'] = 0;
			}
            postData['status'] = 2;
            postData['alamat_kirim'] = $("#alamat_kirim").val();
			postData['jenis_barang'] = $("#jenis_barang").val();
            if($('#show_tempo').is(':checked')){
				postData['show_tempo'] = $("#show_tempo").val();
				if($('#jatuh_tempo').val() == ''){
					return Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Tanggal Jatuh Tempo harus di isi jika dicentang !',
					});
				}else{
					postData['jatuh_tempo'] = $('#jatuh_tempo').val();
				}
			}else{
				postData['show_tempo'] = 0;
				postData['jatuh_tempo'] = '';
			}
            postData.databarang = databarang;
            
            $.ajax({
                url: "{{ url('po/update') }}/"+id,
                type: "post",
                data: JSON.stringify(postData),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been updated.',
                    }).then((result) => {
                        window.location = "{{ url('po') }}";
                    });
                },
                error: function(errors){
                    let msg = (errors.responseJSON && errors.responseJSON.message) ? errors.responseJSON.message : 'Terjadi kesalahan pada server.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg,
                    });
                }
            });
            
        }else{
			return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Minimal Nama Toko Harus di ISI!',
            });
		}
    });
    
     $(document).on('click','#batal', function(e) {
		window.location = "{{ url('po') }}";
	 });

     $(document).on('click','#canceled', function(e) {
        e.stopPropagation();
        let id = $(this).attr('data-id') || $(this).data('id') || data.id;

        if($('#nama_toko').val()){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let postData = {};
            postData.button = $(this).data('button') || 'canceled';
            let databarang = [];
            
            $('.needs-validation').find('.form-control').each(function(){
                if($(this).attr('id')){
                    postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });

            $('.row-detail-barang').each(function(){
                let row = [];
                $(this).find('.edit_detail_barang, .detail_barang_new').each(function(){
                    row.push($(this).val());
                });
                databarang.push(row);
            });

            $('.needs-validation-payment').find('.form-control').each(function(){
                if($(this).attr('id')){
                    postData[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                }
            });

            if($('#status_bayar').is(':checked')){
				postData['status_bayar'] = $("#status_bayar").val();
			}else{
				postData['status_bayar'] = 0;
			}
            if($('#status_dp').is(':checked')){
				postData['input_dp'] = $("#input_dp").val();
			}else{
				postData['input_dp'] = 0;
			}
			if($('#status_terima').is(':checked')){
				postData['status_terima'] = $("#status_terima").val();
			}else{
				postData['status_terima'] = 0;
			}
            postData['status'] = 3;
            postData['alamat_kirim'] = $("#alamat_kirim").val();
			postData['jenis_barang'] = $("#jenis_barang").val();
            if($('#show_tempo').is(':checked')){
				postData['show_tempo'] = $("#show_tempo").val();
				if($('#jatuh_tempo').val() == ''){
					return Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Tanggal Jatuh Tempo harus di isi jika dicentang !',
					});
				}else{
					postData['jatuh_tempo'] = $('#jatuh_tempo').val();
				}
			}else{
				postData['show_tempo'] = 0;
				postData['jatuh_tempo'] = '';
			}
            postData.databarang = databarang;
            
            $.ajax({
                url: "{{ url('po/update') }}/"+id,
                type: "post",
                data: JSON.stringify(postData),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been updated.',
                    }).then((result) => {
                        window.location = "{{ url('po') }}";
                    });
                },
                error: function(errors){
                    let msg = (errors.responseJSON && errors.responseJSON.message) ? errors.responseJSON.message : 'Terjadi kesalahan pada server.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg,
                    });
                }
            });
            
        }else{
			return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Minimal Nama Toko Harus di ISI!',
            });
		}
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
