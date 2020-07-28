@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Hi, I am <b>{{ auth()->user()->name }}</b> working at <b>{{auth()->user()->organization->title}}</b>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
