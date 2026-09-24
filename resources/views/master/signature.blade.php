@extends('layouts.app')
@section('css')
<style>
    .signature-wrapper {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        background-color: #f9fafb;
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }
    .signature-pad {
        width: 100%;
        height: 300px;
        border-radius: 8px;
        cursor: crosshair;
        background-color: transparent;
    }
    .preview-container {
        margin-top: 15px;
        display: none;
    }
    #preview-image {
        max-height: 150px;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
    }
</style>
@endsection
@section('content')
<div class="section-header">
    <h1>Tanda Tangan</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Tanda Tangan</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#edit-modal" id="btn-add"><i
            class="fas fa-plus"></i>
        Tambah Tanda Tangan</button>
    <h2 class="section-title">Tanda Tangan</h2>
    <p class="section-lead">
        Berikut adalah data tanda tangan sales staff melindastore.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Tanda Tangan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nama Sales</th>
                                    <th>Tanda Tangan</th>
                                    <th>Action</th>
                                </tr>
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
<!-- Modal Edit/Tambah Signature -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Update Tanda Tangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="signature-form">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label>Nama Sales</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required placeholder="Masukkan Nama Sales">
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center mb-4">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="sig_mode_modal" id="mode-draw-modal" checked> <i class="fas fa-paint-brush"></i> Gambar Langsung
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="sig_mode_modal" id="mode-upload-modal"> <i class="fas fa-upload"></i> Upload PNG/Foto
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Draw Container -->
                            <div id="draw-container-modal">
                                <div class="signature-wrapper mb-3">
                                    <canvas id="signature-pad-modal" class="signature-pad" width="600" height="300"></canvas>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-warning" id="clear-btn-modal">
                                        <i class="fas fa-eraser"></i> Bersihkan Canvas
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Container -->
                            <div id="upload-container-modal" style="display: none;">
                                <div class="form-group">
                                    <label>Upload File Tanda Tangan (PNG disarankan)</label>
                                    <input type="file" id="file-input-modal" class="form-control" accept="image/png, image/jpeg">
                                    <div class="preview-container text-center" id="preview-box-modal">
                                        <p class="mb-1 mt-3 small font-weight-bold">Preview:</p>
                                        <img src="" id="preview-image-modal" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="save-signature-modal" class="btn btn-primary">Simpan Tanda Tangan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('signature/data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { 
                data: 'signature', 
                name: 'signature',
                render: function(data) {
                    if (data) {
                        return '<img src="' + data + '" height="60" style="background:#fff; border:1px solid #ddd; padding:2px; border-radius:4px; max-width:200px;"/>';
                    }
                    return '<span class="badge badge-warning">Belum ada</span>';
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    var canvas = document.getElementById('signature-pad-modal');
    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });

    // Handle internal resize for signature pad
    function resizeCanvas() {
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }
    
    // Resize when modal shows
    $('#edit-modal').on('shown.bs.modal', function () {
        resizeCanvas();
    });

    $('input[name="sig_mode_modal"]').on('change', function() {
        if ($(this).attr('id') === 'mode-draw-modal') {
            $('#draw-container-modal').show();
            $('#upload-container-modal').hide();
        } else {
            $('#draw-container-modal').hide();
            $('#upload-container-modal').show();
        }
    });

    $('#clear-btn-modal').on('click', function() {
        signaturePad.clear();
    });

    $('#file-input-modal').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#preview-image-modal').attr('src', event.target.result);
                $('#preview-box-modal').show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#btn-add').on('click', function() {
        $('#edit-modalLabel').text('Tambah Tanda Tangan');
        $('#edit_id').val('');
        $('#edit_name').val('');
        resetModal();
    });

    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            onBeforeOpen: () => { Swal.showLoading(); }
        });

        $.get("{{ url('signature/get') }}/" + id, function(data) {
            Swal.close();
            $('#edit-modalLabel').text('Update Tanda Tangan');
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            resetModal();
            $('#edit-modal').modal('show');
        });
    });

    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data tanda tangan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
                $.post("{{ url('signature/delete') }}", {id: id}, function(data) {
                    Swal.fire('Berhasil!', 'Data tanda tangan telah dihapus.', 'success');
                    table.ajax.reload();
                });
            }
        });
    });

    function resetModal() {
        $('#mode-draw-modal').prop('checked', true).closest('label').addClass('active');
        $('#mode-upload-modal').prop('checked', false).closest('label').removeClass('active');
        $('#draw-container-modal').show();
        $('#upload-container-modal').hide();
        $('#file-input-modal').val('');
        $('#preview-box-modal').hide();
    }

    // Helper to crop transparent background
    function cropSignatureCanvas(canvas) {
        var ctx = canvas.getContext('2d');
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imageData.data;
        var minX = canvas.width, minY = canvas.height, maxX = 0, maxY = 0;
        for (var y = 0; y < canvas.height; y++) {
            for (var x = 0; x < canvas.width; x++) {
                var alpha = data[((y * canvas.width) + x) * 4 + 3];
                if (alpha > 0) {
                    if (x < minX) minX = x;
                    if (x > maxX) maxX = x;
                    if (y < minY) minY = y;
                    if (y > maxY) maxY = y;
                }
            }
        }
        var cropWidth = maxX - minX + 1;
        var cropHeight = maxY - minY + 1;
        if (cropWidth > 0 && cropHeight > 0) {
            var tempCanvas = document.createElement('canvas');
            tempCanvas.width = cropWidth;
            tempCanvas.height = cropHeight;
            var tempCtx = tempCanvas.getContext('2d');
            tempCtx.putImageData(ctx.getImageData(minX, minY, cropWidth, cropHeight), 0, 0);
            return tempCanvas.toDataURL('image/png');
        }
        return canvas.toDataURL('image/png');
    }

    $('#save-signature-modal').on('click', function() {
        var name = $('#edit_name').val();
        if (!name) {
            Swal.fire('Warning', 'Silakan masukkan nama sales', 'warning');
            return;
        }

        var signatureData = "";
        if ($('#mode-draw-modal').is(':checked')) {
            if (signaturePad.isEmpty()) {
                Swal.fire('Warning', 'Silakan gambar tanda tangan terlebih dahulu', 'warning');
                return;
            }
            signatureData = cropSignatureCanvas(canvas);
        } else {
            signatureData = $('#preview-image-modal').attr('src');
            if (!signatureData) {
                Swal.fire('Warning', 'Silakan pilih file gambar', 'warning');
                return;
            }
        }

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            onBeforeOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "{{ url('signature') }}",
            type: "POST",
            data: {
                id: $('#edit_id').val(),
                name: name,
                signature: signatureData
            },
            success: function(response) {
                $('#edit-modal').modal('hide');
                Swal.fire('Success', 'Tanda tangan berhasil disimpan', 'success');
                table.ajax.reload();
            },
            error: function() {
                Swal.fire('Error', 'Gagal menyimpan tanda tangan', 'error');
            }
        });
    });
});
</script>
@endsection
