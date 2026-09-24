@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Barang Masuk</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Barang Masuk</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah Barang Masuk</button>
    <h2 class="section-title">Barang Masuk</h2>
    <p class="section-lead">
        Berikut adalah data barang melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Barang Masuk</h4>
                </div>
                <div class="card-body">
                    <div class="form-row">
                       <div class="form-group {{ $user->status == 1 ? "col-md-3" : "col-md-3" }}">
                        <label for="date">Tanggal Awal</label>
                        <input type="text" class="form-control datepicker" id="date_from" placeholder="Tanggal Awal" required>
                        <div class="invalid-feedback feedback-date_from">
                            Tanggal Awal harus diisi.
                        </div>
                      </div>
                      <div class="form-group {{ $user->status == 1 ? "col-md-3" : "col-md-3" }}">
                        <label for="date">Tanggal Akhir</label>
                        <input type="text" class="form-control datepicker" id="date_to" placeholder="Tanggal Akhir" required>
                        <div class="invalid-feedback feedback-date_to">
                            Tanggal Akhir harus diisi.
                        </div>
                      </div>
                      @if($user->status == 1)
                      <div class="form-group col-md-3">
                        <label for="inputPassword4">Toko</label>
                        <select class="form-control" name="toko" id="toko" required="" multiple=""></select>
                      </div>
                      @endif
                      
                      <div class="form-group {{ $user->status == 1 ? "col-md-3" : "col-md-3" }}">
                        <label for="inputPassword4"></label>
                        <input type="button" class="form-control btn btn-warning mt-1 filter" value="Filter">
                      </div>
                    </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>No PO</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Tanggal Masuk</th>
                                <th>Date</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Price List</th>
                                <th>Made In</th>
                                <th>Supplier</th>
                                <th>Toko</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('modal')
<script>
    function GetPrice(){
        let nama_barang = $('#nama_barang').val();
        $.ajax({
            url: "{{ url('barang') }}/"+nama_barang,
            type: "get",
            dataType: "json",
            success: function(response){
                if (response.harga == null) response.harga = 0;
                $('#price_list').val(response.harga.toLocaleString('en-US'));
            }
        });
    }
    function GetPriceEdit(){
        let edit_nama_barang = $('#edit_nama_barang').val();
        $.ajax({
            url: "{{ url('barang') }}/"+edit_nama_barang,
            type: "get",
            dataType: "json",
            success: function(response){
                if (response.harga == null) response.harga = 0;
                $('#edit_price_list').val(response.harga.toLocaleString('en-US'));
            }
        });
    }
    
</script>
<!-- Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modalLabel">Tambah Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="wizard-steps">
                        <div class="wizard-step wizard-step-active" id="wizard-data-barang">
                            <div class="wizard-step-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="wizard-step-label">
                                Data Stock
                            </div>
                        </div>
                        <div class="wizard-step" id="wizard-detail-barang">
                            <div class="wizard-step-icon">
                                <i class="fas fa-th"></i>
                            </div>
                            <div class="wizard-step-label">
                                Detail Barang
                            </div>
                        </div>
                    </div>
                </div>
                <form class="needs-validation" novalidate="">
                    <div id="data-barang">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select class="form-control" name="nama_barang" id="nama_barang" onchange="GetPrice()" required=""></select>
                            <div class="invalid-feedback feedback-nama_barang">
                                Nama barang harus diisi.
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" required="" id="jumlah" name="jumlah"
                                    placeholder="Jumlah" min="1">
                                <div class="invalid-feedback feedback-jumlah">
                                    Jumlah barang harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Masuk</label>
                                <input type="text" class="form-control datepicker" required="" id="tanggal_masuk"
                                    name="tanggal_masuk" placeholder="Tanggal Masuk">
                                <div class="invalid-feedback feedback-tanggal_masuk">
                                    Tanggal Masuk harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Supplier</label>
                                <input type="text" class="form-control" required="" id="supplier" name="supplier"
                                    placeholder="Supplier">
                                <div class="invalid-feedback feedback-supplier">
                                    Supplier harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Harga Beli</label>
                                <input type="text" class="form-control money-format" required="" id="harga_beli"
                                    name="harga_beli" placeholder="Harga Beli">
                                <div class="invalid-feedback feedback-harga_beli">
                                    Harga Beli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Harga Jual</label>
                                <input type="text" class="form-control money-format" required="" id="harga_jual"
                                    name="harga_jual" placeholder="Harga Jual">
                                <div class="invalid-feedback feedback-harga_jual">
                                    Harga Jual harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Price List</label>
                                <input type="text" class="form-control money-format" required="" id="price_list"
                                    name="price_list" placeholder="Price List">
                                <div class="invalid-feedback feedback-price_list">
                                    Price List harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Made In</label>
                                <input type="text" class="form-control" required="" id="made_in" name="made_in"
                                    placeholder="Made In">
                                <div class="invalid-feedback feedback-made_in">
                                    Made In harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Toko</label>
                                <select class="form-control" name="nama_toko" id="nama_toko" required=""></select>
                                <div class="invalid-feedback feedback-nama_toko">
                                    Nama Toko harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Serial Number</label>
                                <select name="serial_number" id="serial_number" class="form-control">
                                    <option value="1">All</option>
                                    <option value="2">One by one</option>
                                </select>
                                <div class="invalid-feedback feedback-serial_number">
                                    Serial Number harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" style="height: 100px;"
                                placeholder="Keterangan"></textarea>
                            <div class="invalid-feedback invalid-feedback_keterangan">
                                Keterangan harus diisi.
                            </div>
                        </div>
                    </div>
                </form>
                <div id="detail-barang" style="display: none;">
                    <form class="needs-validation-serial-number" novalidate="" id="form-detail-barang">

                    </form>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" id="close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="back" style="display: none;"><i
                        class="fas fa-arrow-left"></i> Back</button>
                <button type="button" id="next" class="btn btn-primary">Next <i class="fas fa-arrow-right"></i></button>
                <button type="button" id="save" class="btn btn-primary" style="display: none;">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Edit Barang Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <input type="hidden" name="id_barang_masuk_edit" id="id_barang_masuk_edit">
                    <input type="hidden" name="wajib_serial_number_edit" id="wajib_serial_number_edit">
                    <div class="wizard-steps">
                        <div class="wizard-step wizard-step-active" id="edit-wizard-data-barang">
                            <div class="wizard-step-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="wizard-step-label">
                                Data Stock
                            </div>
                        </div>
                        <div class="wizard-step" id="edit-wizard-detail-barang">
                            <div class="wizard-step-icon">
                                <i class="fas fa-th"></i>
                            </div>
                            <div class="wizard-step-label">
                                Detail Barang
                            </div>
                        </div>
                    </div>
                </div>
                <form class="edit-needs-validation" novalidate="">
                    <div id="edit-data-barang">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select class="form-control edit_nama_barang" onchange="GetPriceEdit()"  name="edit_nama_barang" id="edit_nama_barang"
                                required=""></select>
                            <div class="invalid-feedback feedback-edit_nama_barang">
                                Nama barang harus diisi.
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" required="" id="edit_jumlah"
                                    name="edit_jumlah" placeholder="Jumlah" min="1">
                                <div class="invalid-feedback feedback-edit_jumlah">
                                    Jumlah harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal Masuk</label>
                                <input type="text" class="form-control datepicker" required="" id="edit_tanggal_masuk"
                                    name="edit_tanggal_masuk" placeholder="Tanggal Masuk">
                                <div class="invalid-feedback feedback-edit_tanggal_masuk">
                                    Tanggal Masuk harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Supplier</label>
                                <input type="text" class="form-control" required="" id="edit_supplier"
                                    name="edit_supplier" placeholder="Supplier">
                                <div class="invalid-feedback feedback-edit_supplier">
                                    Supplier harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Harga Beli</label>
                                <input type="text" class="form-control money-format" required="" id="edit_harga_beli"
                                    name="edit_harga_beli" placeholder="Harga Beli">
                                <div class="invalid-feedback feedback-edit_harga_beli">
                                    Harga Beli harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Harga Jual</label>
                                <input type="text" class="form-control money-format" required="" id="edit_harga_jual"
                                    name="edit_harga_jual" placeholder="Harga Jual">
                                <div class="invalid-feedback feedback-edit_harga_jual">
                                    Harga Jual harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Price List</label>
                                <input type="text" class="form-control money-format" required="" id="edit_price_list"
                                    name="edit_price_list" placeholder="Price List">
                                <div class="invalid-feedback feedback-edit_price_list">
                                    Price List harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Made In</label>
                                <input type="text" class="form-control" required="" id="edit_made_in"
                                    name="edit_made_in" placeholder="Made In">
                                <div class="invalid-feedback feedback-edit_made_in">
                                    Made In harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Toko</label>
                                <select class="form-control" name="edit_nama_toko" id="edit_nama_toko"
                                    required=""></select>
                                <div class="invalid-feedback feedback-edit_nama_toko">
                                    Nama Toko harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Serial Number</label>
                                <select name="edit_type_serial_number" id="edit_type_serial_number" class="form-control">
                                    <option value="1">All</option>
                                    <option value="2">One by one</option>
                                </select>
                                <div class="invalid-feedback feedback-edit_type_serial_number">
                                    Serial Number harus diisi.
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="edit_keterangan" id="edit_keterangan" class="form-control"
                                style="height: 100px;" placeholder="Keterangan"></textarea>
                            <div class="invalid-feedback_edit_keterangan">
                                Merk harus diisi.
                            </div>
                        </div>
                    </div>
                </form>
                <div id="edit-detail-barang" style="display: none;">
                    <form class="edit-needs-validation-serial-number" novalidate="" id="edit-form-detail-barang">

                    </form>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" id="edit_close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="edit_back" style="display: none;"><i
                        class="fas fa-arrow-left"></i> Back</button>
                <button type="button" id="edit_next" class="btn btn-primary">Next <i class="fas fa-arrow-right"></i></button>
                <button type="button" id="edit_save" class="btn btn-primary" style="display: none;">Update</button>
            </div>
        </div>
    </div>
</div>
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
    
    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
    });
    
    $('#toko').select2({
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
        },
        allowClear:true,
        tags:true,
    });
    
    

    // @if (Auth::user()->status == 1)
    //     $('#nama_toko').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
    //     $('#nama_toko').prop('disabled', true);
    // @endif

    function validation() {
        $('.needs-validation').find('.form-control').each(function(){
            if($(this).prop('required')){
                if($(this).val() === '' || $(this).val() === null){
                    $('#'+$(this).attr('id')).addClass('requirement-validasi');
                    $('.feedback-'+$(this).attr('id')).show();
                    $('.needs-validation').addClass('was-validated');
                }else{
                    $('.feedback-'+$(this).attr('id')).hide();
                    $('.needs-validation').addClass('was-validated');

                }   
            }
        });
    }

    function editValidation() {
        $('.edit-needs-validation').find('.form-control').each(function(){
            if($(this).prop('required')){
                if($(this).val() === '' || $(this).val() === null){
                    $('#'+$(this).attr('id')).addClass('requirement-validasi');
                    $('.feedback-'+$(this).attr('id')).show();
                    $('.edit-needs-validation').addClass('was-validated');
                }else{
                    $('.feedback-'+$(this).attr('id')).hide();
                    $('.edit-needs-validation').addClass('was-validated');

                }   
            }
        });
    }

    $(document).on('click','#next', function() {
        validation();
        if($('.invalid-feedback:visible').length == 0){
            $('#data-barang').hide();
            $('#detail-barang').show();
            $('#back').show();
            $('#save').show();
            $('#next').hide();
            $('#close').hide();
            $('#wizard-data-barang').removeClass('wizard-step-active');
            $('#wizard-detail-barang').addClass('wizard-step-active');
        }
        if($('#serial_number').val() == 1){
            $('#form-detail-barang').html(
            '<div class="form-group">'+
                '<label>Serial number <code>*pisahkan dengan koma tanpa spasi</code></label>'+
                '<textarea name="data_serial_number" id="data_serial_number" class="form-control data_sn"'+
                    'style="height: 100px;" placeholder="masukan serial number pisahkan dengan tanda koma ","" data-type="textarea"></textarea>'+
                '<div class="invalid-feedback feedback-data_serial_number">'+
                    'Serial Number harus diisi.'+
                '</div>'+
            '</div>');
        }else{
            let detail_barang = '';
            let jumlah = $('#jumlah').val();
            for (let i = 0; i < jumlah; i++) {
                detail_barang += '<div class="form-group col-md-4">'+
                                    '<label>Serial Number</label>'+
                                    '<input type="text" class="form-control data_sn" name="data_sn[]" id="data_sn_'+i+'" required data-type="input">'+
                                    '<div class="invalid-feedback feedback-data_sn_'+i+'">'+
                                        'Serial Number harus diisi.'+
                                    '</div>'+
                                '</div>';
            }
            $('#form-detail-barang').html('<div class="form-row">'+
                            detail_barang+
                        '</div>');
        }
        $.ajax({
            url: '{{ url("barang/get") }}/'+$('#nama_barang').val(),
            type: 'GET',
            success:function(result){
                if(result.wajib_serial_number == 1){
                    $('.data_sn').prop('required',true);
                }else{
                    $('.data_sn').removeAttr('required');
                }
            }
        });
    });

    $(document).on('click','#back', function() {
        $('#data-barang').show();
        $('#detail-barang').hide();
        $('#back').hide();
        $('#save').hide();
        $('#next').show();
        $('#close').show();
        $('#wizard-detail-barang').removeClass('wizard-step-active');
        $('#wizard-data-barang').addClass('wizard-step-active');
    });

    $(document).on('click','#edit_next', function() {
        editValidation();
        if($(".edit-needs-validation").find('.invalid-feedback:visible').length == 0){
            $('#edit-data-barang').hide();
            $('#edit-detail-barang').show();
            $('#edit_back').show();
            $('#edit_save').show();
            $('#edit_next').hide();
            $('#edit_close').hide();
            $('#edit-wizard-data-barang').removeClass('wizard-step-active');
            $('#edit-wizard-detail-barang').addClass('wizard-step-active');
            let detail_barang = '';
            let serial_number = $('.edit_data_sn:visible').length;
            if($('#edit_jumlah').val() < serial_number ){
                $('.append-delete').show();
                $('.new_sn').remove();
                $('.edit_data_sn').each(function(i){
                    $('#append-delete-'+i).html('<div class="input-group-append">'+
                                                '<div class="input-group-text">'+
                                                    '<button type="button" data-id="'+i+'" class="btn btn-icon btn-danger btn-delete-serial-number">'+
                                                        '<i class="fas fa-trash"></i>'+
                                                    '</button>'+
                                                '</div>'+
                                            '</div>');
                });
            }else{
                $('.append-delete').html('');
                let serial_number_real = $('.edit_data_sn').length;
                let add_more_serial_number = $('#edit_jumlah').val() - serial_number_real;
                let readonly = '';
                if($('#wajib_serial_number_edit').val() != 1){
                    readonly = 'readonly';
                }
                for (let i = 0; i < add_more_serial_number; i++) {
                    let number = serial_number_real+i 
                    detail_barang += '<div class="form-group col-md-4 new_sn">'+
                                        '<label>Serial Number</label>'+
                                        '<input  type="text" class="form-control edit_data_sn" name="edit_data_sn" id="edit_data_sn_'+number+'" data-id="">'+
                                        '<div class="invalid-feedback" id="feedback-edit_data_sn_'+number+'">'+
                                            'Serial Number harus diisi.'+
                                        '</div>'+
                                    '</div>';
                }
                //'+readonly+'
                $('#form-row-edit').append(detail_barang);
               
            }
        }

        $.ajax({
            url: '{{ url("barang/get") }}/'+$('#edit_nama_barang').val(),
            type: 'GET',
            success:function(result){
                if(result.wajib_serial_number == 1){
                    $('.edit_data_sn').prop('required',true);
                }else{
                    $('.edit_data_sn').removeAttr('required');
                }
            }
        });
    });

    $(document).on('click','#edit_back', function() {
        $('#edit-data-barang').show();
        $('#edit-detail-barang').hide();
        $('#edit_back').hide();
        $('#edit_save').hide();
        $('#edit_next').show();
        $('#edit_close').show();
        $('#edit-wizard-detail-barang').removeClass('wizard-step-active');
        $('#edit-wizard-data-barang').addClass('wizard-step-active');
    });

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation-serial-number').find('.form-control').each(function(){
            if($(this).prop('required')){
                if($(this).val() === '' || $(this).val() === null){
                    $('#'+$(this).attr('id')).addClass('requirement-validasi');
                    $('.feedback-'+$(this).attr('id')).show();
                    $('.needs-validation-serial-number').addClass('was-validated');
                }else{
                    $('.feedback-'+$(this).attr('id')).hide();
                    $('.needs-validation-serial-number').addClass('was-validated');
                }
            }
        });
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            data.product_detail = [];
            $('.needs-validation-serial-number').find('.form-control').each(function(i){
                data.product_detail[i] = this.value;
            });

            if($('.data_sn').prop('required') == true ){
                let array = [];
                if($('.data_sn').data('type') == 'textarea'){
                    array = $('.data_sn').val().split(",");
                }else{
                    array = $('.data_sn').map(function() {
                                return this.value;
                            }).get();
                }
                let duplicate = find_duplicate_in_array(array);
                if(duplicate.length > 0){
                    return Swal.fire({
                        icon: 'error',
                        title: 'Periksa serial number!',
                        text: 'Serial Number duplicate '+duplicate.toString(),
                    });
                }
                let checkArrayDuplicate = checkSnDuplicate(array, $('#nama_barang').val());
                let duplicateDb = find_duplicate_in_array(array.concat(checkArrayDuplicate));
                if(duplicateDb.length > 0){
                    return Swal.fire({
                        icon: 'error',
                        title: 'Periksa serial number!',
                        text: 'Serial Number duplicate '+duplicateDb.toString(),
                    });
                }
                if($('#jumlah').val() != array.length){
                    return Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Sesuaikan Jumlah dengan serial number!, jumlah barang adalah '+$('#jumlah').val(),
                    });
                }else{
                    if($.isNumeric(data.nama_barang) == true){
                        insert(data);
                    }else{
                        let data_barang = {}
                        data_barang.nama_barang = $('#nama_barang').val();
                        $.ajax({
                            url: "{{ url('barang') }}",
                            type: "post",
                            data: JSON.stringify(data),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function(response){
                                data.nama_barang = response.id;
                                insert(data);
                            }
                        }); 
                    }
                }
            }else{
                if($.isNumeric(data.nama_barang) == true){
                    insert(data);
                }else{
                    let data_barang = {}
                    data_barang.nama_barang = $('#nama_barang').val();
                    $.ajax({
                        url: "{{ url('barang') }}",
                        type: "post",
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(response){
                            data.nama_barang = response.id;
                            insert(data);
                        }
                    }); 
                }
            }
        }
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
    
    function insert(data) {
        $.ajax({
            url: "{{ url('stock-in') }}",
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
                    $('#datatable').DataTable().ajax.reload();
                    $('.needs-validation').find('.form-control').each(function(){
                        if($(this).attr('id') == 'nama_barang' || $(this).attr('id') == 'nama_toko' ){
                            @if (Auth::user()->status == 2)
                                $('#nama_toko').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
                                $('#nama_toko').prop('disabled', true);
                            @else
                                $("#"+$(this).attr('id')).val(null).trigger("change");
                            @endif
                        }else if($(this).attr('id') == 'serial_number'){
                            $("#"+$(this).attr('id')).val(1);
                        }else{
                            $("#"+$(this).attr('id')).val('');
                        }
                    });
                    $('#data-barang').show();
                    $('#detail-barang').hide();
                    $('#back').hide();
                    $('#save').hide();
                    $('#next').show();
                    $('#close').show();
                    $('#wizard-detail-barang').removeClass('wizard-step-active');
                    $('#wizard-data-barang').addClass('wizard-step-active');
                });
            }
        });
    }

    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $('#nama_barang, #edit_nama_barang').select2({
        placeholder:'Pilih Barang',
        tags: true,
        ajax: {
            url: '{{ url("barang/select") }}',
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
    
  

    $('#nama_toko, #edit_nama_toko').select2({
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

    $(document).on('select2:select','#nama_barang', function(e){
        if($(this).val() !== '' || $(this).val() !== null){
            $('.feedback-nama_barang').hide();
        }
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('stock-in/data') }}",
        columns: [
            { data: 'id', name: 'id'},
            { data: 'kode_po', name: 'kode_po'},
            { data: null, 
				render: function ( data, type, row ) {
				 return row.barang.nama_product + ' ' + row.barang.warna;
				} , name: 'barang.nama_product'},
            { data: 'jumlah', name: 'jumlah'},
            { data: 'tanggal', name: 'tanggal', orderData: 5},
            { data: 'tanggal_masuk', name: 'tanggal_masuk', visible: false },            
            { data: 'harga_beli', name: 'harga_beli'},
            { data: 'harga_jual', name: 'harga_jual'},
            { data: 'price_list', name: 'price_list'},
            { data: 'made_in', name: 'made_in'},
            { data: 'supplier', name: 'supplier'},
            { data: 'toko.nama_toko', name: 'toko.nama_toko'},
            { data: 'keterangan', name: 'keterangan'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        
        order: [[5, 'desc']]
    });

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id = $(this).data('id');
        $.ajax({
            url: "{{url('stock-in/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $('#id_barang_masuk_edit').val(response.id);
                $('#wajib_serial_number_edit').val(response.barang.wajib_serial_number);
                if(response.barang){
                    $('#edit_nama_barang').empty().append('<option selected value="'+response.barang_id+'">'+response.barang.nama_product + ' ' + response.barang.warna+'</option>');
                }
                $('#edit_jumlah').val(response.jumlah);
                $('#edit_tanggal_masuk').val(response.tanggal_masuk.split("-").reverse().join("-"));
                $('#edit_supplier').val(response.supplier);
                $('#edit_made_in').val(response.made_in);
                $('#edit_type_serial_number').val(response.type_serial_number);
                $('#edit_keterangan').val(response.keterangan);
                $('#edit_harga_beli').val(response.harga_beli.toLocaleString('en-US'));
                $('#edit_harga_jual').val(response.harga_jual.toLocaleString('en-US'));
                $('#edit_price_list').val(response.price_list.toLocaleString('en-US'));
                $('#edit_nama_toko').empty().append('<option selected value="'+response.toko_id+'">'+response.toko.nama_toko+'</option>');
                $('#edit_save').attr('data-id', id);
                $('#edit_save').attr('data-barang-id', response.barang_id);
                let detail_barang = '';
                $.each(response.gudang_barang, function (i,v) {
					// if(v.deleted_at == null){
                    detail_barang += '<div class="form-group col-md-4" id="form-group-id-'+i+'">'+
                                        '<label>Serial Number</label>'+
                                        '<div class="input-group">'+
                                            '<input type="text" data-id="'+v.id+'" value="'+ v.serial_number+'" class="form-control edit_data_sn" name="edit_data_sn" id="edit_data_sn_'+i+'" >'+
                                            '<div id="append-delete-'+i+'" class="append-delete">'+
                                            '</div>'+
                                            '<div class="invalid-feedback" id="feedback-edit_data_sn_'+i+'">'+
                                                'Serial Number harus diisi.'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>';
                    // }
                });
                //readonly
                $('#edit-form-detail-barang').html('<div class="form-row" id="form-row-edit">'+
                                detail_barang+
                            '</div>');
                Swal.close();
                $('#edit-modal').modal('show');
            }
        });
        
    });

    $(document).on('click','.btn-delete', function(e) {
        e.stopPropagation();
        let data = {}
        data.id = $(this).data('id');
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
                url: "{{ url('stock-in/delete') }}",
                type: "post",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been deleted.',
                    }).then((result) => {
                        $('#datatable').DataTable().ajax.reload();
                    });
                }
            });
          }
      })
    });

    $(document).on('click','#edit_save', function(e) {
        e.stopPropagation();
        $('.edit-needs-validation').addClass('was-validated');
        $('.edit-needs-validation-serial-number').find('.form-control').each(function(){
            if($(this).prop('required')){
                if($(this).val() === '' || $(this).val() === null){
                    $('#'+$(this).attr('id')).addClass('requirement-validasi');
                    $('#feedback-'+$(this).attr('id')).show();
                    $('.edit-needs-validation-serial-number').addClass('was-validated');
                }else{
                    $('.edit-needs-validation-serial-number').addClass('was-validated');
                    $('#feedback-'+$(this).attr('id')).hide();
                }
            }
        });
        
        // $('#edit_save').attr('data-id', id);
        // $('.edit_data_sn').each(function(i){
					//alert(this.value);
				//});
        /*let datasn = [];
        $("#input[name='data_sn[]']").each(function(){
			console.log($(this).attr('id'));
                datasn.push($("#"+$(this).attr('id')).val() + ' -- ' + $("#"+$(this).attr('id')).id  );
            });*/
           
        if($('#edit_jumlah').val() < $('.edit_data_sn:visible').length){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Sesuaikan Jumlah dengan serial number!',
            });
        }else{
            if($(".edit-needs-validation").find('.invalid-feedback:visible').length == 0){
                Swal.fire({
                    title: 'Checking...',
                    text: "Please wait",
                    imageUrl: "{{ asset('waiting.gif') }}",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                let data = {}
                data.id = $('#id_barang_masuk_edit').val();;
                data.datasn =  $(".edit_data_sn").map(function(){return $(this).val();}).get();
				data.datasn_id =  $(".edit_data_sn").map(function(){return $(this).attr('data-id');}).get();
                $('.edit-needs-validation').find('.form-control').each(function(){
                    data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
                });
                console.log(data.datasn);
                console.log(data.datasn_id);
                $.ajax({
                    url: "{{ url('stock-in/update') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        $('#edit-modal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Data has been updated.',
                        }).then((result) => {
                            $('#datatable').DataTable().ajax.reload();
                        });
                    }
                });
            }
        }
    });

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

    $(document).on('click','.btn-delete-serial-number', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
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
              $('#form-group-id-'+id).hide();
              $('#edit_data_sn_'+id).val('deleted');
              if($('#edit_jumlah').val() == $('.edit_data_sn:visible').length){
                $('.append-delete').hide();
              }
          }
      })
    });
    
    $('.filter').on('click', function(e) {
        e.stopPropagation();
        
        let date_from = $('#date_from').val();
        let date_to = $('#date_to').val();
        
            let toko = '';
            if($('#toko').val() != undefined){
                toko = $('#toko').val();
            }
            
            $('.table-responsive').html('');
           let table = '<table class="table table-striped" id="datatable-new">'+
                                '<thead>'+
                                    '<th>Id</th>'+
                                    '<th>No PO</th>'+
                                    '<th>Nama Barang</th>'+
                                    '<th>Jumlah</th>'+
                                    '<th>Tanggal Masuk</th>'+
                                    '<th>Harga Beli</th>'+
                                    '<th>Harga Jual</th>'+
                                    '<th>Price List</th>'+
                                    '<th>Made in</th>'+
                                    '<th>Supplier</th>'+
                                    '<th>Toko</th>'+
                                    '<th>Keterangan</th>'+
									'<th>Action</th>'+
                                '</thead>'+
                                '<tbody>'+
                                '</tbody>'+
                            '</table>';
            $('.table-responsive').html(table);
            $('#datatable-new').DataTable({
				processing: true,
				serverSide: true,
				ajax: "{{ url('stock-in/data') }}"+'?date_from='+date_from+'&date_to='+date_to+'&toko='+toko,
				columns: [
					{ data: 'id', name: 'id'},
					{ data: 'kode_po', name: 'kode_po'},
					{ data:  null, 
						render: function ( data, type, row ) {
						 return row.barang.nama_product + ' ' + row.barang.warna;
						} , name: 'barang.nama_product'},
					{ data: 'jumlah', name: 'jumlah'},
					{ data: 'tanggal', name: 'tanggal', orderData: 5},
					//{ data: 'tanggal_masuk', name: 'tanggal_masuk', visible: false },            
					{ data: 'harga_beli', name: 'harga_beli'},
					{ data: 'harga_jual', name: 'harga_jual'},
					{ data: 'price_list', name: 'price_list'},
					{ data: 'made_in', name: 'made_in'},
					{ data: 'supplier', name: 'supplier'},
					{ data: 'toko.nama_toko', name: 'toko.nama_toko'},
					{ data: 'keterangan', name: 'keterangan'},
					{data: 'action', name: 'action', orderable: false, searchable: false},
				],
				order: [[5, 'desc']]
			});
       
    })
});
</script>
@endsection
@endsection




