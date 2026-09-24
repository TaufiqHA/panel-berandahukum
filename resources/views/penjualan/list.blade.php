@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Penjualan Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Penjualan Toko</a></div>
    </div>
</div>

<div class="section-body">
    <a class="btn btn-icon icon-left btn-primary float-right" href="{{ url('penjualan/create') }}"><i class="fas fa-plus"></i>
        Penjualan</a>
    <h2 class="section-title">Penjualan</h2>
    <p class="section-lead">
        Berikut adalah data penjualan toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Penjualan</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                          <div class="form-group {{ $user->status == 1 ? "col-md-3" : "col-md-4" }}">
                            <label for="date">Tanggal Awal</label>
                            <input type="text" class="form-control datepicker" id="date_from" placeholder="Tanggal Awal" required>
                            <div class="invalid-feedback feedback-date_from">
                                Tanggal Awal harus diisi.
                            </div>
                          </div>
                          <div class="form-group {{ $user->status == 1 ? "col-md-3" : "col-md-4" }}">
                            <label for="date">Tanggal Akhir</label>
                            <input type="text" class="form-control datepicker" id="date_to" placeholder="Tanggal Akhir" required>
                            <div class="invalid-feedback feedback-date_to">
                                Tanggal Akhir harus diisi.
                            </div>
                          </div>
                          @if($user->status == 1)
                          <div class="form-group col-md-4">
                            <label for="inputPassword4">Toko</label>
                            <select class="form-control" name="toko" id="toko" required="" multiple=""></select>
                          </div>
                          @endif
                          <div class="form-group {{ $user->status == 1 ? "col-md-2" : "col-md-4" }}">
                            <label for="inputPassword4"></label>
                            <input type="button" class="form-control btn btn-warning mt-1 filter" value="Filter">
                          </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Tanggal</th>
                                <th>Date</th>
                                <th>Kode Penjualan</th>
                                <th>Nama Pembeli</th>
                                <th>Metode Pembayaran</th>
                                <th>Toko</th>
                                <th>Cara Pembayaran</th>
                                <th>Total Pembayaran</th>
                                <th>DP</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th>Nama Project</th>
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
            <div class="modal-body">
                <form class="needs-validation" novalidate="">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Toko</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" required="" id="nama_toko" name="nama_toko" placeholder="Nama Toko">
                            <div class="invalid-feedback">
                                Nama toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat Toko</label>
                        <div class="col-sm-9">
                            <textarea style="height: 100px" name="alamat_toko" class="form-control" id="alamat_toko" required="" placeholder="Alamat Toko"></textarea>
                            <div class="invalid-feedback">
                                Alamat toko harus diisi.
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
        ajax: "{{ url('penjualan/show') }}",
        columns: [
            { data: 'id', name: 'id' },
			{ data: 'tanggal', name: 'tanggal', orderData: 2 },
            { data: 'date', name: 'date', visible:false },
            { data: 'kode_penjualan', name: 'kode_penjualan' },
            { data: 'nama_pembeli', name: 'nama_pembeli' },
            { data: 'metode_pembayaran', name: 'metode_pembayaran' },
            { data: 'toko.nama_toko', name: 'toko.nama_toko' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'total_pembayaran', name: 'total_pembayaran' },
            { data: 'dp_payment', name: 'dp_payment' },
            { data: 'sisa', name: 'sisa' },
            { data: 'status_name', name: 'status_name' },
            { data: 'nama_project', nametanggal: 'nama_project' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[ 2, 'desc' ]],
    });

    $('.datepicker').daterangepicker({
        locale: {format: 'DD-MM-YYYY'},
        singleDatePicker: true,
        autoUpdateInput: false,
    });

    $('.filter').on('click', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        let date_from = $('#date_from').val();
        let date_to = $('#date_to').val();
        if($('.invalid-feedback:visible').length == 0){
            let toko = '';
            if($('#toko').val() != undefined){
                toko = $('#toko').val();
            }
            $('.table-responsive').html('');
            let table = '<table class="table table-striped" id="datatable-new">'+
                                '<thead>'+
                                    '<th>Id</th>'+
                                    '<th>Tanggal</th>'+
                                    '<th>Date</th>'+
                                    '<th>Kode Penjualan</th>'+
                                    '<th>Nama Pembeli</th>'+
                                    '<th>Metode Pembayaran</th>'+
                                    '<th>Toko</th>'+
                                    '<th>Cara Pembayaran</th>'+
                                    '<th>Total Pembayaran</th>'+
                                    '<th>DP</th>'+
                                    '<th>Sisa</th>'+
                                    '<th>Status</th>'+
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
                ajax: "{{ url('penjualan/show') }}"+'?date_from='+date_from+'&date_to='+date_to+'&toko='+toko,
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'tanggal', name: 'tanggal', orderData: 2 },
                    { data: 'date', name: 'date', visible:false },
                    { data: 'kode_penjualan', name: 'kode_penjualan' },
                    { data: 'nama_pembeli', name: 'nama_pembeli' },
                    { data: 'metode_pembayaran', name: 'metode_pembayaran' },
                    { data: 'toko.nama_toko', name: 'toko.nama_toko' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'total_pembayaran', name: 'total_pembayaran' },
                    { data: 'dp_payment', name: 'dp_payment' },
                    { data: 'sisa', name: 'sisa' },
                    { data: 'status_name', name: 'status_name' },
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[2, 'desc']],
            });
        }
    });

    $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY'));
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
                url: "{{ url('penjualan/delete') }}",
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

    $(document).on('click','.btn-detail', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('penjualan/get') }}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                console.log(response);
                // $('#create-modal').modal('show');
            }
        });
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
    $('.select2-search__field').css('width');
});
</script>
@endsection
@endsection
