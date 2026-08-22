@extends('layouts.app')

@section('content')
<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Master Jenis Pelanggaran</h5>
            <a href="{{ route('koordinator-bk.jenis-pelanggaran.create') }}" class="btn btn-primary btn-sm">Tambah Pelanggaran</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pelanggarans as $p)
                    <tr>
                        <td>{{ $p->nama_pelanggaran }}</td>
                        <td>
                            @if($p->kategori == 'ringan')
                                <span class="badge bg-success">Ringan</span>
                            @elseif($p->kategori == 'sedang')
                                <span class="badge bg-warning">Sedang</span>
                            @else
                                <span class="badge bg-danger">Berat</span>
                            @endif
                        </td>
                        <td>{{ $p->poin }}</td>
                        <td>
                            <a href="{{ route('koordinator-bk.jenis-pelanggaran.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('koordinator-bk.jenis-pelanggaran.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection