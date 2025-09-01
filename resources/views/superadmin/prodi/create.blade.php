@extends('adminlte::page')

@section('title', 'Tambah Prodi')

@include('superadmin.partials.header')


@section('content_header')
<h1>Tambah Prodi</h1>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form action="{{ route('superadmin.prodi.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            @include('superadmin.prodi.partials.form')
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('superadmin.prodi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</form>
@stop