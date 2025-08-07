<div class="form-group">
    <label>Nama Prodi</label>
    <input type="text" name="nama_prodi" class="form-control" value="{{ old('nama_prodi', $prodi['nama_prodi'] ?? '') }}" required>
</div>

<div class="form-group">
    <label>Fakultas</label>
    <select name="id_fakultas" class="form-control" required>
        <option value="">-- Pilih Fakultas --</option>
        @foreach($fakultasList as $fakultas)
            <option value="{{ $fakultas['id_fakultas'] }}" {{ (old('id_fakultas', $prodi['id_fakultas'] ?? '') == $fakultas['id_fakultas']) ? 'selected' : '' }}>{{ $fakultas['nama_fakultas'] }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Username</label>
    <input type="text" name="username" class="form-control" value="{{ old('username', $prodi['username'] ?? '') }}" required>
</div>

<div class="form-group">
    <label>Password {{ isset($isEdit) ? '(Kosongkan jika tidak ingin diubah)' : '' }}</label>
    <input type="password" name="password" class="form-control">
</div>

<div class="form-group">
    <label>Akreditasi</label>
    <input type="text" name="akreditasi" class="form-control" value="{{ old('akreditasi', $prodi['akreditasi'] ?? '') }}">
</div>

<div class="form-group">
    <label>SK Akreditasi</label>
    <input type="text" name="sk_akre" class="form-control" value="{{ old('sk_akre', $prodi['sk_akre'] ?? '') }}">
</div>

<div class="form-group">
    <label>Jenis Jenjang</label>
    <input type="text" name="jenis_jenjang" class="form-control" value="{{ old('jenis_jenjang', $prodi['jenis_jenjang'] ?? '') }}">
</div>

<div class="form-group">
    <label>Kompetensi Kerja</label>
    <textarea name="kompetensi_kerja" class="form-control">{{ old('kompetensi_kerja', $prodi['kompetensi_kerja'] ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Bahasa</label>
    <textarea name="bahasa" class="form-control">{{ old('bahasa', $prodi['bahasa'] ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Penilaian</label>
    <textarea name="penilaian" class="form-control">{{ old('penilaian', $prodi['penilaian'] ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Jenis Lanjutan</label>
    <input type="text" name="jenis_lanjutan" class="form-control" value="{{ old('jenis_lanjutan', $prodi['jenis_lanjutan'] ?? '') }}">
</div>

<div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat" class="form-control">{{ old('alamat', $prodi['alamat'] ?? '') }}</textarea>
</div>
