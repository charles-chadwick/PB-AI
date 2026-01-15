<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia;

class DocumentController extends Controller
{
    /**
     * Upload avatar for the specified entity.
     */
    public function uploadAvatar(Request $request, string $entity_id)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', // Max 2MB
        ]);

        $entity_type = $request->route()->defaults['entity_type'];
        $entity = $this->resolveEntity($entity_type, (int) $entity_id);

        // Add media to the avatar collection (will replace existing if any)
        $entity->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');

        return back()
            ->with('message', 'Avatar uploaded successfully.')
            ->with('type', 'success');
    }

    /**
     * Remove avatar for the specified entity.
     */
    public function destroyAvatar(Request $request, string $entity_id)
    {
        $entity_type = $request->route()->defaults['entity_type'];
        $entity = $this->resolveEntity($entity_type, (int) $entity_id);

        // Clear the avatar media collection
        $entity->clearMediaCollection('avatar');

        return back()
            ->with('message', 'Avatar removed successfully.')
            ->with('type', 'success');
    }

    /**
     * Resolve the entity model based on type and ID.
     */
    private function resolveEntity(string $entity_type, int $entity_id): Model&HasMedia
    {
        return match ($entity_type) {
            'users' => User::findOrFail($entity_id),
            'patients' => Patient::findOrFail($entity_id),
            default => abort(404, 'Invalid entity type'),
        };
    }
}
