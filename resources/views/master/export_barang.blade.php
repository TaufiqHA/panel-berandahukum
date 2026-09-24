<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE = edge">
        <meta name="viewport" content="width = device-width, initial-scale = 1.0">
        <title> Export PDF </title>
        {{-- <! - Bootstrap5 CSS -> --}}
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
                integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU"
                crossorigin="anonymous">

            <style>
                table,
                th,
                td {
                    border: 1px solid black;
                }

                th,
                td {
                    border-color: #96D4D4;

                }

                .col1 {
                    width: 100px;
                }

                .col2 {
                    width: 500px;
                    ;
                }
                @media print {
                    #printable{
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        height:100%;
                        }
                        html, body{
                        height:100%;
                        width:100%;
                        }
                }   
            </style>
    </head>

    <body>
        <div class="container mt-4" id="printarea" >
            <div class="row">
                <div class="col-md-8">
                    <h2> Product list </h2>
                </div>
                <div class="col-md-4">
                    <div class="mb-4 d-flex justify-content-end">
                        {{-- <a class="btn btn-primary" href="http://127.0.0.1:8000/product/pdf"> Export to PDF </a> --}}
                    </div>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" class="col1">Id</th>
                                <th scope="col" class="col2">Nama Barang</th>
                                <th scope="col" class="col1">Merk</th>
                                <th scope="col" class="col1">Satuan</th>
                                <th scope="col" class="col1">Warna</th>
                                <th scope="col" class="col1">Berat</th>
                                <th scope="col" class="col1">Ukuran</th>
                                <th scope="col" class="col2">Price List</th>
                                <th scope="col" class="col2">Wajib Serial Number</th>
                                <th scope="col" class="col2">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $product)
                                <tr>
                                    <th class="col1" scope="row"> {{ $product->id }} </th>
                                    <td class="col2"> {{ $product->nama_product }} </td>
                                    <td class="col1"> {{ $product->merk }} </td>
                                    <td class="col1"> {{ $product->satuan }} </td>
                                    <td class="col1"> {{ $product->warna }} </td>
                                    <td class="col1"> {{ $product->berat }} </td>
                                    <td class="col1"> {{ $product->ukuran }} </td>
                                    <td class="col1"> {{ $product->harga }} </td>
                                    <td class="col2"> {{ $product->wajib_serial_number }} </td>
                                    <td class="col2"> {{ $product->keterngan }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>
            
            printDiv('printarea')
            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
            }
        </script>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KinkN" crossorigin="anonymous">
        </script>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

    </body>

    </html>
