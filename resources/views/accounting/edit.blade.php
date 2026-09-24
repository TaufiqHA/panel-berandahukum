@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Setting Accounting</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Setting Accounting</a></div>
    </div>
</div>

<div class="section-body">
    <h2 class="section-title">Update </h2>
    {{--<p class="section-lead">
        Berikut adalah data edit penjualan toko melindastore</a>.
    </p>--}}
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Update Accounting</h4>
                </div>
                <div class="card-body">
                    <form class="needs-validation" novalidate="">
						<div class="form-row">
							<div class="form-group col-md-6">
                                <label>Keterangan Cara Pembayaran</label>
                                <textarea class="form-control" name="cara_pembayaran" id="cara_pembayaran"
                                    style="height: 200px;">{{ $setting->cara_pembayaran }}</textarea>
                                <div class="invalid-feedback feedback-cara_pembayaran">
                                    Keterangan harus diisi.
                                </div>
                            </div>
                            <input type="hidden" name="id_set"  id="id_set" value="{{ $setting->id }}" />
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 text-right">
                    <button class="btn btn-primary save" data-button="save" type="button" id="save">Simpan</button>
				   </div>
                        </div>
                    </form>
                </div>
                 <div class="card-footer ">
                    <!-- <button class="btn btn-warning save" data-button="draft" type="button" id="draft">Save as
                        Draft</button> -->
                   
                </div>
            </div>
            
           
        </div>
    </div>
</div>
@section('modal')
<!-- Modal -->
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
    $(document).on('click','.save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
    
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Saviing...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {};
            let cara_pembayaran = $('#cara_pembayaran').val();
            if(cara_pembayaran.length == 0){
                return Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Informasi Cara Pembayaran harus di isi!',
                })
            }else{
				
                let id = $('#id_set').val();
                data.cara_pembayaran = cara_pembayaran;
				$.ajax({
                    url: "{{ url('setting/update') }}/"+ id,
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Data has been updated.',
                        }).then((result) => {
                            //window.location = '/setting';
                        });
                    },
                    error: function(errors){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errors.responseJSON.message,
                        })
                    }
                });
            }
        }
    });
});
</script>
@endsection
@endsection
