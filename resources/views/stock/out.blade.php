@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Barang</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Barang</a></div>
    </div>
</div>

<div class="section-body">
    <a class="btn btn-primary float-right" href="{{ url('pindah-toko/out/create') }}"><i
            class="fas fa-plus"></i>
        Tambah Barang</a>
    <h2 class="section-title">Barang</h2>
    <p class="section-lead">
        Berikut adalah data barang melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Barang</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Nama Kategori</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Satuan</th>
                                <th>Warna</th>
                                <th>Berat</th>
                                <th>Ukuran</th>
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
                <h5 class="modal-title" id="create-modalLabel">Tambah Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate="">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama Kategori</label>
                            <select class="kategori_barang form-control" name="kategori_barang" id="kategori_barang"></select>
                            <div class="invalid-feedback">
                                Nama kategori harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" required="" id="nama_barang" name="nama_barang">
                            <div class="invalid-feedback">
                                Nama barang harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Merk</label>
                            <input type="text" class="form-control" required="" id="merk" name="merk">
                            <div class="invalid-feedback">
                                Merk harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Satuan</label>
                            <input type="text" class="form-control" required="" id="satuan" name="satuan">
                            <div class="invalid-feedback">
                                Satuan harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Warna</label>
                            <input type="text" class="form-control" required="" id="warna" name="warna">
                            <div class="invalid-feedback">
                                Warna harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Berat</label>
                            <input type="text" class="form-control" required="" id="berat" name="berat">
                            <div class="invalid-feedback">
                                Berat harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Ukuran</label>
                            <input type="text" class="form-control" required="" id="ukuran" name="ukuran">
                            <div class="invalid-feedback">
                                Ukuran harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Wajib Serial Number</label>
                            <select name="wajib_serial_number" id="wajib_serial_number" class="form-control">
                                <option value="1">Iya</option>
                                <option value="0">Tidak</option>
                            </select>
                            <div class="invalid-feedback">
                                Wajib Serial Number harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"
                            style="height: 100px;"></textarea>
                        <div class="invalid-feedback">
                            Merk harus diisi.
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
                <h5 class="modal-title" id="edit-modalLabel">Edit Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit" novalidate="">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama Kategori</label>
                            <select class="kategori_barang form-control" name="kategori_barang_edit" id="kategori_barang_edit"></select>
                            <div class="invalid-feedback">
                                Nama kategori harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" required="" id="nama_barang_edit" name="nama_barang_edit">
                            <div class="invalid-feedback">
                                Nama barang harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Merk</label>
                            <input type="text" class="form-control" required="" id="merk_edit" name="merk_edit">
                            <div class="invalid-feedback">
                                Merk harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Satuan</label>
                            <input type="text" class="form-control" required="" id="satuan_edit" name="satuan_edit">
                            <div class="invalid-feedback">
                                Satuan harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Warna</label>
                            <input type="text" class="form-control" required="" id="warna_edit" name="warna_edit">
                            <div class="invalid-feedback">
                                Warna harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Berat</label>
                            <input type="text" class="form-control" required="" id="berat_edit" name="berat_edit">
                            <div class="invalid-feedback">
                                Berat harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Ukuran</label>
                            <input type="text" class="form-control" required="" id="ukuran_edit" name="ukuran_edit">
                            <div class="invalid-feedback">
                                Ukuran harus diisi.
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Wajib Serial Number</label>
                            <select name="wajib_serial_number_edit" id="wajib_serial_number_edit" class="form-control">
                                <option value="1">Iya</option>
                                <option value="0">Tidak</option>
                            </select>
                            <div class="invalid-feedback">
                                Wajib Serial Number harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan_edit" id="keterangan_edit" class="form-control"
                            style="height: 100px;"></textarea>
                        <div class="invalid-feedback">
                            Merk harus diisi.
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
        $('.kategori_barang').select2({
            placeholder:'Pilih Kategori',
            ajax: {
                url: '{{ url("kategori/select") }}',
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
        $('#wajib_serial_number').select2({
            placeholder:'Pilih',
        });
        $('.select2-selection__rendered').css("line-height","42px");
        $('.select2-selection__placeholder').css("line-height","42px");
    });
    

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('barang/data') }}",
        columns: [
            { data: 'id', name: 'id'},
            { data: 'kategori.nama_kategori', name: 'kategori.nama_kategori'},
            { data: 'nama_product', name: 'nama_product'},
            { data: 'merk', name: 'merk'},
            { data: 'satuan', name: 'satuan'},
            { data: 'warna', name: 'warna'},
            { data: 'berat', name: 'berat'},
            { data: 'ukuran', name: 'ukuran'},
            { data: 'wajib_serial_number', name: 'wajib_serial_number'},
            { data: 'keterangan', name: 'keterangan'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
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
                url: "{{ url('barang') }}",
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
                    });
                }
            });
        }
    });

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $.ajax({
            url: "{{url('barang/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $('#kategori_barang_edit').empty().append('<option selected value="'+response.kategori_id+'">'+response.kategori.nama_kategori+'</option>');
                $('#nama_barang_edit').val(response.nama_product);
                $('#merk_edit').val(response.merk);
                $('#satuan_edit').val(response.satuan);
                $('#warna_edit').val(response.warna);
                $('#berat_edit').val(response.berat);
                $('#ukuran_edit').val(response.ukuran);
                $('#keterangan_edit').val(response.keterangan);
                $('#wajib_serial_number_edit').val(response.wajib_serial_number);
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
                url: "{{ url('barang/delete') }}",
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
            let data = {}
            data.id = $(this).data('id');
            $('.needs-validation-edit').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $.ajax({
                url: "{{ url('barang/update') }}",
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