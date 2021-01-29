<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    </head>
    <body>
    <div class="col-md-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="flex-center position-ref full-height">
            <form id="csvCalc" method="post" action="{{route('formProcess')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="exampleInputEmail1">Upload file *</label>
                    <input type="file" class="form-control" id="csvFile" name="csvFile" required/>
                    <small id="csvFileHelp" class="form-text text-muted">upload csv file</small>
                    <small id="fileError" class="form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">List of currencies and exchange rates * (EUR:1,USD:0.987,GBP:0.878)</label>
                    <input type="text" class="form-control" id="currencies" name="currencies" placeholder="currencies and exchange rates separeted by comma (EUR:1,USD:0.987,GBP:0.878)" required>
                    <small id="listError" class="form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Output currency * (GBP)</label>
                    <input type="text" class="form-control" id="outputCurrency" name="outputCurrency" placeholder="GBP" required>
                    <small id="outputError" class="form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Customer</label>
                    <input type="text" class="form-control" id="customer" name="customer" placeholder="vat">
                </div>
                <button type="submit" class="btn btn-primary">Calc</button>
            </form>
        </div>
        @if (\Session::has('success'))
            <div class="alert alert-success mt-5">
                <ul>
                    @foreach(\Session::get('success')['result'] as $customer => $sum)
                        
                        <li>Customer {{$customer}} - {{$sum}} {{Session::get('success')['currency']}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <script src="{{asset('validation.js')}}"></script>
    </body>
</html>
