@extends('adminlte::page')

@section('title', 'Edit Prodi')
@include('superadmin.partials.header')


@section('content_header')
    <h1>Edit Prodi</h1>
@stop

@section('content')
    <form action="{{ route('superadmin.prodi.update', $prodi['id_prodi']) }}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="card">
            <div class="card-body">
                @include('superadmin.prodi.partials.form', ['isEdit' => true])
                <button class="btn btn-success">Simpan Perubahan</button>
                <a href="{{ route('superadmin.prodi.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@stop