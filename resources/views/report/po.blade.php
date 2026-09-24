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
      <form id="report-form" method="post" action="{{ url('report/download-po') }}">
        @csrf
        <div class="card" id="settings-card">
          <div class="card-header">
            <h4>Laporan Purchase Order</h4>
          </div>
          <div class="card-body">
			<div class="form-group row align-items-center">
              <label for="tanggalAwal" class="form-control-label col-sm-3 text-md-right">Jatuh Tempo</label>
              <div class="col-sm-6 col-md-3">
                <input type="text" name="jatuh_tempo_awal" class="form-control datepicker" id="jatuh_tempo_awal">
              </div>
              <div class="col-sm-6 col-md-1">S/D</div>
              <div class="col-sm-6 col-md-3">
                <input type="text" name="jatuh_tempo_akhir" class="form-control datepicker" id="jatuh_tempo_akhir">
              </div>
              
            </div>
			<div class="form-group row align-items-center">
			  <label for="site-description" class="form-control-label col-sm-3 text-md-right">Supplier</label>
			  <div class="col-sm-6 col-md-9">
				<select class="form-control" name="nama_supplier[]" id="nama_supplier" multiple=""></select>
			  </div>
			</div>   
			{{--<div class="form-group row align-items-center">
			  <label for="site-description" class="form-control-label col-sm-3 text-md-right">Barang</label>
			  <div class="col-sm-6 col-md-9">
				<select class="form-control" name="nama_barang[]" id="nama_barang" multiple=""></select>
			  </div>
			</div>  --}}
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
            <div class="form-group row align-items-center">
              <label for="status_terima" class="form-control-label col-sm-3 text-md-right">Status Barang</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="status_terima" id="status_terima">
				<option value="2">-Pilih Semua-</option>	
                <option value="1">Diterima</option>
                <option value="0">Belum Diterima</option>
                </select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label for="status_bayar" class="form-control-label col-sm-3 text-md-right">Status Bayar</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="status_bayar" id="status_bayar">
				        <option value="3">-Pilih Semua-</option>	
                <option value="1">Lunas</option>
                <option value="0">Hutang</option>
                <option value="2">DP</option>
                </select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label for="status_po" class="form-control-label col-sm-3 text-md-right">Status PO</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="status_po" id="status_po">
				<option value="semua">-Pilih Semua-</option>
				<option value="1">Dikirim</option>
				<option value="2">Draft</option>
                </select>
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
          <h4>Data Purchase Order</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-md">
              <thead>
                <tr>
					<th>Id</th>
					<th>Tanggal</th>
					<th>Kode PO</th>
					<th>Nama Purchasing</th>
					<th>Nama Supplier</th>
					<th>Alamat</th>
					<th>Telepon</th>
					<th>Total Pembayaran</th>
					<th>Status PO</th>
					<th>Toko</th>
					<th>Status Barang</th>
					<th>Status Bayar</th>
					<th>Jatuh Tempo</th>
					<th>Keterangan</th>
					
                </tr>
              </thead>
              <tbody id="tbody">
                <tr>
                  <td align="center" colspan="13">Data Not Available</td>
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
      
      data.jatuh_tempo_awal = $('#jatuh_tempo_awal').val();
      data.jatuh_tempo_akhir = $('#jatuh_tempo_akhir').val();
      data.nama_supplier = $('#nama_supplier').val();
      data.tanggalAwal = $('#tanggalAwal').val();
      data.tanggalAkhir = $('#tanggalAkhir').val();
      data.nama_toko = $('#nama_toko').val();    
      data.status_terima = $('#status_terima').val();     
      data.status_bayar = $('#status_bayar').val(); 
      data.status_po = $('#status_po').val();       
      $.ajax({
          url: "{{ url('report/po') }}",
          type: "post",
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(response){
            console.log(response);
            let val = '';
            let no = 1;
            if(response.length > 0){
              $.each(response, function(key, value) {
                if(value.keterangan == null){
                  value.keterangan = '';
                }
                val += '<tr>'+
                      '<td>'+value.id+'</td>'+
                      '<td>'+value.tanggal+'</td>'+
                      '<td>'+value.kode_po+'</td>'+
                      '<td>'+value.nama_purchase+'</td>'+
                      '<td>'+value.nama_supplier+'</td>'+
                      '<td>'+value.alamat_supplier+'</td>'+
                      '<td>'+value.telepon+'</td>'+
                      '<td>'+value.sub_total+'</td>'+
                      '<td>'+value.status_name+'</td>'+
                      '<td>'+value.nama_toko+'</td>'+
                      '<td>'+value.status_terima+'</td>'+
                      '<td>'+value.status_bayar+'</td>'+
                      '<td>'+value.tanggal_jtempo+'</td>'+
                      '<td>'+value.keterangan+'</td>'+
                     '</tr>';
                no++;
                });
              
                
            }else{
              val = '<tr><td align="center" colspan="13">Data Not Available</td></tr>';
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
