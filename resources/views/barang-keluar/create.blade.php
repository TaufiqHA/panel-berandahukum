@extends('layouts.app')
@section('content')

<div class="section-header">
    <h1>Barang Keluar</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Barang Keluar</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Barang Keluar</h2>
    <p class="section-lead">
        Berikut adalah data barang_keluar melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Barang Keluar</h4>
                </div>
                <div class="card-body">P
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
                                    value="BK - {{  sprintf('%07d', $barang_keluar === null ? "1" : $barang_keluar->id+1) }}">
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
                                <label>Nama Penerima</label>
                                <input type="text" class="form-control" required="" id="nama_penerima"
                                    name="nama_penerima">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Nama Penerima harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Alamat Penerima</label>
                                <input type="text" class="form-control" id="alamat_penerima" name="alamat_penerima">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Alamat Penerima harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Telepon Penerima</label>
                                <input type="text" class="form-control" id="telepon_penerima" name="telepon_penerima">
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Telepon Penerima harus diisi.
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
                                <textarea class="form-control" name="keterangan_pembayaran" id="keterangan_pembayaran"
                                    style="height: 150px;"></textarea>
                                <div class="invalid-feedback feedback-keterangan_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-right">
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
                '<div class="form-group col-md-4">'+
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
                '<div class="form-group col-md-4">'+
                    '<label>Pilih Serial Number</label>'+
                    '<select class="form-control serial_number" name="serial_number" id="serial_number_'+index+'" multiple></select>'+
                    '<div class="invalid-feedback feedback-serial_number_'+index+'">'+
                        'Serial Number harus dipilih.'+
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

     $(document).on("select2:select",".serial_number", function(e){
         $('.feedback-'+$(this).attr('id')).hide();
    });

    $(document).on("select2:select","#nama_toko", function(e){
        $(".nama_barang").val(null).trigger("change");
        $(".stock_toko_awal").val('');
        $(".jumlah").val('');
        $(".serial_number").val(null).trigger("change");
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
        
        let url = "{{ url('serial-number/id_toko/') }}/"+$('#nama_toko').val()+"/id_barang/"+$(this).val();
        $("#serial_number_"+$(this).data('id')).val(null).trigger("change");
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
        $('#subtotal_'+id).val('');
        $('.feedback-stock_toko_awal_'+id).hide();
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
        });
    }

    $(document).on('click','.save', function(e) {
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
                    url: "{{ url('barang-keluar') }}",
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
                            window.location = '/barang-keluar/';
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
        let sum  = 0;
        $('.subtotal').each(function() {
            sum += parseFloat($(this).val().split(",").join(""));  
        });
    });
});
</script>
@endsection
@endsection