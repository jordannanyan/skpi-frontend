@extends('adminlte::page')

@section('title', 'Edit Prodi')

@include('fakultas.partials.header')


@section('content_header')
    <h1>Edit Prodi</h1>
@stop

@section('content')
    <form action="{{ route('fakultas.prodi.update', $prodi['id_prodi']) }}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="card">
            <div class="card-body">
                @include('fakultas.prodi.partials.form', ['isEdit' => true])
                <button class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('fakultas.prodi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@stop