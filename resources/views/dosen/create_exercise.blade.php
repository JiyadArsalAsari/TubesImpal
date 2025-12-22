<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#4b5b3b] text-white font-sans min-h-screen">
    <div class="max-w-3xl mx-auto py-10 px-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-200">Buat Assignment untuk</p>
                <h1 class="text-2xl font-bold">{{ $mahasiswa->user->name ?? $mahasiswa->nama ?? 'Mahasiswa' }}</h1>
            </div>
            <a href="{{ route('dosen.dashboard') }}" class="text-sm underline">Kembali ke Dashboard</a>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 text-red-800 p-4 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dosen.exercise.store', $mahasiswa->id) }}" method="POST" enctype="multipart/form-data" class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
            @csrf
            <input type="hidden" name="type" value="assignment">
            <div>
                <label class="block text-sm text-gray-200 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg text-black p-3">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-200 mb-1">Judul</label>
                <input type="text" name="title" class="w-full rounded-lg text-black p-3" placeholder="Contoh: Quiz 1 - Introduction to UI/UX Design" required>
            </div>

            <div>
                <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
                <textarea name="description" rows="3" class="w-full rounded-lg text-black p-3" placeholder="Ringkasan soal atau instruksi"></textarea>
            </div>

            <div>
                <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
                <input type="datetime-local" name="deadline" class="w-full rounded-lg text-black p-3">
            </div>

            <div>
                <label class="block text-sm text-gray-200 mb-1">File Assignment (opsional)</label>
                <input type="file" name="file_attachment" class="w-full rounded-lg text-black p-3">
                <p class="text-xs text-gray-200 mt-1">Upload file assignment (PDF, DOC, DOCX, ZIP, etc.)</p>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Simpan Exercise</button>
            </div>
        </form>
    </div>
</body>
</html>

