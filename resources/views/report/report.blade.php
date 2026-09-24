@extends('layouts.app')
@section('content')
<div class="section-header">
  <div class="section-header-back">
    <a href="features-settings.html" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
  </div>
  <h1>Report</h1>
  <div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="#">Report</a></div>
  </div>
</div>

<div class="section-body">
  <h2 class="section-title">All About Report</h2>
  <p class="section-lead">
    You can adjust all Report here
  </p>

  <div id="output-status"></div>
  <div class="row">
    @include('layouts.report')
    <div class="col-md-8">
      <form id="report-form" method="post" action="{{ url('report/download-barang-masuk') }}">
        @csrf
        <div class="card" id="settings-card">
          <div class="card-header">
            <h4>Laporan Barang Masuk</h4>
          </div>
          <div class="card-body">
			<div class="form-group row align-items-center">
			  <label for="site-description" class="form-control-label col-sm-3 text-md-right">Barang</label>
			  <div class="col-sm-6 col-md-9">
				<select class="form-control" name="nama_barang[]" id="nama_barang" multiple=""></select>
			  </div>
			</div>  
			<div class="form-group row align-items-center">
              <label for="tanggalAwal" class="form-control-label col-sm-3 text-md-right">Tanggal Awal</label>
              <div class="col-sm-6 col-md-9">
                <input type="text" name="tanggalAwal" class="form-control datepicker" id="tanggalAwal">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label for="tanggalAkhir" class="form-control-label col-sm-3 text-md-right">Tanggal Akhir</label>
              <div class="col-sm-6 col-md-9">
                <input type="text" name="tanggalAkhir" class="form-control datepicker" id="tanggalAkhir">
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label for="site-description" class="form-control-label col-sm-3 text-md-right">Toko</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="nama_toko[]" id="nama_toko" multiple=""></select>
              </div>
            </div>
          </div>
          <div class="card-footer bg-whitesmoke text-md-right">
            <button class="btn btn-primary" id="save-btn" type="button">Submit</button>
          </div>
        </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>Data Barang Masuk</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-md">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Tanggal Masuk</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Price List</th>
                  <th>Made In</th>
                  <th>Supplier</th>
                  <th>Toko</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody id="tbody">
                <tr>
                  <td align="center" colspan="12">Data Not Available</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-whitesmoke text-md-right">
          <button class="btn btn-primary" id="download-btn">Download</button>
        </div>
        </form>
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
    $('#report').select2();
    @if (Auth::user()->status == 2)
    $('#nama_toko').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
    @endif
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
    
    $('#nama_barang').select2({
		  placeholder:'Pilih Barang',
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

    $(document).on('click', '#save-btn', function(e) {
      e.stopPropagation();
      Swal.fire({
          title: 'Checking...',
          text: "Please wait",
          imageUrl: "{{ asset('waiting.gif') }}",
          showConfirmButton: false,
          allowOutsideClick: false
      });
      let data = {}
      data.nama_barang = $('#nama_barang').val();
      data.tanggalAwal = $('#tanggalAwal').val();
      data.tanggalAkhir = $('#tanggalAkhir').val();
      data.nama_toko = $('#nama_toko').val();      
      $.ajax({
          url: "{{ url('report/barang-masuk') }}",
          type: "post",
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(response){
            let val = '';
            let no = 1;
            if(response.length > 0){
              $.each(response, function(key, value) {
                if(value.keterangan == null){
                  value.keterangan = '';
                }
                val += '<tr>'+
                      '<td>'+no+'</td>'+
                      '<td>'+value.nama_product+'</td>'+
                      '<td>'+value.jumlah+'</td>'+
                      '<td>'+value.tanggal_masuk+'</td>'+
                      '<td>'+value.harga_beli+'</td>'+
                      '<td>'+value.harga_jual+'</td>'+
                      '<td>'+value.price_list+'</td>'+
                      '<td>'+value.made_in+'</td>'+
                      '<td>'+value.supplier+'</td>'+
                      '<td>'+value.toko+'</td>'+
                      '<td>'+value.keterangan+'</td>'+
                     '</tr>';
                no++;
                });
                
            }else{
              val = '<tr><td align="center" colspan="12">Data Not Available</td></tr>';
            }
            $('#tbody').html(val);
            Swal.close();
          },
          error: function(errors){
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: errors.responseJSON.message,
              })
          }
      }); 
    });
  });
</script>
@endsection
