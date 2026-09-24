@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Users</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Users</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah User</button>
    <h2 class="section-title">Users</h2>
    <p class="section-lead">
        Berikut adalah data users melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Nama Toko</th>
                                <th>User Menu</th>
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
                <h5 class="modal-title" id="create-modalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate="">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama User</label>
                            <input type="text" autocomplete="off" class="form-control" required="" id="name" name="name" placeholder="Nama User">
                            <div class="invalid-feedback">
                                Nama user harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" required="" id="email" name="email" placeholder="Email">
                            <div class="invalid-feedback">
                                Email harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="password" autocomplete="off" class="form-control" required="" id="password" name="password" placeholder="Password">
                            <div class="invalid-feedback">
                                Password harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Konfirmasi Password</label>
                            <input type="password" autocomplete="off" class="form-control" required="" id="password_confirmation" name="password_confirmation" placeholder="Password Confirmation">
                            <div class="invalid-feedback">
                                Password Confirmation harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" id="div_status">
                            <label>Status</label>
                            <select class="status form-control" name="status" id="status" required="">
                                <option value="2">Admin</option>
                                <option value="3">Admin Pusat</option>
                                <option value="1">Superadmin</option>
                            </select>
                            <div class="invalid-feedback">
                                Status harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6" id="div_toko">
                            <label>Toko</label>
                                <select class="toko form-control" name="toko" id="toko"></select>
                            <div class="invalid-feedback">
                                Toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="row_menu">
                        <div class="form-group col-md-12">
                            <label>Menu</label>
                            <select multiple="true" class="menu form-control" name="menu" id="menu">
                                <option value="Barang">Barang</option>
                                <option value="Kategori Barang">Kategori Barang</option>
                                <option value="Toko">Toko</option>
                                <option value="Barang Masuk">Barang Masuk</option>
                                <option value="Stock">Stock</option>
                                <option value="Pindah Toko">Pindah Toko</option>
                                <option value="Search">Search</option>
                                <option value="Purchase Order">Purchase Order</option>
                                <option value="Penjualan">Penjualan</option>
                                <option value="Invoice">Invoice</option>
                                <option value="Quotation">Quotation</option>
                                <option value="Report">Report</option>
                                <option value="User">User</option>
                            </select>
                            <div class="invalid-feedback">
                                Menu harus diisi.
                            </div>
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
<div class="modal fade edit-modal" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Edit Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit" novalidate="">
                    <input type="hidden" name="id" id="edit_id" class="form-control">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama User</label>
                            <input type="text" autocomplete="off" class="form-control" required="" id="edit_name" name="edit_name" placeholder="Nama User">
                            <div class="invalid-feedback">
                                Nama user harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" required="" id="edit_email" name="edit_email" placeholder="Email">
                            <div class="invalid-feedback">
                                Email harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 row_second">
                            <label>Password <span style="color:red;font-size:8px;">*Tidak harus diisi</span>  </label>
                            <input type="password" autocomplete="off" class="form-control" id="edit_password" name="edit_password" placeholder="Password">
                            <div class="invalid-feedback">
                                Password harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-4 row_second" id="div_status_edit">
                            <label>Status </label>
                            <select class="status form-control" name="edit_status" id="edit_status" required="">
                                <option value="1">Super Admin</option>
                                <option value="3">Admin Pusat</option>
                                <option value="2">Admin</option>
                            </select>
                            <div class="invalid-feedback">
                                Status harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-4 row_second" id="div_toko_edit">
                            <label>Toko</label>
                                <select class="toko form-control" name="edit_toko" id="edit_toko" required=""></select>
                            <div class="invalid-feedback">
                                Toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="edit_row_menu">
                        <div class="form-group col-md-12">
                            <label>Menu</label>
                            <select multiple="true" class="menu form-control" name="edit_menu" id="edit_menu">
                                <option value="Barang">Barang</option>
                                <option value="Kategori Barang">Kategori Barang</option>
                                <option value="Toko">Toko</option>
                                <option value="Barang Masuk">Barang Masuk</option>
                                <option value="Stock">Stock</option>
                                <option value="Pindah Toko">Pindah Toko</option>
                                <option value="Search">Search</option>
                                <option value="Purchase Order">Purchase Order</option>
                                <option value="Penjualan">Penjualan</option>
                                <option value="Invoice">Invoice</option>
                                <option value="Quotation">Quotation</option>
                                <option value="Report">Report</option>
                                <option value="User">User</option>
                            </select>
                            <div class="invalid-feedback">
                                Menu harus diisi.
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });

    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $('.modal').on('shown.bs.modal', function () {
        $('.toko').select2({
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
        $('.status').select2({
            placeholder:'Pilih',
        });
        $('.menu').select2({
            placeholder:'Pilih Menu',
        });
        $('.select2-selection__rendered').css("line-height","42px");
        $('.select2-selection__placeholder').css("line-height","42px");
    });

    $('#status').change(function(){
        if($(this).val() != 2){
            $('#div_toko').hide();
            $('#div_status').removeClass('col-md-6');
            $('#div_status').addClass('col-md-12');
            $('#toko').prop('required', false);
            $('#row_menu').hide();
        }else{
            $('#div_status').removeClass('col-md-12');
            $('#div_status').addClass('col-md-6');
            $('#div_toko').show();
            $('#toko').prop('required', true);
            $('#row_menu').show();
        }
    });

    $('#edit_status').change(function(){
        if($(this).val() != 2){
            $('#div_toko_edit').hide();
            $('.row_second').removeClass('col-md-4');
            $('.row_second').addClass('col-md-6');
            $('#edit_toko').prop('required', false);
            $('#row_menu_edit').hide();
        }else{
            $('.row_second').removeClass('col-md-6');
            $('.row_second').addClass('col-md-4');
            $('#div_toko_edit').show();
            $('#edit_toko').prop('required', true);
            $('#row_menu_edit').show();
        }
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('users/show') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'status_name', name: 'status_name' },
            { data: 'nama_toko', name: 'nama_toko' },
            { data: 'user_menu', name: 'user_menu' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
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
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            $.ajax({
                url: "{{ url('users/delete') }}",
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
            if(data.password !=data. password_confirmation){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Sesuaikan Password dan Confirm Password!',
                });
            }
            $.ajax({
                url: "{{ url('users') }}",
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
                        $("#toko").val(null).trigger("change");
                    });
                }
            });
        }
    });

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $.ajax({
            url: "{{url('users/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $('#edit_id').val(response.id);
                $('#edit_name').val(response.name);
                $('#edit_email').val(response.email);
                $('#edit_password').val('');
                if (response.status_admin == 1) {
                    $('#edit_status').val(3);
                }else{
                    $('#edit_status').val(response.status);
                }
               
                if(response.status == 2){
                    console.log('a');
                    $('#div_toko_edit').show();
                    $('#edit_row_menu').show();
                    $('#edit_toko').empty().append('<option selected value="'+response.toko_id+'">'+response.toko.nama_toko+'</option>');
                    $('.row_second').removeClass('col-md-6');
                    $('.row_second').addClass('col-md-4');
                    var values=response.user_menu;
                    if(values != ''){
                        $.each(values.split(","), function(i,e){
                            console.log(e);
                            $("#edit_menu option[value='" + e + "']").prop("selected", true);
                        });
                    }
                }else{
                    console.log('b');
                    $('#div_toko_edit').hide();
                    $('#edit_row_menu').hide();
                    $('.row_second').removeClass('col-md-4');
                    $('.row_second').addClass('col-md-6');
                }
                $('#update').attr('data-id', id);
                $('.edit-modal').modal('show');
            }
        });
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
            if(data.password != data. password_confirmation){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Sesuaikan Password dan Confirm Password!',
                });
            }
            $.ajax({
                url: "{{ url('users/update') }}",
                type: "post",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    $('#edit-modal').modal('hide');
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been saved.',
                    }).then((result) => {
                        $('#datatable').DataTable().ajax.reload();
                        $('.needs-validation').find('.form-control').each(function(){
                            $("#"+$(this).attr('id')).val('');
                        });
                        $("#toko").val(null).trigger("change");
                    });
                }
            });
        }
    });
});
</script>
@endsection
@endsection