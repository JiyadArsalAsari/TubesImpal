<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function loginForm(Request $request)
    {
        $role = $request->query('role');
        return view('auth.login', compact('role'));
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,dosen',
        ]);

        // Simpan ke tabel users
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Jika role = mahasiswa
        if ($request->role == 'mahasiswa') {
            // Generate mahasiswaID otomatis
            $mahasiswaID = $this->generateMahasiswaID();
            
            // Simpan ke tabel mahasiswa
            Mahasiswa::create([
                'mahasiswaID' => $mahasiswaID,
                'nama' => $request->nama,
                'user_id' => $user->id,
            ]);
        }
        // Jika role = dosen
        elseif ($request->role == 'dosen') {
            // Generate dosenID otomatis
            $dosenID = $this->generateDosenID();
            
            // Simpan ke tabel dosen
            Dosen::create([
                'dosenID' => $dosenID,
                'nama' => $request->nama,
                'user_id' => $user->id,
            ]);
        }

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Redirect berdasarkan role
        if ($user->role == 'mahasiswa') {
            return redirect('/mahasiswa/dashboard');
        } else {
            return redirect('/dosen/dashboard');
        }
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah input adalah email atau username
        $field = filter_var($request->username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $request->username_or_email,
            'password' => $request->password,
        ];

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            $user = Auth::user();
            
            // Jika ada role dari parameter, cek apakah sesuai dengan role user
            if ($request->has('role') && $request->role !== $user->role) {
                Auth::logout();
                return back()->withErrors([
                    'username_or_email' => 'Anda tidak memiliki akses sebagai ' . $request->role . '.',
                ]);
            }
            
            if ($user->role == 'mahasiswa') {
                return redirect('/mahasiswa/dashboard');
            } else {
                return redirect('/dosen/dashboard');
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'username_or_email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function generateMahasiswaID()
    {
        // Ambil mahasiswa terakhir berdasarkan ID
        $lastMahasiswa = Mahasiswa::orderBy('id', 'desc')->first();
        
        if (!$lastMahasiswa) {
            // Jika belum ada mahasiswa, mulai dari MHS-001
            return 'MHS-001';
        }
        
        // Ambil angka dari ID terakhir
        $lastID = $lastMahasiswa->mahasiswaID; // Format: MHS-xxx
        $number = (int) substr($lastID, 4); // Ambil angka setelah "MHS-"
        $newNumber = $number + 1;
        
        // Format dengan leading zeros
        return 'MHS-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateDosenID()
    {
        // Ambil dosen terakhir berdasarkan ID
        $lastDosen = Dosen::orderBy('id', 'desc')->first();
        
        if (!$lastDosen) {
            // Jika belum ada dosen, mulai dari DSN-001
            return 'DSN-001';
        }
        
        // Ambil angka dari ID terakhir
        $lastID = $lastDosen->dosenID; // Format: DSN-xxx
        $number = (int) substr($lastID, 4); // Ambil angka setelah "DSN-"
        $newNumber = $number + 1;
        
        // Format dengan leading zeros
        return 'DSN-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}