@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Accounting</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Setting Cara Pembayaran</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah </button>
    <h2 class="section-title">Cara Pembayaran</h2>
    <p class="section-lead">
        Berikut adalah daftar cara pembayaran setiap toko </a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Cara Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Nama Toko</th>
                                <th>Cara Pembayaran</th>
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
                <h5 class="modal-title" id="create-modalLabel">Tambah Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate="">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama Toko</label>
                            <select class="kategori_barang form-control" name="nama_toko" id="nama_toko" required=""></select>
                            <div class="invalid-feedback">
                                Nama toko harus diisi.
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Cara Pembayaran</label>
                        <textarea name="cara_pembayaran" id="cara_pembayaran" class="form-control"
                            style="height: 100px;" placeholder="Masukan Keterangan Cara Pembayaran" required=""></textarea>
                        <div class="invalid-feedback">
                            Cara Pembayaran harus diisi.
                        </div>
                    </div>
                </form>
            </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Edit Cara Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit" novalidate="">
                    <input type="hidden" name="id" id="id_setting_edit" class="form-control">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama Toko</label>
                            <select class="nama_toko form-control" name="nama_toko_edit" id="nama_toko_edit"></select>
                            <div class="invalid-feedback">
                                Nama toko harus diisi.
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="cara_pembayaran_edit" id="cara_pembayaran_edit" class="form-control"
                            style="height: 100px;"></textarea>
                        <div class="invalid-feedback">
                            Cara Pembayaran harus diisi.
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $('.modal').on('shown.bs.modal', function () {
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
        $('.select2-selection__rendered').css("line-height","42px");
        $('.select2-selection__placeholder').css("line-height","42px");
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('setting/data') }}",
        columns: [
            { data: 'id', name: 'id'},
            { data: 'toko.nama_toko', name: 'toko.nama_toko'},
            { data: 'cara_pembayaran', name: 'cara_pembayaran'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ]
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
                url: "{{ url('setting') }}",
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

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $.ajax({
            url: "{{url('setting/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                if(response.toko){
                    $('#nama_toko_edit').empty().append('<option selected value="'+response.toko_id+'">'+response.toko.nama_toko+'</option>');
                }
                $('#id_setting_edit').val(response.id);
                $('#cara_pembayaran_edit').val(response.cara_pembayaran);
               
                $('#update').attr('data-id', id);
                $('#edit-modal').modal('show');
            }
        });
        
    });

    $(document).on('click','.btn-delete', function(e) {
        e.stopPropagation();
        let data = {}
        data.id = $(this).data('id');
        Swal.fire({
          title: 'Anda Yakin?',
          text: "Data ini akan dihapus!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.value) {
            $.ajax({
                url: "{{ url('setting/delete') }}",
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
            $('.needs-validation-edit').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $.ajax({
                url: "{{ url('setting/update') }}",
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
