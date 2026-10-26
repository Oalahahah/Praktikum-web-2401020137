<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// READ: Menampilkan data mahasiswa dan program studi
Route::get('/mahasiswa', function () {
    try {
        $pdo = DB::connection()->getPdo();

        // Membaca data mahasiswa beserta program studinya
        $stmtMahasiswa = $pdo->prepare(
            'SELECT mahasiswa.*, program_studi.nama_prodi AS nama_program_studi
             FROM mahasiswa
             JOIN program_studi
                ON mahasiswa.program_studi_id = program_studi.id
             ORDER BY mahasiswa.nim'
        );

        $stmtMahasiswa->execute();
        $mahasiswa = $stmtMahasiswa->fetchAll(PDO::FETCH_ASSOC);

        // Membaca daftar program studi
        $stmtProgram = $pdo->prepare(
            'SELECT id, nama_prodi AS nama_program_studi
             FROM program_studi
             ORDER BY nama_prodi'
        );

        $stmtProgram->execute();
        $programStudi = $stmtProgram->fetchAll(PDO::FETCH_ASSOC);

        return view(
            'mahasiswa',
            compact('mahasiswa', 'programStudi')
        );

    } catch (PDOException $e) {
        report($e);

        abort(
            500,
            'Terjadi kesalahan saat membaca data dari database.'
        );
    }
})->name('mahasiswa.index');

// CREATE: Menyimpan data mahasiswa baru
Route::post('/mahasiswa', function (Request $request) {
    try {
        // The exists rule also queries the database, so keep it inside this handler.
        $validated = $request->validate([
            'nim' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'usia' => 'required|integer|min:1|max:120',
            'program_studi_id' => 'required|integer|exists:program_studi,id',
        ]);

        $pdo = DB::connection()->getPdo();

        // Memeriksa NIM duplikat
        $stmtNim = $pdo->prepare(
            'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim'
        );

        $stmtNim->execute([
            'nim' => $validated['nim'],
        ]);

        if ((int) $stmtNim->fetchColumn() > 0) {
            return redirect()
                ->route('mahasiswa.index')
                ->withInput()
                ->withErrors([
                    'nim' => 'NIM sudah terdaftar.',
                ]);
        }

        // Memeriksa email duplikat
        $stmtEmail = $pdo->prepare(
            'SELECT COUNT(*) FROM mahasiswa WHERE email = :email'
        );

        $stmtEmail->execute([
            'email' => $validated['email'],
        ]);

        if ((int) $stmtEmail->fetchColumn() > 0) {
            return redirect()
                ->route('mahasiswa.index')
                ->withInput()
                ->withErrors([
                    'email' => 'Email sudah terdaftar.',
                ]);
        }

        // Menyimpan data dengan prepared statement
        $stmtInsert = $pdo->prepare(
            'INSERT INTO mahasiswa
                (nim, nama, email, usia, program_studi_id)
             VALUES
                (:nim, :nama, :email, :usia, :program_studi_id)'
        );

        $stmtInsert->execute([
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'usia' => $validated['usia'],
            'program_studi_id' => $validated['program_studi_id'],
        ]);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan.');

    } catch (PDOException $e) {
        report($e);

        return redirect()
            ->route('mahasiswa.index')
            ->withInput()
            ->with('error', 'Data gagal disimpan. Periksa database.');
    }
})->name('mahasiswa.store');
