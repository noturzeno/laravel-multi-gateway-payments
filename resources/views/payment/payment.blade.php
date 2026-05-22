@extends('layouts.app')
@section('content')
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-body">
                        <h2>Recon Payment</h2>
                        <br>
                        <form action="{{ $url }}" method="post">
                            <button type="submit" class="btn btn-lg btn-primary">Pay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
