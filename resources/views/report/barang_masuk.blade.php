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
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h4>Jump To</h4>
				</div>
				<div class="card-body">
					<ul class="nav nav-pills flex-column">
						<li class="nav-item"><a href="{{ url('report/barang-masuk') }}" class="nav-link {{ Request::segment(2) === "barang-masuk" ? "active" : "" }}">Laporan Barang Masuk</a></li>
						<li class="nav-item"><a href="{{ url('report/barang-keluar') }}" class="nav-link {{ Request::segment(2) === "barang-keluar" ? "active" : "" }}">laporan Barang Keluar</a></li>
						<li class="nav-item"><a href="{{ url('report/pindah-barang') }}" class="nav-link {{ Request::segment(2) === "pindah-barang" ? "active" : "" }}">Laporan Perpindahan Barang</a></li>
						<li class="nav-item"><a href="{{ url('report/stock') }}" class="nav-link {{ Request::segment(2) === "stock" ? "active" : "" }}">Laporan Stock</a></li>
						<li class="nav-item"><a href="{{ url('report/laba-rugi') }}" class="nav-link {{ Request::segment(2) === "laba-rugi" ? "active" : "" }}">Laporan Laba Rugi</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<form id="setting-form">
				<div class="card" id="settings-card">
					<div class="card-header">
						<h4>Report</h4>
					</div>
					<div class="card-body">
						<div class="form-group row align-items-center">
							<label for="tanggalAwal" class="form-control-label col-sm-3 text-md-right">Tanggal Awal dljfsdlk</label>
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
								<select class="form-control" name="nama_toko" id="nama_toko" required=""></select>
							</div>
						</div>
					</div>
					<div class="card-footer bg-whitesmoke text-md-right">
						<button class="btn btn-primary" id="save-btn">Submit</button>
						<button class="btn btn-secondary" type="button">Reset</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
	$(document).ready(function() {
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
		
	});
</script>
@endsection
