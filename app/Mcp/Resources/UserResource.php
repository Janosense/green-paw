<?php

namespace App\Mcp\Resources;

use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;

#[Description('Retrieve detailed user profile information including roles and metadata.')]
class UserResource extends Resource
{
    /**
     * The URI template for this resource.
     */
    protected string $uriTemplate = 'user://{id}';

    /**
     * The MIME type of the resource.
     */
    protected string $mimeType = 'application/json';

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        $id = $request->get('id');
        $user = User::with('roles', 'tenant')->find($id);

        if (!$user) {
            return Response::error("User with ID {$id} not found.");
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'bio' => $user->bio,
            'avatar' => $user->avatar,
            'timezone' => $user->timezone,
            'provider' => $user->provider,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'slug' => $user->tenant->slug,
            ] : null,
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
