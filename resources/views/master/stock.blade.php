@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Barang</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Stock Barang</a></div>
    </div>
</div>

<div class="section-body">
<!--     <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah Stock Barang</button> -->
    <h2 class="section-title">Stock Barang</h2>
    <p class="section-lead">
        Berikut adalah data stock barang melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Stock Barang</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Satuan</th>
                                <th>Warna</th>
                                <th>Berat</th>
                                <th>Ukuran</th>
                                <th>Stock</th>
                                <th>Wajib Serial Number</th>
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
<!-- Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                  <div class="card-body">
                    <ul class="list-unstyled user-details list-unstyled-border list-unstyled-noborder">
                      
                    </ul>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sn">
                        <thead>
                            <th>Id</th>
                            <th>Serial Number</th>
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
<!-- Modal -->
<div class="modal fade" id="edit-modal-sn" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel-sn"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel-sn"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_barang" id="id_barang" class="form-control">
                <button id="button" class="btn btn-success select-multiple-row" style="margin-bottom: 10px;"><i class="fas fa-check mr-2"></i>Pilih Semua Data</button>
                <button id="button" class="btn btn-danger delete-multiple-row" style="margin-bottom: 10px;display: none;"><i class="fas fa-trash mr-2"></i>Hapus Data Yang dipilih</button>
                <div class="table-responsive" id="table-serial-number">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-modal-sn-detail" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel-sn"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel-sn">Edit Serial Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit-sn" novalidate="">
                    <div class="form-row">
                        <input type="hidden" name="serial_number_id" id="serial_number_id">
                        <div class="form-group col-md-12">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number_value" id="serial_number_value" class="form-control">
                            <div class="invalid-feedback">
                                Serial Number harus diisi.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="update" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    let user = @json($user);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('stock/data') }}",
        columns: [
            { data: 'id', name: 'id'},
            { data: 'nama_product', name: 'nama_product'},
            { data: 'merk', name: 'merk'},
            { data: 'satuan', name: 'satuan'},
            { data: 'warna', name: 'warna'},
            { data: 'berat', name: 'berat', },
            { data: 'ukuran', name: 'ukuran'},
            { data: 'total', name: 'total'},
            { data: 'wajib_serial_number', name: 'wajib_serial_number'},
            { data: 'keterangan', name: 'keterangan'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        columnDefs: [
          {
              "targets": 7,
              "className": "text-center",
         }],
    });

    $(document).on('click','.view-stock', function(e) {
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id = $(this).data('id');
        let barang = $(this).data('barang');
        let satuan = $(this).data('satuan');
        $.ajax({
            url: "{{url('stock/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                let append = '';
                if(response.length > 0){
                    $.each(response, function (i, v) {
                        append += '<li class="media">'+
                            '<div class="media-body">'+
                              '<div class="media-title">'+v.toko.nama_toko+'</div>'+
                              '<div class="text-job text-muted">'+v.toko.alamat_toko+'</div>'+
                            '</div>'+
                            '<div class="media-items">'+
                              '<div class="media-item">'+
                                '<div class="media-value">'+v.total+'</div>'+
                                '<div class="media-label">'+satuan+'</div>'+
                              '</div>'+
                            '</div>'+
                          '</li>';
                    });
                }else{
                    append += '<li class="media">'+
                                '<div class="media-body">'+
                                  '<div class="media-title">Tidak Ada Data</div>'+
                                '</div>'+
                              '</li>';
                }
                $('.list-unstyled-noborder').html(append);
                $('#create-modalLabel').html(barang);
            }
        });
        $('#create-modal').modal('show');
        Swal.close();
    });

    $(document).on('click','.view-sn', function(e) {
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id = $(this).data('id');
        let barang = $(this).data('barang');
        $('#edit-modalLabel-sn').html(barang);
        $('#table-serial-number').html('');
        $('#id_barang').val(id);
        if(user.status == 2){
            $('.select-multiple-row').hide();
            $('.delete-multiple-row').hide();
        }
        let table = '<table class="table table-striped table-sn" id="table-detail-sn-'+id+'" style="cursor:pointer;">'+
                        '<thead>'+
                            '<th>Id</th>'+
                            '<th>Serial Number</th>'+
                            '<th>Toko</th>'+
                            '<th>Action</th>'+
                        '</thead>'+
                        '<tbody>'+
                        '</tbody>'+
                    '</table>';
        $('#table-serial-number').html(table);
        var testing = $('#table-detail-sn-'+id).DataTable({
            processing: true,
            serverSide: true,
            retrieve: true,
            ajax: "{{ url('stock/sn') }}/"+id,
            columns: [
                { data: 'id', name: 'id'},
                { data: 'serial_number', name: 'serial_number'},
                { data: 'toko.nama_toko', name: 'toko.nama_toko'},
                { data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            select: {
                style: 'multi'
            }
        });
        testing.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                if ( testing.rows('.selected').data().length > 0 ) {
                    if(user.status == 1){
                        $('.delete-multiple-row').show();
                    }
                } else {
                    $('.delete-multiple-row').hide();
                }
            }
        });

        testing.on( 'deselect', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                if ( testing.rows('.selected').data().length <= 0 ) {
                    $('.delete-multiple-row').hide();
                }
            }
        });
        $('.select-multiple-row').click( function () {
            if (testing.rows('.selected').data().length == 0) {
                $('.delete-multiple-row').show();
                testing.rows(  ).select();
            } else {
                $('.delete-multiple-row').hide();
                testing.rows(  ).deselect();
            }
        });

        $('.delete-multiple-row').click(function (e) {
            e.stopPropagation();
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
                    var tables = $('#table-detail-sn-'+id).DataTable();
                    Swal.fire({
                        title: 'Memeriksa...',
                        text: "Harap menunggu",
                        imageUrl: "{{ asset('waiting.gif') }}",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    var data = $.map(tables.rows('.selected').data(), function (value) {
                        let data = {}
                        data.id = value.id;
                        $.ajax({
                            url: "{{ url('stock/sn/delete') }}",
                            type: "post",
                            data: JSON.stringify(data),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function(response){
                                
                            },error: function(errors){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errors.responseJSON.message,
                                })
                            }
                        });
                    });
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Your Data has been deleted.',
                        icon: 'success'
                    }).then((result) => {
                        $('.delete-multiple-row').hide();
                        $('#table-detail-sn-'+id).DataTable().ajax.reload();
                    });
                }
            })
        });
        $('#edit-modal-sn').modal('show');
        Swal.close();
    });

    $(document).on('click','.btn-edit-sn', function(e) {
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let id = $(this).data('id');
        let sn = $(this).data('sn');
        $('#serial_number_id').val(id);
        $('#serial_number_value').val(sn);
        $('#edit-modal-sn-detail').modal('show');
        Swal.close();
    });

    $(document).on('click','#update', function(e) {
        e.stopPropagation();
        Swal.fire({
            title: 'Checking...',
            text: "Please wait",
            imageUrl: "{{ asset('waiting.gif') }}",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        let data = {}
        let id = $('#id_barang').val();
        data.id = $('#serial_number_id').val();
        data.serial_number = $('#serial_number_value').val();
        let duplicate = {}
        $.ajax({
            url: "{{url('stock/get-sn')}}/"+id,
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async:false,
            success: function(response){
                duplicate = response;
            }
        });

        if(duplicate.serial_number != undefined){
            return Swal.fire({
                icon: 'error',
                title: 'Periksa serial number!',
                text: 'Serial Number '+duplicate.serial_number+' Duplicate',
            });
        }else{
            $.ajax({
                url: "{{ url('stock/sn/update') }}",
                type: "post",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    $('#edit-modal-sn-detail').modal('hide');
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been updated.',
                    }).then((result) => {
                        $('#table-detail-sn-'+id).DataTable().ajax.reload();
                    });
                }
            });
        }
    });

    $(document).on('click','.btn-delete-sn', function(e) {
        e.stopPropagation();
        let id = $(this).data('barang-id');
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
                url: "{{ url('stock/sn/delete') }}",
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
                        $('#table-detail-sn-'+id).DataTable().ajax.reload();
                    });
                },error: function(errors){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errors.responseJSON.message,
                    })
                }
            });
          }
      })
    });
});
</script>
@endsection
@endsection