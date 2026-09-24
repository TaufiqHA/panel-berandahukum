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
      <form id="report-form" method="post" action="{{ url('report/download-pindah-barang') }}">
        @csrf
        <div class="card" id="settings-card">
          <div class="card-header">
            <h4>Laporan Perpindahan Barang</h4>
          </div>
          <div class="card-body">
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
              <label for="site-description" class="form-control-label col-sm-3 text-md-right">Dari Toko</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="toko_from" id="toko_from"></select>
              </div>
            </div>
            <div class="form-group row align-items-center">
              <label for="site-description" class="form-control-label col-sm-3 text-md-right">Menuju Toko</label>
              <div class="col-sm-6 col-md-9">
                <select class="form-control" name="toko_to" id="toko_to"></select>
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
          <h4>Data Pindah Barang</h4>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-md">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Ref Number</th>
                  <th>Toko Awal</th>
                  <th>Toko Tujuan</th>
                  <th>Nama Barang</th>
                  <th>Serial Number</th>
                  <th>Keterangan</th>
                  <th>Status</th>
                  <th>Harga Beli</th>
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

    $('#toko_from').select2({
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

    $(document).on("select2:select","#toko_from", function(e){
      $("#toko_to").select2({
        placeholder: "Pilih Toko",
        ajax: {
          url: "{{ url('toko/except') }}/"+$(this).val(),
          dataType: "json",
          cache: true,
          data: function(params) {
            return {
              term: params.term || "",
              page: params.page || 1
            }
          },
        }
      });
    });

    $("#toko_to").select2({
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
      data.toko_from = $('#toko_from').val();
      data.toko_to = $('#toko_to').val();
      $.ajax({
          url: "{{ url('report/get-pindah-barang') }}",
          type: "post",
          data: JSON.stringify(data),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(response){
            let val = '';
            let no = 1;
            if(response.length > 0){
              let sum = 0;
              $.each(response, function(key, value) {
                if(value.keterangan == null){
                  value.keterangan = '';
                }
                val += '<tr>'+
                      '<td>'+no+'</td>'+
                      '<td>'+value.date+'</td>'+
                      '<td>'+value.no_ref+'</td>'+
                      '<td>'+value.from+'</td>'+
                      '<td>'+value.to+'</td>'+
                      '<td>'+value.nama_product+'</td>'+
                      '<td>'+value.serial_number+'</td>'+
                      '<td>'+value.keterangan+'</td>'+
                      '<td>'+value.status+'</td>'+
                      '<td>'+value.harga_beli.toLocaleString('en-US')+'</td>'+
                     '</tr>';
                if(value.harga_beli == ''){
                  value.harga_beli = 0;
                }
                sum += parseInt(value.harga_beli);
                no++;
                });
                val += '<tr>'+
                      '<td colspan="9" align="right" style="font-weight:bold;font-size:16px;">Total</td>'+
                      '<td colspan="2" style="font-weight:bold;font-size:16px;">'+sum.toLocaleString('en-US')+'</td>'+
                     '</tr>';
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