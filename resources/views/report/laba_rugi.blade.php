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
      <form id="report-form" method="post" action="{{ url('report/download-laba-rugi') }}">
          @csrf
        <div class="card" id="settings-card">
          <div class="card-header">
            <h4>Laporan Laba Rugi</h4>
          </div>
          <div class="card-body">
			<div class="form-group row align-items-center">
              <label for="status_terima" class="form-control-label col-sm-3 text-md-right">Berdasarkan</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="jenis_report" id="jenis_report" onchange="setJenis();">
				<option value="1">Barang</option>
                <option value="2">Penjualan</option>
                </select>
              </div>
            </div>  
            
            <div class="form-group row align-items-center" id="pilihan_barang">
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
            <button class="btn btn-primary" id="save-btn" type="button">Export</button>
          </div>
        </div>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>Data Laba Rugi</h4>
        </div>
        <div class="card-body p-0" id="table-barang">
          <div class="table-responsive">
            <table class="table table-striped table-md">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>No Ref</th>
                  <th>Nama Barang</th>
                  <th>Serial Number</th>
                  <th>Nama Toko</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Keuntungan</th>
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
        <div class="card-body p-0" >
          <div class="table-responsive" id="table-penjualan">
            <table class="table table-striped table-md">
							  <thead>
								<tr>
									<th>No</th>
									<th>Tanggal</th>
									<th>No Ref</th>
									<th>Nama Pembeli</th>
									<th>Metode Pembayaran</th>
									<th>Toko</th>
									<th>Cara Pembayaran</th>
									<th>Total Pembayaran</th>
									<th>DP</th>
									<th>Sisa</th>
									<th>Total Hrg.Beli</th>
									<th>Keuntungan</th>
									<th>Status</th>
									<th>Nama Project</th>
							  </thead>
							  <tbody id="tbody-penjualan">
								<tr>
								  <td align="center" colspan="14">Data Not Available</td>
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
	function setJenis(){
		if($('#jenis_report').val()== '2'){
			$('#pilihan_barang').hide();
			$('#table-penjualan').show();
			$('#table-barang').hide();
		}else{
			$('#pilihan_barang').show();
			$('#table-penjualan').hide();
		    $('#table-barang').show();
		}
	}
  $(document).ready(function() {
	$('#table-penjualan').hide();
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
    @if (Auth::user()->status == 2)
    $('#nama_toko').empty().append('<option selected value="{{ Auth::user()->toko_id }}">{{ $nama_toko }}</option>');
    $('#nama_toko').prop('disabled', true);
    @endif
    $('#nama_toko').select2({
      placeholder:'Pilih Toko',
      multiple:true,
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
      data.tanggalAwal = $('#tanggalAwal').val();
      data.tanggalAkhir = $('#tanggalAkhir').val();
      data.nama_toko = $('#nama_toko').val();
      data.jenis_report = $('#jenis_report').val();
      if(data.jenis_report == '2'){
		  data.nama_barang = null;
	  }else{
		  data.nama_barang = $('#nama_barang').val();
	  }
      
      $.ajax({
          url: "{{ url('report/get-laba-rugi') }}",
          type: "post",
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(response){
            let val = '';
            let no = 1;
            if(response.length > 0){
              let sum_harga_beli = 0;
              let sum_harga_jual = 0;
              let sum_total_pembayaran = 0;
              let sum_keuntungan = 0;
				  if(data.jenis_report == '2'){
				  //jika berdasarkan penjualan
				  $.each(response, function(key, value) {
					val += '<tr>'+
						  '<td>'+no+'</td>'+
						  '<td>'+value.tanggal+'</td>'+
						  '<td>'+value.kode_penjualan+'</td>'+
						  '<td>'+value.nama_pembeli+'</td>'+
						  '<td>'+value.metode_pembayaran+'</td>'+
						  '<td>'+value.nama_toko+'</td>'+
						  '<td>'+value.payment_status+'</td>'+
						  '<td>'+value.total_pembayaran+'</td>'+
						  '<td>'+value.dp_payment+'</td>'+
						  '<td>'+value.sisa+'</td>'+
						  '<td>'+value.harga_beli+'</td>'+
						  '<td>'+value.keuntungan+'</td>'+
						  '<td>'+value.status_name+'</td>'+
						  '<td>'+value.nama_project+'</td>'+
						 '</tr>';
					if(value.harga_beli == ''){
					  value.harga_beli = 0;
					}
					if(value.total_pembayaran == ''){
					  value.total_pembayaran = 0;
					}
					if(value.keuntungan == ''){
					  value.keuntungan = 0;
					}
					sum_harga_beli += parseInt(value.harga_beli);
					sum_total_pembayaran += parseInt(value.total_pembayaran);
					sum_keuntungan += parseInt(value.keuntungan);
					no++;
					});
					val += '<tr>'+
						  '<td colspan="7" align="right" style="font-weight:bold;font-size:16px;">Total</td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_total_pembayaran.toLocaleString('en-US')+'</td>'+
						  '<td colspan="2" align="right" style="font-weight:bold;font-size:16px;"></td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_harga_beli.toLocaleString('en-US')+'</td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_keuntungan.toLocaleString('en-US')+'</td>'+
						  '<td style="font-weight:bold;font-size:16px;"></td>'+
						 '</tr>';
						 
						 $('#tbody-penjualan').html(val);
						
				  //---------------------------------------------------
				  }else{	  
				//berdasarkan barang
				  $.each(response, function(key, value) {
					  
					if(value.telepon_pembeli == null){
					  value.telepon_pembeli = '';
					}
					val += '<tr>'+
						  '<td>'+no+'</td>'+
						  '<td>'+value.tanggal+'</td>'+
						  '<td>'+value.no_ref+'</td>'+
						  '<td>'+value.nama_product+'</td>'+
						  '<td>'+value.serial_number+'</td>'+
						  '<td>'+value.nama_toko+'</td>'+
						  '<td>'+value.harga_beli.toLocaleString('en-US')+'</td>'+
						  '<td>'+value.harga_jual.toLocaleString('en-US')+'</td>'+
						  '<td>'+value.keuntungan.toLocaleString('en-US')+'</td>'+
						 '</tr>';
					if(value.harga_beli == ''){
					  value.harga_beli = 0;
					}
					if(value.harga_jual == ''){
					  value.harga_jual = 0;
					}
					if(value.keuntungan == ''){
					  value.keuntungan = 0;
					}
					sum_harga_beli += parseInt(value.harga_beli);
					sum_harga_jual += parseInt(value.harga_jual);
					sum_keuntungan += parseInt(value.keuntungan);
					no++;
					});
					val += '<tr>'+
						  '<td colspan="6" align="right" style="font-weight:bold;font-size:16px;">Total</td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_harga_beli.toLocaleString('en-US')+'</td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_harga_jual.toLocaleString('en-US')+'</td>'+
						  '<td style="font-weight:bold;font-size:16px;">'+sum_keuntungan.toLocaleString('en-US')+'</td>'+
						 '</tr>';
						 $('#tbody').html(val);
						
				  }
            }else{
              
              if(data.jenis_report == '2'){
				  val = '<tr><td align="center" colspan="14">Data Not Available</td></tr>';
				   $('#tbody-penjualan').html(val);
					
			  }else{
					val = '<tr><td align="center" colspan="12">Data Not Available</td></tr>';
					$('#tbody').html(val);
				  
				}
            }
            
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
