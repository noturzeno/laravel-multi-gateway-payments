@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Payment Return') }}</div>

                <div class="card-body">
                    <h2>Return from payment gateway</h2>
                    @if (!empty($payload))
                        <pre>{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @else
                        <p>No return data received.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
