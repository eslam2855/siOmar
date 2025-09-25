<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Notifications\WelcomeNotification;

class UserService
{
    /**
     * Create a new user with proper error handling.
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'password' => Hash::make($data['password']),
            ]);
            
            $user->assignRole('user');
            
            // Send welcome notification
            $user->notify(new WelcomeNotification());
            
            DB::commit();
            return $user;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update user profile with image handling.
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $image = null): User
    {
        DB::beginTransaction();
        
        try {
            // Handle profile image upload
            if ($image) {
                $imagePath = $this->handleProfileImageUpload($user, $image);
                $data['profile_image'] = $imagePath;
            }
            
            $user->update($data);
            
            DB::commit();
            return $user->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Handle profile image upload with optimization.
     */
    protected function handleProfileImageUpload(User $user, UploadedFile $image): string
    {
        // Delete old image if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }
        
        // Generate unique filename
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
        
        // Store image
        $path = $image->storeAs('profile-images', $filename, 'public');
        
        return $path;
    }
    
    /**
     * Remove user profile image.
     */
    public function removeProfileImage(User $user): bool
    {
        if (!$user->profile_image) {
            return false;
        }
        
        DB::beginTransaction();
        
        try {
            Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Change user password with validation.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \InvalidArgumentException('Current password is incorrect.');
        }
        
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
        
        // Revoke all tokens to force re-login
        $user->tokens()->delete();
        
        return true;
    }
    
    /**
     * Get user with eager loaded relationships.
     */
    public function getUserWithRelations(int $userId): ?User
    {
        return User::with(['reservations', 'reviews'])
            ->find($userId);
    }
    
    /**
     * Get paginated users with search and filtering.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15)
    {
        $query = User::with(['reservations', 'reviews']);
        
        // Apply search filter
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply role filter
        if (isset($filters['role']) && $filters['role'] !== '') {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
