@extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>Edit Penjualan Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Edit Penjualan Toko</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Edit Penjualan</h2>
    <p class="section-lead">
        Berikut adalah data edit penjualan toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Edit Penjualan</h4>
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
                                    placeholder="Ref Number" value="{{ $penjualan->kode_penjualan }}" readonly>
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
                                <input type="text" class="form-control" required="" id="nama_pembeli"
                                    name="nama_pembeli" value="{{ $penjualan->nama_pembeli }}">
                                <div class="invalid-feedback feedback-nama_pembeli">
                                    Nama Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Pembeli</label>
                                <input type="text" class="form-control" id="alamat_pembeli" name="alamat_pembeli"
                                    value="{{ $penjualan->alamat_pembeli }}">
                                <div class="invalid-feedback feedback-alamat_pembeli">
                                    Alamat Pembeli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Pembeli</label>
                                <input type="text" class="form-control" id="telepon" name="telepon"
                                    value="{{ $penjualan->telepon }}">
                                <div class="invalid-feedback feedback-telepon">
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
                                <input type="text" class="form-control" id="option_text"
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
                            <div class="form-group {{ $penjualan->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama">
                                <label>Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
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
                            <div class="form-group {{ $penjualan->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama">
                                <label>Cara Pembayaran</label>
                                <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                                    <option value="Lunas" {{ $penjualan->payment_status === "Lunas" ? "selected" : "" }}>Lunas
                                    </option>
                                    <option value="Cash Before Delivery (CBD)" {{ $penjualan->payment_status === "Cash Before Delivery (CBD)" ? "selected" : "" }}>Cash Before Delivery (CBD)</option>
                                    <option value="Cash On Delivery (COD)" {{ $penjualan->payment_status === "Cash On Delivery (COD)" ? "selected" : "" }}>Cash On Delivery (COD)</option>
                                    <option value="Tempo"
                                        {{ $penjualan->payment_status === "Tempo" ? "selected" : "" }}>Tempo</option>
                                    <option value="DP" {{ $penjualan->payment_status === "DP" ? "selected" : "" }}>DP
                                    </option>
                                </select>
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Cara Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $penjualan->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama" id="waktu-bulan"
                                style={{ $penjualan->payment_status === "Tempo" ? "" : "display:none" }}>
                                <label>Waktu / Bulan</label>
                                <input type="text" class="form-control" id="tempo_waktu" name="tempo_waktu" value="{{$penjualan->waktu}}">
                                <div class="invalid-feedback feedback-status_pembayaran">
                                    Lama Waktu Pembayaran harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $penjualan->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama" id="grand-total"
                                style="{{ $penjualan->ppn != "" ? "display: none" : "" }}">
                                <label>Grand Total</label>
                                <input type="text" class="form-control" name="sub_total" id="sub_total"
                                    readonly value="{{ number_format($penjualan->subtotal) }}">
                                <div class="invalid-feedback feedback-sub_total">
                                    Grand Total harus diisi.
                                </div>
                            </div>
                            <div class="form-group {{ $penjualan->payment_status === 'Tempo' ? 'col-md-3' : 'col-md-4' }} row_pertama div_ppn"
                                style="{{ $penjualan->ppn != "" ? "" : "display: none" }}">
                                <label>Total</label>
                                <input type="text" class="form-control" name="input_total" id="input_total"
                                    value="{{ number_format($penjualan->subtotal) }}" readonly>
                                <div class="invalid-feedback feedback-input_total">
                                    Total harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;"
                                    {{ $penjualan->ppn != "" ? "checked" : "" }}>
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
                            <div class="col-md-6 mt-4">
                                <div class="form-group row div_ppn" id="div-ppn"
                                    style="{{ $penjualan->ppn != "" ? "" : "display: none;" }}">
                                    <label class="col-sm-3 col-form-label">PPN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="input_ppn" id="input_ppn"
                                            value="{{ number_format($penjualan->ppn) }}" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            Input PPN harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row div_ppn" id="div-ppn"
                                    style="{{ $penjualan->ppn != "" ? "" : "display: none;" }}">
                                    <label class="col-sm-3 col-form-label">Grand Total</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="grand_total"
                                            id="grand_total"
                                            value="{{ number_format($penjualan->subtotal + ($penjualan->subtotal * 11 / 100)) }}"
                                            readonly>
                                        <div class="invalid-feedback feedback-grand_total">
                                            Total harus diisi.
                                        </div>
                                    </div>
                                </div>
                                <div id="dp-payment"
                                    style="@if($penjualan->payment_status == 'DP' || $penjualan->payment_status == 'Tempo') @else display:none @endif">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">DP</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control money-format" name="dp_payment"
                                                id="dp_payment" value="{{ number_format($penjualan->dp_payment) }}">
                                            <div class="invalid-feedback feedback-dp_payment">
                                                Dp harus diisi.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Sisa</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="sisa" id="sisa"
                                                value="@if($penjualan->payment_status == 'Tempo' || $penjualan->payment_status == 'DP'){{ number_format($penjualan->sisa) }}
                                                @else 0 @endif" readonly>
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
					@if($penjualan->status == 1)
					Status : Done &nbsp;&nbsp;&nbsp;&nbsp;<br ><br >
					@endif
					<button class="btn btn-danger" data-button="batal" type="button" id="batal">Batal</button>
					@if($penjualan->status == 2)
                    <button class="btn btn-info" data-button="draft" type="button" id="draft">Draft</button>
                    @endif
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
    if(data.toko && data.toko.id){
        $('#nama_toko').empty().append('<option selected value="'+data.toko.id+'">'+data.toko.nama_toko+'</option>');
    }
    @if (Auth::user()->status == 2)
        $('#nama_toko').prop('disabled', true);
    @endif
    getDetailBarang();
    function getDetailBarang() {
        let id = {{$penjualan->id}};
        $('#detail-barang').html('');
        let detail_barang = '';
        if(data.detail_penjualan && data.detail_penjualan.length > 0){
            $.each(data.detail_penjualan, function (i,v) {
                let discount;
                if(v.discount == null){
                    discount = '';
                }else{
                    discount = v.discount;
                }
                let id_serial_number = '';
                let name_serial_number = '';

                if (v.serial_number && v.serial_number.serial_number) {
                    name_serial_number = v.serial_number.serial_number;
                    id_serial_number = v.gudang_barang_id || v.serial_number.id;
                } else if (v.gudang_barang) {
                    name_serial_number = (typeof v.gudang_barang.serial_number === 'string' && v.gudang_barang.serial_number)
                        ? v.gudang_barang.serial_number
                        : (v.gudang_barang.serial_number_id || (v.gudang_barang.serial_number && v.gudang_barang.serial_number.serial_number ? v.gudang_barang.serial_number.serial_number : ''));
                    id_serial_number = v.gudang_barang_id || v.gudang_barang.id || '';
                } else if (v.serial_number_id) {
                    id_serial_number = v.gudang_barang_id || v.serial_number_id;
                    name_serial_number = v.serial_number_id;
                }

                let id_barang = v.barang ? v.barang.id : (v.barang_id || '');
                let nama_barang = v.barang ? v.barang.nama_product : '';
                let wajib_sn = v.barang && typeof v.barang.wajib_serial_number !== 'undefined' ? v.barang.wajib_serial_number : ((id_serial_number || name_serial_number) ? 1 : 0);
                let disabled = (wajib_sn == 1 || id_serial_number || name_serial_number) ? '' : 'disabled';
                let data_subtotal = v.price;
                if(v.discount != '' && v.discount != null){
                    data_subtotal = v.price - (v.price * v.discount / 100);
                }

                detail_barang += '<div class="form-row row-detail-barang" id="row_barang_'+row_edit+'" data-id="'+row_edit+'" data-edit="'+row_edit+'">'+
                    '<div class="form-group col-md-1">'+
                        '<label>&nbsp;</label><br>'+
                        '<button type="button" class="btn btn-light btn-move-barang" style="cursor: move;"><i class="fas fa-arrows-alt"></i></button>'+
                    '</div>'+
                    '<input type="hidden" name="id" id="id_'+row_edit+'" value="'+v.id+'" class="edit_detail_barang barang_existing">'+
                    '<div class="form-group col-md-2">'+
                        '<label>Pilih Barang</label>'+
                        '<select class="form-control edit_detail_barang nama_barang" data-id="'+row_edit+'" name="nama_barang" id="nama_barang_'+row_edit+'" required="">'+
                            (id_barang ? '<option selected value="'+id_barang+'">'+nama_barang+'</option>' : '')+
                        '</select>'+
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
                    '<div class="form-group col-md-2">'+
                        '<label>Pilih Serial Number</label>'+
                        '<select class="form-control edit_detail_barang serial_number" name="serial_number" id="serial_number_'+row_edit+'" required="" '+disabled+'>'+
                            (id_serial_number ? '<option selected value="'+id_serial_number+'">'+name_serial_number+'</option>' : '')+
                        '</select>'+
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
                    '<div class="form-group col-md-1">'+
                        '<label>Jumlah</label>'+
                        '<input type="text" name="subtotal" id="subtotal_'+row_edit+'" class="form-control edit_detail_barang subtotal" value="'+data_subtotal.toLocaleString('en-US')+'" readonly>'+
                        '<div class="invalid-feedback feedback-keterangan_'+row_edit+'">'+
                            'Keterangan harus dipilih.'+
                        '</div>'+
                    '</div>'+
                    '<div class="form-group col-md-1">'+
                        '<label>Action</label>'+
                    '<br>'+
                        '<button data-id="'+v.id+'" data-row="'+row_edit+'" type="button" class="btn btn-icon btn-danger btn-delete-barang"><i class="far fas fa-trash"></i></button>'+
                    '</div>'+
                '</div>';
                
                let currentRow = row_edit;
                let databarangid = {
                    id_toko: $('#nama_toko').val(),
                    id_barang: id_barang
                };
                getTotalBarang(databarangid, currentRow);

                row_edit ++;
            });
            $('#detail-barang').append(detail_barang);

            // Initialize select2 after appending rows to DOM
            for (let r = 1; r < row_edit; r++) {
                let currentBarangId = $('#nama_barang_' + r).val();
                let url_serial_number = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+currentBarangId;
                let url_toko = "{{ url('barang/select') }}";

                $('#nama_barang_' + r).select2({
                    placeholder: {
                        id: -1,
                        text: "Pilih Barang"
                    },
                    minimumInputLength: 3,
                    ajax: {
                        url: url_toko,
                        dataType: "json",
                        cache: true,
                        data: function(params) {
                            return {
                                term: params.term || "",
                                page: params.page || 1
                            };
                        }
                    }
                });

                $('#serial_number_' + r).select2({
                    placeholder: "Pilih Serial Number",
                    ajax: {
                        url: url_serial_number,
                        dataType: "json",
                        cache: true,
                        data: function(params) {
                            return {
                                term: params.term || "",
                                page: params.page || 1
                            };
                        }
                    }
                });
            }
            $(".select2-container").css("width","100%");
        }
        $('#save').attr('data-id', id);
        $('#draft').attr('data-id', id);
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

    $(document).on('change', '.jumlah', function() {
        let id = $(this).data('id');
        $('.feedback-jumlah_'+id).hide();
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
            if($('#status_pembayaran').val() == 'DP' || $('#status_pembayaran').val() == 'Tempo'){
                let sisa = $('#sub_total').val().split(",").join("") - $('#dp_payment').val().split(",").join("");
                $('#sisa').val(sisa.toLocaleString('en-US'));
            }
            $('#input_ppn').val(0);
        }
    });

    $(document).on('click', '#add-barang', function(e) {
        $('.invalid-feedback').hide();
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
                '<div class="form-group col-md-1">'+
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
        let barangValid = true;
        let barangErrorMsg = '';

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
                text: 'Silahkan Masukan Barang yang akan dijual!',
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
                let name = $("#"+$(this).attr('id')).attr("name");
                let val = $("#"+$(this).attr('id')).val() || '';
                if(name == 'grand_total' || name == 'input_ppn' || name == 'input_total' || name == 'sub_total' || name == 'dp_payment' || name == 'sisa'){
                    postData[name] = val.split(",").join("");
                }else{
                    postData[name] = val;
                }
            }
        });

        if($('#show_infopembayaran').is(':checked')){
            postData['show_infopembayaran'] = $("#show_infopembayaran").val();
        }else{
            postData['show_infopembayaran'] = 0;
        }
        if($('#show_option').is(':checked')){
            postData['show_option'] = $("#show_option").val();
        }else{
            postData['show_option'] = 0;
        }
        if($('#show_project').is(':checked')){
            postData['show_project'] = $("#show_project").val();
        }else{
            postData['show_project'] = 0;
        }
        postData['option_text'] = $('#option_text').val();
        postData['nama_project'] = $('#nama_project').val();
        postData['status'] = 1;
        postData.databarang = databarang;

        $.ajax({
            url: "{{ url('penjualan/update') }}/"+id,
            type: "post",
            data: JSON.stringify(postData),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                if (response.status == 'error') {
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
                        window.location = "{{ url('penjualan') }}";
                    });
                }
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
                    let name = $("#"+$(this).attr('id')).attr("name");
                    let val = $("#"+$(this).attr('id')).val() || '';
                    if(name == 'grand_total' || name == 'input_ppn' || name == 'input_total' || name == 'sub_total' || name == 'dp_payment' || name == 'sisa'){
                        postData[name] = val.split(",").join("");
                    }else{
                        postData[name] = val;
                    }
                }
            });
            if($('#show_infopembayaran').is(':checked')){
				postData['show_infopembayaran'] = $("#show_infopembayaran").val();
			}else{
				postData['show_infopembayaran'] = 0;
			}
			if($('#show_option').is(':checked')){
				postData['show_option'] = $("#show_option").val();
			}else{
				postData['show_option'] = 0;
			}
			if($('#show_project').is(':checked')){
				postData['show_project'] = $("#show_project").val();
			}else{
				postData['show_project'] = 0;
			}
			postData['option_text'] = $('#option_text').val();
			postData['nama_project'] = $('#nama_project').val();
            postData['status'] = 2;
            postData.databarang = databarang;
            
            $.ajax({
                url: "{{ url('penjualan/update') }}/"+id,
                type: "post",
                data: JSON.stringify(postData),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    if (response.status == 'error') {
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
                            window.location = "{{ url('penjualan') }}";
                        });
                    }
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
		window.location = "{{ url('penjualan') }}";
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
