    @extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>PO</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">PO</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">PO</h2>
    <p class="section-lead">
        Berikut adalah data po melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>PO</h4>
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
                                    value="PO - {{  sprintf('%07d', $po === null ? "1" : $po->id+1) }}">
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
                                        <option value="{{ $signature->name }}">{{ $signature->name }}</option>
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
                                <select class="form-control" name="nama_supplier" id="nama_supplier" required="" ></select>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Supplier harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
								<input class="form-check-input" type="checkbox" value="1" name="show_tempo" id="show_tempo"
									style="margin-left: 0px;cursor: pointer;">
								<label class="form-check-label" for="show_tempo" style="margin-left: 1.25rem;">
									Jatuh Tempo
								</label>
								<br />
								<input type="text" class="form-control datepicker" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tgl Jatuh Tempo">
								<div class="invalid-feedback feedback-nama_toko_from">
								   Jatuh Tempo harus diisi.
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
                            <div class="form-group col-md-6">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    PPN
                                </label>
                            </div>
                            <div class="form-group col-md-2">
                                <input  class="form-check-input" name="status_dp" type="checkbox" value="2" id="status_dp"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="status_dp" style="margin-left: 1.25rem;">
                                    DP
                                </label>
                            </div>
                             <div class="form-group col-md-2">
                                <input  class="form-check-input" name="status_bayar" type="checkbox" value="1" id="status_bayar"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    LUNAS
                                </label>
                            </div>
                            <div class="form-group col-md-2">
                                <input  class="form-check-input" name="status_terima" type="checkbox" value="1" id="status_terima"
                                    style="margin-left: 0px;cursor: pointer;">
                                <label class="form-check-label" for="flexCheckDefault" style="margin-left: 1.25rem;">
                                    DITERIMA
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
                            <div class="form-group col-md-6">
                                <div class="mt-1">
                                    <div class="mt-1 div_ppn" style="display: none;">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Total</label>
                                        <input type="text" class="form-control" required="" name="input_total"
                                            id="input_total" value="0" readonly>
                                        <div class="invalid-feedback feedback-input_total">
                                            Grand Total harus diisi.
                                        </div>
                                    </div>
                                    <div style="display: none" class="mt-1 div_ppn">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">PPN</label>
                                        <input type="text" class="form-control" required="" name="input_ppn" id="input_ppn"
                                            value="0" readonly>
                                        <div class="invalid-feedback feedback-input_ppn">
                                            PPN harus diisi.
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Grand
                                            Total</label>
                                        <input type="text" class="form-control" required="" name="sub_total" id="sub_total"
                                            value="0" readonly>
                                        <div class="invalid-feedback feedback-sub_total">
                                            Total harus diisi.
                                        </div>
                                    </div>

                                    <div style="display: none" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Uang Muka</label>
                                        <input type="text" class="form-control money-format uangmuka" required="" name="input_dp" id="input_dp"
                                            value="0" >
                                        <div class="invalid-feedback feedback-input_dp">
                                            Uang Muka harus diisi.
                                        </div>
                                    </div>

                                    <div style="display: none" class="mt-1 div_dp">
                                        <label
                                            style="font-weight:600; color:#34395e;font-size:12px;letter-spacing:0.5px">Sisa Pembayaran</label>
                                        <input type="text" class="form-control money-format sisapem" required="" readonly name="input_sisa" id="input_sisa"
                                            value="0" readonly>
                                        <div class="invalid-feedback feedback-input_dp">
                                            Sisa Pembayaran harus diisi.
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Alamat Pengiriman</label>
                                <textarea class="form-control" name="alamat_kirim" id="alamat_kirim"
                                    style="height: 150px;"></textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Alamat Pengiriman harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-6"></div>
                            <div class="form-group col-md-6">
                                <label>Jenis Barang</label>
                               <input type="text" class="form-control" name="jenis_barang" id="jenis_barang"
                                            value="" >
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Jenis barang harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-danger" data-id="batal" type="button" id="batal">Batal</button>
                    <button class="btn btn-info" data-id="draft" type="button" id="draft">Draft</button>
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

    $('#flexCheckDefault').click(function ppn_setting() {
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

    let index = 0;
    $(document).on('click', '#add-barang', function(e) {
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
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
                    '<label>Qty</label>'+
                    '<input type="number" min="0" value="0" name="jumlah" id="jumlah_'+index+'" class="form-control jumlah" required data-id="'+index+'">'+
                    '<div class="invalid-feedback feedback-jumlah_'+index+'">'+
                        'Qty harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-3">'+
                    '<label>Harga</label>'+
                    '<input data-id="'+index+'" type="text" name="harga" id="harga_'+index+'" class="form-control money-format harga" required>'+
                    '<div class="invalid-feedback feedback-harga_'+index+'">'+
                        'Jumlah harus diisi.'+
                    '</div>'+
                '</div>'+
                '<div class="form-group col-md-1">'+
                    '<label>Diskon %</label>'+
                    '<input data-id="'+index+'" type="text" name="discount" id="discount_'+index+'" class="form-control discount">'+
                    '<div class="invalid-feedback feedback-discount_'+index+'">'+
                        'Diskon harus diisi.'+
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
                '$("#serial_number_'+index+'").select2({'+
                    'placeholder:"Pilih Serial Number",'+
                '});'+
                '$(".select2-container").css("width","100%");'+
            '</script'+'>'+
            '</div>');
        index ++;
        } 
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

    $(document).on("select2:select",".nama_barang", function(e){
        let status = $(this).data('id');
        // let barang = {}
        // barang.id = $(this).val();
        let id = $(this).val();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        $.ajax({
            url: "{{ url('barang') }}/"+id,
            type: "get",
            dataType: "json",
            success: function(response){
                if (response.harga == null) response.harga = 0;
                $("#harga_"+status).val(response.harga.toLocaleString('en-US'));
                Swal.close();
            }
        });
        // $.ajax({
        //     url: "{{ url('stock-in/price-list') }}",
        //     type: "post",
        //     data: JSON.stringify(barang),
        //     contentType: "application/json; charset=utf-8",
        //     dataType: "json",
        //     success: function(response){
        //         if(response.length > 0){
        //             console.log('stest');
        //             $("#harga_"+id).val(parseInt(response[0].price_list).toLocaleString('en-US'));
        //         }else{
        //             $("#harga_"+id).val(0);
        //         }
        //         Swal.close();
        //     }
        // });
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
            let datapayment = {}
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.form-control').each(function(){
                databarang.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            if($('#status_bayar').is(':checked')){
				data['status_bayar'] = $("#status_bayar").val();
			}else{
				data['status_bayar'] =  0;
			}
            if($('#status_dp').is(':checked')){
				data['input_dp'] = $("#input_dp").val();
			}else{
				data['input_dp'] =  0;
			}
			if($('#status_terima').is(':checked')){
				data['status_terima'] = $("#status_terima").val();
			}else{
				data['status_terima'] =  0;
			}
			if($('#show_tempo').is(':checked')){
				data['show_tempo'] = $("#show_tempo").val();
				if($('#jatuh_tempo').val() == ''){
					return Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Tanggal Jatuh Tempo harus di isi jika dicentang !',
					});
				}else{
					data['jatuh_tempo'] = $('#jatuh_tempo').val() ;
				}
			}else{
				data['show_tempo'] =  0;
				data['jatuh_tempo'] = $('#jatuh_tempo').val();
			}
            data['status'] =  1;
            data['alamat_kirim'] = $("#alamat_kirim").val();
			data['jenis_barang'] = $("#jenis_barang").val();
            if(databarang.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Silahkan Masukan Barang !',
                });
            }else{
                $.ajax({
                    url: "{{ url('po') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        $.ajax({
                            url: "{{ url('po/barang') }}/"+response.id,
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
                                    window.location = "{{ url('po') }}";
                                });
                            }
                        });
                    }
                });
            }
        }
    });
    
    $(document).on('click','#draft', function(e) {
        e.stopPropagation();
       // $('.needs-validation').addClass('was-validated');
        //$('.needs-validation-barang').addClass('was-validated');
        //validationBarang();
        //if($('.invalid-feedback:visible').length == 0){
        if($('#nama_toko').val() > 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            let databarang = []
            let datapayment = {}
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $('.needs-validation-barang').find('.form-control').each(function(){
                databarang.push($("#"+$(this).attr('id')).val())
            });
            $('.needs-validation-payment').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            if($('#status_bayar').is(':checked')){
				data['status_bayar'] = $("#status_bayar").val();
			}else{
				data['status_bayar'] =  0;
			}
            if($('#status_dp').is(':checked')){
				data['input_dp'] = $("#input_dp").val();
			}else{
				data['input_dp'] =  0;
			}
			if($('#status_terima').is(':checked')){
				data['status_terima'] = $("#status_terima").val();
			}else{
				data['status_terima'] =  0;
			}
            data['status'] =  2;
            if($('#show_tempo').is(':checked')){
				data['show_tempo'] = $("#show_tempo").val();
				if($('#jatuh_tempo').val() == ''){
					return Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Tanggal Jatuh Tempo harus di isi jika dicentang !',
					});
				}else{
					data['jatuh_tempo'] = $('#jatuh_tempo').val() ;
				}
			}else{
				data['show_tempo'] =  0;
				data['jatuh_tempo'] = '';
			}
			data['alamat_kirim'] = $("#alamat_kirim").val();
			data['jenis_barang'] = $("#jenis_barang").val();
                $.ajax({
                    url: "{{ url('po') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
						if(databarang.length > 0){
							$.ajax({
								url: "{{ url('po/barang') }}/"+response.id,
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
										window.location = "{{ url('po') }}";
									});
								}
							});
						}else{
							$('#create-modal').modal('hide');
									Swal.fire({
										title: 'Success',
										icon: 'success',
										text: 'Data has been saved.',
									}).then((result) => {
										window.location = "{{ url('po') }}";
									});
						}
                    }
                });
            
         }else{
			return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Minimal Nama Toko Harus di ISI!',
                })
		}
    });
    
    $(document).on('click','#batal', function(e) {
		window.location = "{{ url('po') }}";
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
        $('#sub_total').val(sum.toLocaleString('en-US'));
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
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
        }else{
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
        }

        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
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
        let grandtotal = $('#sub_total').val().split(",").join("");
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
        }else{
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
        }
        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
    });

    $(document).on('change', '.harga', function() {
        let id = $(this).data('id');
        let jumlah = $('#jumlah_'+id).val().split(",").join("");
        console.log($(this).val());
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
        if($('#flexCheckDefault').is(':checked') == true){
            let disc = sum + (sum * 11 / 100);
            $('#sub_total').val(parseFloat(disc).toLocaleString('en-US'));
            $('#input_total').val(parseFloat(sum).toLocaleString('en-US'));
        }else{
            $('#sub_total').val(parseFloat(sum).toLocaleString('en-US'));
        }
        let harga_dp = $('#input_dp').val().split(",").join("");
        let subtotal = $('#sub_total').val().split(",").join("");
        let uangmuka = subtotal - harga_dp;
        $('#input_sisa').val(uangmuka.toLocaleString('en-US'));
    });

});
</script>
@endsection
@endsection
