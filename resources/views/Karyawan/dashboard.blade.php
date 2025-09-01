@extends('layouts.app')

@section('title', 'Dashboard Gudang')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard Gudang</h1>
    <p>Halo {{ Auth::user()->name }}, ini adalah dashboard bagian gudang.</p>
@endsection
