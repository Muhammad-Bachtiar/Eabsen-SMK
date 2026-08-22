@extends('layouts.app')

@section('content')
<div class="page-heading">
    <h3>Catat Pelanggaran Baru</h3>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Form Input Pelanggaran</h4>
                    <a href="{{ route('bk.pelanggaran.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('bk.pelanggaran.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Kejadian</label>
                            <input type="date" name="tanggal_kejadian" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswas as $siswa)
                                    <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Pelanggaran</label>
                            <select name="jenis_pelanggaran_id" class="form-select" required>
                                <option value="">-- Pilih Pelanggaran --</option>
                                @foreach($jenisPelanggarans as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama_pelanggaran }} (Poin: {{ $jenis->poin }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Poin akan otomatis direkap ke data siswa.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Deskripsi Kejadian (Opsional / Detail)</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan detail kejadian pelanggarannya di sini..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Catatan Pelanggaran</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection