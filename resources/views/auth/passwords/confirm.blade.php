<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ ucfirst(str_replace("-"," ",Request::segment(1))) }} &mdash; MelindaStore</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                  <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand">
                      <img src="{{ asset('logo-melinda.png') }}" alt="logo" width="300" height="100" 
                            class="mt-2">
                  </div>
                  <div class="card card-primary">
                      <div class="card-header"><h4>Reset Password</h4></div>

                      <div class="card-body">
                        <form method="POST" action="{{ url('update-password') }}" id="update-password">
                            @csrf
                            <input id="email" type="hidden" class="form-control" name="email" tabindex="1" required value="{{ $email }}">

                          <div class="form-group">
                            <label for="password">New Password <span style="color: red;" id="span-password"></span></label>
                            <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" tabindex="2" required minlength="6">
                            <div id="pwindicator" class="pwindicator">
                              <div class="bar"></div>
                              <div class="label"></div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="password-confirm">Confirm Password <span style="color: red;" id="span-password-confirm"></span></label>
                            <input id="password-confirm" type="password" class="form-control" name="confirm-password" tabindex="2" required minlength="6">
                          </div>

                          <div class="form-group">
                            <button type="button" class="btn btn-primary btn-lg btn-block" tabindex="4">
                              Reset Password
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  <div class="simple-footer">
                      Copyright &copy; Melindastore.
                  </div>
              </div>
          </div>
      </div>
  </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': "application/json"
            }
        });

        var minLength = 6;

        $("input").on("keydown keyup change", function(){
            var value = $(this).val();
            var id = $(this).attr('id');
            if (value.length < minLength){
                $("#span-"+id).text("Min length 6 digit");
            }else{
                $("#span-"+id).text("");
            }
        });

        $(document).on('click', '.btn-block', function(){
            if($('#password').val() != $('#password-confirm').val()){
                $("#span-password-confirm").text('*password not match');
            }else{
                Swal.fire({
                    title: 'Checking...',
                    text: "Please wait",
                    imageUrl: "{{ asset('waiting.gif') }}",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                let data = {}
                data.email = $('#email').val();
                data.password = $('#password').val();
                $.ajax({
                    url: "{{ url('update-password') }}",
                    type: "post",
                    data: JSON.stringify(data),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(response){
                        Swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Password has been updated.',
                        }).then((result) => {
                            window.location.href="/login";
                        });
                    }
                });
            }
        });
    });
</script>
<!-- General JS Scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>

<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- Page Specific JS File -->
</body>

</html>