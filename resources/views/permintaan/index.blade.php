    @extends('layouts.app')

    @section('content')
        @livewire('permintaan-table', ['context' => 'index'])
    @endsection
