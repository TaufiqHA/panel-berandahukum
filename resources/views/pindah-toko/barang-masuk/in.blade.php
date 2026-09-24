@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Barang Masuk</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Barang Masuk</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Barang Masuk</h2>
    <p class="section-lead">
        Berikut adalah data barang masuk melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Toko</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Tanggal</th>
                                <th>Date</th>
                                <th>Ref Number</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
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
<!-- Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modalLabel">Edit Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <div class="modal-body">
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
                                placeholder="Ref Number" readonly>
                            <div class="invalid-feedback feedback-ref_number">
                                Ref Number harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Toko Awal</label>
                            <select class="form-control" name="nama_toko_from" id="nama_toko_from" required=""></select>
                            <div class="invalid-feedback feedback-nama_toko_from">
                                Nama Toko awal harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Toko Tujuan</label>
                            <select class="form-control" name="nama_toko_to" id="nama_toko_to" required=""></select>
                            <div class="invalid-feedback feedback-nama_toko_to">
                                Nama Toko tujuan harus diisi.
                            </div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-warning float-right" id="edit-add-barang" type="button"><i class="fas fa-plus"></i>
                    Barang</button>
                <hr>
                <br>
                <div id="edit-detail-barang">
                    <form class="needs-validation-barang" novalidate="" id="form-detail-barang">
                        <div id="detail-barang">

                        </div>
                    </form>
                </div>
            </div> --}}
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Barang Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Date</label>
                                <input type="text" class="form-control datepicker" required="" id="date" name="date"
                                    placeholder="Tanggal" min="1" readonly>
                                <div class="invalid-feedback feedback-date">
                                    Tanggal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ref Number</label>
                                <input type="text" class="form-control" required="" id="ref_number" name="ref_number"
                                    placeholder="Ref Number" readonly>
                                <div class="invalid-feedback feedback-ref_number">
                                    Ref Number harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Toko Awal</label>
                                <select class="form-control" name="nama_toko_from" id="nama_toko_from" required="" disabled></select>
                                <div class="invalid-feedback feedback-nama_toko_from">
                                    Nama Toko awal harus diisi.
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Toko Tujuan</label>
                                <select class="form-control" name="nama_toko_to" id="nama_toko_to" required="" disabled></select>
                                <div class="invalid-feedback feedback-nama_toko_to">
                                    Nama Toko tujuan harus diisi.
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <br>
                    <div id="edit-detail-barang">
                        <form class="needs-validation-barang" novalidate="" id="form-detail-barang">
                            <div id="detail-barang">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="update" class="btn btn-primary">Terima</button>
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
    
     var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('pindah-toko/data-in') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'tanggal', name: 'tanggal', orderData: 2 },
            { data: 'date', name: 'date', visible:false },
            { data: 'no_ref', name: 'no_ref' },
            { data: 'toko.nama_toko', name: 'toko.nama_toko' },
            { data: 'toko_to.nama_toko', name: 'toko_to.nama_toko' },
            { data: 'status_name', name: 'status_name' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        order: [[ 2, 'desc' ]],
    });

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
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
            $.ajax({
                url: "{{ url('toko') }}",
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
                            $("#"+$(this).attr('id')).val('');
                        });
                    });
                }
            });
        }
    });

    let row_edit = 1;
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
            url: "{{ url('pindah-toko/out') }}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $('#date').val(response.date.split("-").reverse().join("-"));
                $('#ref_number').val(response.no_ref);
                $('#nama_toko_from').empty().append('<option selected value="'+response.toko.id+'">'+response.toko.nama_toko+'</option>');
                $('#nama_toko_to').empty().append('<option selected value="'+response.toko_to.id+'">'+response.toko_to.nama_toko+'</option>');
                let detail_barang = '';
                $.each(response.detail_barang_keluar, function (i,v) {
                    $('#detail-barang').html('');
                    let keterangan = v.keterangan;
                    let detail_barang_id = v.id;
                    if(keterangan == null){
                        keterangan = '';
                    }
                    detail_barang += '<div class="form-row edit-data" id="row_barang_edit_'+row_edit+'" data-id="'+row_edit+'">'+
                                        '<input type="hidden" name="id_detail_barang" id="id_detail_barang_'+row_edit+'" class="form-control id_detail_barang edit_detail_barang" value="'+detail_barang_id+'">'+
                                        '<div class="form-group col-md-4">'+
                                            '<label>Nama Barang</label>'+
                                            '<select class="form-control edit_nama_barang edit_detail_barang" data-id="'+row_edit+'" name="edit_nama_barang" id="edit_nama_barang_'+row_edit+'" required="" disabled></select>'+
                                            '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                                'Barang harus dipilih.'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="form-group col-md-1">'+
                                            '<label>Jumlah</label>'+
                                            '<input value="1" type="text" name="jumlah" id="jumlah_'+row_edit+'" class="form-control jumlah edit_detail_barang" required readonly disabled>'+
                                            '<div class="invalid-feedback feedback-jumlah_'+row_edit+'">'+
                                                'Jumlah harus diisi.'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="form-group col-md-4">'+
                                            '<label>Serial Number</label>'+
                                            '<select class="form-control edit_serial_number edit_detail_barang" name="edit_serial_number" id="edit_serial_number_'+row_edit+'" required="" disabled></select>'+
                                            '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                                'Serial Number harus dipilih.'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="form-group col-md-3">'+
                                            '<label>Keterangan</label>'+
                                            '<input type="text" name="edit_keterangan" id="edit_keterangan_'+row_edit+'" class="form-control edit_keterangan edit_detail_barang" value="'+keterangan+'" disabled>'+
                                            '<div class="invalid-feedback feedback-data_sn_'+row_edit+'">'+
                                                'Keterangan harus dipilih.'+
                                            '</div>'+
                                        '</div>'+
                                        '<script type="text/javascript">'+
                                            '$("#edit_nama_barang_'+row_edit+'").empty().append("<option selected value='+v.gudang_barang.barang.id+'>'+v.gudang_barang.barang.nama_product+'  </option>");'+
                                            '$("#edit_serial_number_'+row_edit+'").empty().append("<option selected value='+v.gudang_barang.id+'>'+v.gudang_barang.serial_number+'  </option>");'+
                                            '$(".select2-container").css("width","100%");'+
                                        '</script'+'>'+
                                    '</div>';
                    row_edit ++;
                });
                $('#detail-barang').append(detail_barang);
                swal.close();
                $('#edit-modal').modal('show');
            }
        });
        let name = $(this).data('name');
        let alamat = $(this).data('alamat');
        $('#edit_nama_toko').val(name);
        $('#edit_alamat_toko').val(alamat);
        $('#update').attr('data-id', id);
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
                url: "{{ url('toko/delete') }}",
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

    $(document).on('click','#update', function(e) {
        e.stopPropagation();
        $('.needs-validation-edit').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            data.id = $(this).data('id');
            $.ajax({
                url: "{{ url('pindah-toko/terima') }}",
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
    });
});
</script>
@endsection
@endsection