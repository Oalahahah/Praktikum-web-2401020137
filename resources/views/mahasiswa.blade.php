
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
</head>
<body>

    <h1>Data Mahasiswa</h1>

    {{-- Pesan sukses --}}
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    {{-- Pesan gagal --}}
    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    {{-- Pesan kesalahan validasi --}}
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Form Tambah Mahasiswa</h2>

    <form action="{{ route('mahasiswa.store') }}" method="POST">
        @csrf

        <p>
            <label for="nim">NIM:</label><br>
            <input
                type="text"
                id="nim"
                name="nim"
                value="{{ old('nim') }}"
                required
            >
        </p>

        <p>
            <label for="nama">Nama:</label><br>
            <input
                type="text"
                id="nama"
                name="nama"
                value="{{ old('nama') }}"
                required
            >
        </p>

        <p>
            <label for="email">Email:</label><br>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
            >
        </p>

        <p>
            <label for="usia">Usia:</label><br>
            <input
                type="number"
                id="usia"
                name="usia"
                value="{{ old('usia') }}"
                required
            >
        </p>

        <p>
            <label for="program_studi_id">Program Studi:</label><br>
            <select id="program_studi_id" name="program_studi_id" required>
                <option value="">-- Pilih Program Studi --</option>

                @foreach ($programStudi as $program)
                    <option
                        value="{{ $program['id'] }}"
                        {{ old('program_studi_id') == $program['id'] ? 'selected' : '' }}
                    >
                        {{ $program['nama_program_studi'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan</button>
    </form>

    <h2>Daftar Mahasiswa</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>No.</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Usia</th>
                <th>Program Studi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($mahasiswa as $index => $mhs)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mhs['nim'] }}</td>
                    <td>{{ $mhs['nama'] }}</td>
                    <td>{{ $mhs['email'] }}</td>
                    <td>{{ $mhs['usia'] }}</td>
                    <td>{{ $mhs['nama_program_studi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        Belum ada data mahasiswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
