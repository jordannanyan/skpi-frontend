@extends('adminlte::page')
@section('title', 'Profil Admin')
@include('superadmin.partials.header')

@section('content_header')
  <h1>Profil Admin</h1>
@stop

@section('content')
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())    <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="card">
    <form method="POST" action="{{ route('superadmin.profile.update') }}">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username', $admin['username'] ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Password (biarkan kosong jika tidak diganti)</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="{{ url('/superadmin/dashboard') }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
@stop
