<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function loginForm(Request $request)
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,dosen',
        ]);

        if ($validator->fails()) {
            // Hanya kembalikan input yang valid, input yang error (dan password) dikosongkan
            $failedFields = $validator->errors()->keys();
            $inputsToFlash = $request->except(array_merge($failedFields, ['password', 'password_confirmation']));
            
            return back()->withErrors($validator)->withInput($inputsToFlash);
        }

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

        // Redirect berdasarkan role / admin flag
        if ($user->is_admin) {
            return redirect('/admin/dashboard');
        } elseif ($user->role == 'dosen') {
            return redirect('/dosen/dashboard');
        } else {
            return redirect('/mahasiswa/dashboard');
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

            $user = Auth::user();

            // Check if login is restricted to a specific role (except for admins)
            if ($request->has('role') && $request->role && $user->role !== $request->role && !$user->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'username_or_email' => 'Salah login! (Akun Anda: ' . ucfirst($user->role) . ')',
                ]);
            }

            // Redirect berdasarkan role
            if ($user->is_admin) {
                return redirect('/admin/dashboard');
            } elseif ($user->role == 'dosen') {
                return redirect('/dosen/dashboard');
            } else {
                return redirect('/mahasiswa/dashboard');
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'username_or_email' => 'Invalid User ID or Password!',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Profile settings methods
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Log request data for debugging
        \Log::info('Profile update request', [
            'has_file' => $request->hasFile('profile_picture'),
            'file_input' => $request->file('profile_picture'),
            'all_files' => $request->allFiles(),
            'all_input' => $request->except('password', 'password_confirmation')
        ]);
        
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        // Log the validated data before update
        \Log::info('Validated data for update', [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'user_id' => $user->id
        ]);

        // Update user data
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        
        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            // Log file information
            \Log::info('Processing profile picture', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);
            
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/profile_pictures', $filename);
            // Save the filename to the database
            $user->profile_picture = $filename;
            
            // Log successful storage
            \Log::info('Profile picture saved', [
                'filename' => $filename,
                'path' => storage_path('app/public/profile_pictures/' . $filename)
            ]);
        }
        
        $result = $user->save();
        
        // Log the result of the save operation
        \Log::info('User save result', [
            'result' => $result,
            'updated_user' => $user->toArray()
        ]);

        // Also update related mahasiswa or dosen record name
        if ($user->role == 'mahasiswa' && $user->mahasiswa) {
            $user->mahasiswa->nama = $validated['name'];
            $user->mahasiswa->save();
        } elseif ($user->role == 'dosen' && $user->dosen) {
            $user->dosen->nama = $validated['name'];
            $user->dosen->save();
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function deleteProfilePhoto(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has a profile picture
        if ($user->profile_picture) {
            // Delete the file from storage
            Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
            
            // Remove the filename from the database
            $user->profile_picture = null;
            $user->save();
            
            return redirect()->back()->with('success', 'Profile photo deleted successfully!');
        }
        
        return redirect()->back()->with('error', 'No profile photo to delete.');
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