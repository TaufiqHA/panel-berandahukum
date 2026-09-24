@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Pencarian</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Pencarian</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Pencarian</h2>
    <p class="section-lead">
        Berikut adalah data pencarian melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Pencarian</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
                        <div class="form-row">
                          <div class="form-group col-md-8">
                            <label for="date">Serial Number</label>
                            <select type="text" class="form-control" id="serial_number" placeholder="Pencarian" name="serial_number" required multiple></select>
                            <div class="invalid-feedback feedback-serial_number">
                                Serial Number harus diisi.
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="inputPassword4"></label>
                            <input type="button" class="form-control btn btn-warning mt-1 filter" value="Filter">
                          </div>
                        </div>
                    </form>
                    <form  novalidate="">
                        <div class="form-row">
                          <div class="form-group col-md-8">
                            <label for="nama_product">Nama Barang</label>
                            <select type="text" class="form-control" id="nama_product" placeholder="Pencarian Nama Barang" name="nama_product" required multiple></select>
                            <div class="invalid-feedback feedback-nama_barang">
                                Nama Barang harus diisi.
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="inputNamaBarang"></label>
                            <input type="button" class="form-control btn btn-warning mt-2 filter2" id="search_nama" value="Filter">
                          </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="show">
      
    </div>
    <br>
    <form id="report-form" method="post" action="{{ url('search/download-search-nama') }}">
        @csrf
     <input type="hidden" name="search_nama_id" id="search_nama_id" value="" />   
     <div class="card-footer bg-whitesmoke text-md-right">
          <button class="btn btn-primary" id="download-btn">Download</button>
        </div>
    </form>    
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
	$('#download-btn').hide();
	
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });

    $('#serial_number').select2({
        allowClear:true,
        placeholder:'Pilih Serial Number',
        ajax: {
            url: '{{ url("search/select") }}',
            dataType: 'json',
            cache: true,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
        },
    });

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    function tanggalan (date){
        var d=new Date(date);
        var dd=d.getDate();
        var mm= monthNames[d.getMonth()];
        var yy=d.getFullYear();
        return dd+" "+mm+" "+yy;
    }

    $('.filter').on('click', function(e) {
        e.stopPropagation();
        $('#download-btn').hide();
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            let serial_number = $('#serial_number').val();
            $.ajax({
                url: "{{ url('search') }}",
                type: "post",
                data: JSON.stringify(serial_number),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    console.log(response);
                    let append = '';
                    $.each(response, function(index,value) {
                        console.log(value);
                        var nama_barang = '-';
                        if(value.barang != null){
                            nama_barang = value.barang.nama_product;
                        }
                        var nama_supplier = '-';
                        if(value.detail_barang_masuk != null){
							if(value.detail_barang_masuk.stock_in.po_id != 0 && value.detail_barang_masuk.stock_in.po_id != null){
								nama_supplier = value.detail_barang_masuk.stock_in.po.nama_supplier;
							}
						}
                        if(value.barang != null){
                            nama_barang = value.barang.nama_product;
                        }
                        if(value.detail_barang_masuk != null){
							var tanggal_masuk= tanggalan(value.detail_barang_masuk.stock_in.tanggal_masuk);
						}
                        let no_po = '-'
                        if(value.detail_barang_masuk != null){
							if(value.detail_barang_masuk.stock_in.po != null){
								no_po = value.detail_barang_masuk.stock_in.po.kode_po;
							}
						}
                        let detail_penjualan = 'Stock';
                        if(value.detail_penjualan != null){
                            detail_penjualan = 'Terjual';
                        }
                        let nama_pembeli = '';
                        if(value.detail_penjualan != null){
                            var tanggal_terjual = tanggalan(value.detail_penjualan.penjualan.date)
                            nama_pembeli = '<li class="list-group-item">Nama Pembeli: '+value.detail_penjualan.penjualan.nama_pembeli+'</li>'+
                                '<li class="list-group-item">Alamat Pembeli: '+value.detail_penjualan.penjualan.alamat_pembeli+'</li>'+
                                '<li class="list-group-item">Tanggal Terjual: '+tanggal_terjual+'</li>'+
                                '<li class="list-group-item">Kode Penjualan: '+value.detail_penjualan.penjualan.kode_penjualan+'</li>';
                        }
                        append += '<div class="col-12 col-md-12 col-lg-12">'+
                            '<div class="card-group">'+
                              '<div class="card">'+
                                '<div class="card-body">'+
                                  '<h5 class="card-title">Detail Barang</h5>'+
                                  '<ul class="list-group list-group-flush">'+
                                    '<li class="list-group-item">Nama Barang: '+nama_barang+'</li>'+
                                    '<li class="list-group-item">Serial Number: '+value.serial_number+'</li>'+
                                    '<li class="list-group-item">Nama Supplier: '+nama_supplier+'</li>'+
                                    '<li class="list-group-item">Tanggal Masuk: '+tanggal_masuk+'</li>'+
                                    '<li class="list-group-item">Nomor Po : '+no_po+'</li>'+
                                  '</ul>'+
                                '</div>'+
                              '</div>'+
                              '<div class="card">'+
                                '<div class="card-body">'+
                                  '<h5 class="card-title">Status Barang</h5>'+
                                  '<ul class="list-group list-group-flush">'+
                                    '<li class="list-group-item">Status Barang: '+detail_penjualan+'</li>'+
                                    '<li class="list-group-item">Nama Toko: '+value.toko.nama_toko+'</li>'+
                                    nama_pembeli+
                                  '</ul>'+
                                '</div>'+
                              '</div>'+
                            '</div>'+
                          '</div>';
                    });
                    $('#show').html(append);
                }
            });
        }
    });
    
    //tambahan search nama barang
    $('#nama_product').select2({
        allowClear:true,
        placeholder:'Pilih nama barang',
        ajax: {
            url: '{{ url("search/selectnama") }}',
            dataType: 'json',
            cache: true,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
        },
    });
    
    
    $('#search_nama').on('click', function(e) {
		$('#download-btn').hide();
        e.stopPropagation();
        //$('.needs-validation').addClass('was-validated');
         let nama_product = $('#nama_product').val();
         $('#search_nama_id').val(nama_product);
        if(nama_product.length > 0){
			
            $('.feedback-nama_barang').hide();
            
            $.ajax({
                url: "{{ url('search/searchnama') }}",
                type: "post",
                data: JSON.stringify(nama_product),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
					console.log(response);
                    let table_result = '';
                    table_result += '<div class="col-12 col-md-12 col-lg-12 table-responsive">'+
						'<table class="table table-striped" class="col-12 col-md-12 col-lg-12">'+
							'<thead><tr>'+
								'<th>ID</th>'+
								'<th>No PO</th>'+
								'<th>Nama Barang</th>'+
								'<th>Warna</th>'+
								'<th>Jumlah</th>'+
								'<th>Serial Number</th>'+
								'<th>Tgl Masuk</th>'+
                                '<th>Status</th>'+
								'<th>Harga Beli</th>'+
								'<th>Harga Jual</th>'+
								'<th>Nama Toko</th>'+
								'<th>Tgl Barang Keluar</th>'+
                                '<th>Tgl Barang Terjual</th>'+
								'<th>Harga Terjual</th>'+
								'<th>Nama Pembeli</th>'+
							'</tr></thead>'+
							'<tbody>';
					 $('#show').html('');
					if(response.data.length > 0){
					$('#download-btn').show();
                    $.each(response.data, function(index,value) {
							var id = '-';
                        if(value.id != null){
                            id = value.id;
                        }
                        var no_po = '-';
                         if(value.kode_po != null){
                            no_po = value.kode_po;
                        }
                        var nama_barang = '-';
                        if(value.nama_product != null){
                            nama_barang = value.nama_product;
                        }
                        var warna = '-';
                        if(value.warna != null){
                            warna = value.warna;
                        }
                        var jumlah = '1';
                        
                        var serial_number = '-';
                        if(value.serial_number != null){
                            serial_number = value.serial_number;
                        }
                        var tanggal_masuk = '';
                        if(value.tanggal_masuk!= null){
                            tanggal_masuk = value.tanggal_masuk;
                        }
                        var harga_beli = '';
                        if(value.harga_beli!= null){
                            harga_beli = value.harga_beli;
                        }
                        var harga_jual = '';
                        if(value.harga_jual!= null){
                            harga_jual = value.harga_jual;
                        }
                        var toko = '';
                        if(value.toko!= null){
                            toko = value.toko;
                        }
                        var tgl_keluar = '';
                        if(value.tgl_keluar!= null){
                            tgl_keluar = value.tgl_keluar;
                        }
                        var hrg_terjual = '';
                        if(value.hrg_terjual!= null){
                            hrg_terjual = value.hrg_terjual;
                        }
                        var status = '';
                        if(value.status != null){
							status = value.status;
						}
                        var date_jual = '';
                        if(value.date_jual != null){
							date_jual = value.date_jual;
						}
                        
                        var nama_pembeli = '-';
                        if(value.nama_pembeli != null){
							nama_pembeli = value.nama_pembeli;
						}
						table_result += '<tr>'+
								'<td>' + id  + '</td>'+
								'<td>' + no_po + '</td>'+
								'<td>' + nama_barang + '</td>'+
								'<td>' + warna + '</td>'+
								'<td>' + jumlah + '</td>'+
								'<td>' + serial_number + '</td>'+
								'<td>' +  tanggal_masuk + '</td>'+
                                '<td>' + status + '</td>'+
								'<td>' + harga_beli + '</td>'+
								'<td>' + harga_jual + '</td>'+
								'<td>' + toko + '</td>'+
								'<td>' + tgl_keluar + '</td>'+
                                '<td>' + date_jual + '</td>'+
								'<td>' + hrg_terjual + '</td>'+
								'<td>' + nama_pembeli + '</td>'+
							'</tr>';
			             
                    });
                    table_result += '<tr>'+
								'<td colspan="4" style="text-align:center;">JUMLAH</td>'+
								'<td>' + response.summary.total_jumlah + '</td>'+
								'<td>&nbsp;</td>'+
								'<td>&nbsp;</td>'+
                                '<td>&nbsp;</td>'+
								'<td>' + response.summary.total_harga_beli + '</td>'+
								'<td>' + response.summary.total_harga_jual + '</td>'+
                                '<td>&nbsp;</td>'+
								'<td>&nbsp;</td>'+
                                '<td>&nbsp;</td>'+
								'<td>' + response.summary.total_harga_terjual + '</td>'+
								'<td>&nbsp;</td>'+
							'</tr>';
							
                    table_result += '</tbody>'+
								'</table>'+
								'</div>';
								
					}else
					{
						table_result += '</tbody>'+
								'</table>'+
								'</div>';
						$('#download-btn').hide();
						 
					}
                    $('#show').html(table_result);
					
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log(jqXHR);
                }
            });
        }else{
			$('.feedback-nama_barang').show();
		}
    });

});
</script>
@endsection
@endsection
