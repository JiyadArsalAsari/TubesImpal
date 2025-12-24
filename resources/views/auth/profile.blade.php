@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-blue-600 px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Profile Settings</h1>
                        <p class="mt-2 opacity-90">Manage your personal information and account settings</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <i class="fas fa-user-cog text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Form -->
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="px-6 py-8">
                @csrf
                @method('POST')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Profile Picture Section -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="mb-4">
                                <div class="mx-auto bg-gray-200 border-2 border-dashed rounded-full w-24 h-24 flex items-center justify-center">
                                    @if($user->profile_picture && Storage::disk('public')->exists('profile_pictures/' . $user->profile_picture))
                                        <img id="profile-picture-preview" src="{{ asset('storage/profile_pictures/' . $user->profile_picture) }}" alt="Profile" class="rounded-full w-24 h-24 object-cover">
                                    @else
                                        <img id="profile-picture-preview" src="" alt="Profile Preview" class="rounded-full w-24 h-24 object-cover hidden">
                                        <i id="profile-picture-icon" class="fas fa-user text-3xl text-gray-400"></i>
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Profile Photo</h3>
                            <p class="text-sm text-gray-500 mb-4">Upload a photo to make your profile stand out.</p>
                            <div class="mt-4">
                                <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer" onclick="showProfileOptions()">
                                    <i class="fas fa-edit mr-2"></i> Edit Profile Picture
                                </button>
                                <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="handleImageSelection(this)">
                                <div id="file-name-display" class="mt-2 text-sm hidden" style="color: #000000 !important;"></div>
                                <div id="profile-success-message" class="mt-2 text-sm text-green-600 hidden" style="color: #16a34a !important;"></div>
                                <button type="button" id="crop-button" class="mt-2 inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer hidden">
                                    <i class="fas fa-crop-alt mr-1"></i> Crop Image
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details Section -->
                    <div class="md:col-span-2">
                        <div class="space-y-6">
                            <!-- Name Field -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name" 
                                        value="{{ old('name', $user->name) }}"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 bg-gray-100 cursor-default"
                                        placeholder="Enter your full name"
                                        readonly
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="button" onclick="enableEdit('name')" class="text-gray-400 hover:text-green-600 focus:outline-none" id="edit-name-btn">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 pr-10 flex items-center hidden" id="name-controls">
                                        <button type="button" onclick="acceptEdit('name')" class="text-green-600 hover:text-green-800 mr-2">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="cancelEdit('name')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        value="{{ old('email', $user->email) }}"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 bg-gray-100 cursor-default"
                                        placeholder="Enter your email address"
                                        readonly
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="button" onclick="enableEdit('email')" class="text-gray-400 hover:text-green-600 focus:outline-none" id="edit-email-btn">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 pr-10 flex items-center hidden" id="email-controls">
                                        <button type="button" onclick="acceptEdit('email')" class="text-green-600 hover:text-green-800 mr-2">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="cancelEdit('email')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username Field -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tag text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="username" 
                                        id="username" 
                                        value="{{ old('username', $user->username) }}"
                                        class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900 bg-gray-100 cursor-default"
                                        placeholder="Enter your username"
                                        readonly
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="button" onclick="enableEdit('username')" class="text-gray-400 hover:text-green-600 focus:outline-none" id="edit-username-btn">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 pr-10 flex items-center hidden" id="username-controls">
                                        <button type="button" onclick="acceptEdit('username')" class="text-green-600 hover:text-green-800 mr-2">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="cancelEdit('username')" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Fields -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                                
                                <!-- New Password -->
                                <div class="mb-4">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="password" 
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900"
                                            placeholder="Enter new password (leave blank to keep current)"
                                            minlength="8"
                                        >
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input 
                                            type="password" 
                                            name="password_confirmation" 
                                            id="password_confirmation" 
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-gray-900"
                                            placeholder="Confirm new password"
                                        >
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-10 flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:from-green-700 hover:to-blue-700 active:from-green-800 active:to-blue-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
                        onclick="prepareFormForSubmission()"
                    >
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Cropping Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Crop Your Profile Picture</h3>
        </div>
        <div class="p-4">
            <div class="flex justify-center">
                <div id="cropper-container" class="w-full max-w-md">
                    <img id="cropper-image" src="" alt="Crop image">
                </div>
            </div>
        </div>
        <div class="p-4 border-t flex justify-end space-x-2">
            <button type="button" onclick="closeCropper()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Cancel
            </button>
            <button type="button" onclick="applyCrop()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Apply Crop
            </button>
        </div>
    </div>
</div>

<!-- Profile Options Modal -->
<div id="profile-options-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-64">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold" style="color: #000000;">Profile Photo Options</h3>
        </div>
        <div class="p-2">
            <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 flex items-center" onclick="triggerFileUpload()">
                <i class="fas fa-upload text-gray-500 mr-3"></i>
                <span style="color: #000000;">Upload New Photo</span>
            </button>
            @if($user->profile_picture)
            <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 flex items-center text-red-600" onclick="deleteProfilePhoto()">
                <i class="fas fa-trash-alt text-red-500 mr-3"></i>
                <span style="color: #000000;">Delete Photo</span>
            </button>
            @endif
        </div>
        <div class="p-2 border-t">
            <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 flex items-center text-gray-600" onclick="closeProfileOptions()">
                <i class="fas fa-times text-gray-500 mr-3"></i>
                <span style="color: #000000;">Cancel</span>
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-80">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold" style="color: #000000;">Delete Profile Photo</h3>
        </div>
        <div class="p-4">
            <p style="color: #000000;">Are you sure you want to delete your profile photo?</p>
        </div>
        <div class="p-4 border-t flex justify-end space-x-2">
            <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800" onclick="closeDeleteConfirm()" style="color: #000000;">
                Cancel
            </button>
            <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="confirmDeletePhoto()">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Hidden form for delete action -->
<form id="delete-photo-form" action="{{ route('profile.delete.photo') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    // Store original values for cancel functionality
    const originalValues = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        username: document.getElementById('username').value
    };

    // Cropping variables
    let cropper = null;
    let selectedFile = null;

    function enableEdit(fieldId) {
        const field = document.getElementById(fieldId);
        const editBtn = document.getElementById(`edit-${fieldId}-btn`);
        const controls = document.getElementById(`${fieldId}-controls`);
        
        // Enable editing
        field.readOnly = false;
        field.classList.remove('bg-gray-100', 'cursor-default');
        field.classList.add('bg-white');
        field.focus();
        
        // Hide edit button and show controls
        editBtn.classList.add('hidden');
        controls.classList.remove('hidden');
    }

    function acceptEdit(fieldId) {
        const field = document.getElementById(fieldId);
        const editBtn = document.getElementById(`edit-${fieldId}-btn`);
        const controls = document.getElementById(`${fieldId}-controls`);
        
        // Disable editing
        field.readOnly = true;
        field.classList.add('bg-gray-100', 'cursor-default');
        field.classList.remove('bg-white');
        
        // Update original value
        originalValues[fieldId] = field.value;
        
        // Show edit button and hide controls
        editBtn.classList.remove('hidden');
        controls.classList.add('hidden');
    }

    function cancelEdit(fieldId) {
        const field = document.getElementById(fieldId);
        const editBtn = document.getElementById(`edit-${fieldId}-btn`);
        const controls = document.getElementById(`${fieldId}-controls`);
        
        // Restore original value
        field.value = originalValues[fieldId];
        
        // Disable editing
        field.readOnly = true;
        field.classList.add('bg-gray-100', 'cursor-default');
        field.classList.remove('bg-white');
        
        // Show edit button and hide controls
        editBtn.classList.remove('hidden');
        controls.classList.add('hidden');
    }

    function displayFileName(input) {
        const fileNameDisplay = document.getElementById('file-name-display');
        const successMessage = document.getElementById('profile-success-message');
        
        // Hide success message when displaying file name
        successMessage.classList.add('hidden');
        
        if (input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;
            fileNameDisplay.classList.remove('hidden');
        } else {
            fileNameDisplay.classList.add('hidden');
        }
    }

    function handleImageSelection(input) {
        displayFileName(input);
        
        if (input.files && input.files[0]) {
            selectedFile = input.files[0];
            
            // Show crop button
            const cropButton = document.getElementById('crop-button');
            cropButton.classList.remove('hidden');
            
            // Create preview of the selected image (before cropping)
            const reader = new FileReader();
            reader.onload = function(e) {
                // Show the preview image and hide the icon
                const previewImg = document.getElementById('profile-picture-preview');
                const icon = document.getElementById('profile-picture-icon');
                
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                icon.classList.add('hidden');
                
                // Show cropper when file is loaded
                showCropper(e.target.result);
            };
            reader.readAsDataURL(selectedFile);
        }
    }

    function showCropper(imageSrc) {
        const modal = document.getElementById('crop-modal');
        const image = document.getElementById('cropper-image');
        
        // Set image source
        image.src = imageSrc;
        
        // Destroy existing cropper if any
        if (cropper) {
            cropper.destroy();
        }
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Initialize cropper after a small delay to ensure image is loaded
        setTimeout(() => {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                movable: true,
                zoomable: true,
                rotatable: false,
                scalable: false,
                autoCropArea: 1,
                responsive: true,
                background: false,
                ready() {
                    // Cropper is ready
                }
            });
        }, 100);
    }

    function closeCropper() {
        const modal = document.getElementById('crop-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function applyCrop() {
        if (!cropper) return;
        
        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        // Convert to blob
        canvas.toBlob((blob) => {
            if (!blob) return;
            
            // Create a new file from the blob
            const croppedFile = new File([blob], 'cropped-profile.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Update the file input with the cropped image
            const fileInput = document.getElementById('profile_picture');
            
            // Create a new DataTransfer to hold the file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            fileInput.files = dataTransfer.files;
            
            // Update file name display
            const fileNameDisplay = document.getElementById('file-name-display');
            fileNameDisplay.classList.add('hidden'); // Hide the file name display
            
            // Show success message
            const successMessage = document.getElementById('profile-success-message');
            successMessage.textContent = 'Profile photo cropped successfully!';
            successMessage.classList.remove('hidden');
            
            // Hide crop button
            document.getElementById('crop-button').classList.add('hidden');
            
            // Close cropper
            closeCropper();
        }, 'image/jpeg', 0.9);
    }
    
    function showProfileOptions() {
        const modal = document.getElementById('profile-options-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeProfileOptions() {
        const modal = document.getElementById('profile-options-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function triggerFileUpload() {
        document.getElementById('profile_picture').click();
        closeProfileOptions();
    }

    function deleteProfilePhoto() {
        closeProfileOptions();
        const modal = document.getElementById('delete-confirm-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteConfirm() {
        const modal = document.getElementById('delete-confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmDeletePhoto() {
        document.getElementById('delete-photo-form').submit();
    }
    
    // Check for success message from server
    window.addEventListener('load', function() {
        const successDiv = document.querySelector('.bg-green-50');
        if (successDiv) {
            const successMessage = document.getElementById('profile-success-message');
            const successText = successDiv.querySelector('p');
            if (successMessage && successText && successText.textContent.includes('Profile updated successfully!')) {
                successMessage.textContent = 'Profile updated!';
                successMessage.classList.remove('hidden');
                
                // Hide file name display if shown
                document.getElementById('file-name-display').classList.add('hidden');
                
                // Hide crop button
                document.getElementById('crop-button').classList.add('hidden');
            }
        }
    });

    function prepareFormForSubmission() {
        // Check if there's a cropped image and apply it if needed
        if (cropper) {
            applyCrop();
        }
        
        // Accept all pending edits to ensure the values are properly set
        const fields = ['name', 'email', 'username'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const editBtn = document.getElementById(`edit-${fieldId}-btn`);
            const controls = document.getElementById(`${fieldId}-controls`);
            
            // If the field is currently in edit mode, accept the changes
            if (editBtn.classList.contains('hidden') && !controls.classList.contains('hidden')) {
                // Field is in edit mode, accept changes to ensure it's properly saved
                acceptEdit(fieldId);
            }
            
            // Make sure the field is not read-only for form submission
            field.readOnly = false;
            // Also remove the background class that indicates readonly state
            field.classList.remove('bg-gray-100', 'cursor-default');
            field.classList.add('bg-white');
        });
    }
</script>

<!-- Include Cropper.js CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<!-- Custom Styles -->
<style>
    .bg-gradient-to-br {
        background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%);
    }
    
    .bg-gradient-to-r {
        background: linear-gradient(to right, #16a34a 0%, #2563eb 100%);
    }
    
    .hover\:from-green-700:hover {
        --tw-gradient-stops: #15803d, #15803d var(--tw-gradient-from-position), #2563eb var(--tw-gradient-to-position);
    }
    
    .focus\:ring-green-500:focus {
        --tw-ring-opacity: 1;
        --tw-ring-color: rgb(34 197 94 / var(--tw-ring-opacity));
    }
    
    /* Cropper.js customization */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
    }
</style>
@endsection