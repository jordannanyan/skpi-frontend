@extends('adminlte::page')

@section('title', 'Tambah Prodi')

@include('fakultas.partials.header')


@section('content_header')
    <h1>Tambah Prodi</h1>
@stop

@section('content')
    <form action="{{ route('fakultas.prodi.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                @include('fakultas.prodi.partials.form')
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('fakultas.prodi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </form>
@stop