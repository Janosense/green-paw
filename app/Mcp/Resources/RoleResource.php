<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Resource;
use Spatie\Permission\Models\Role;

#[Description('Retrieve role details including assigned permissions.')]
class RoleResource extends Resource
{
    /**
     * The URI template for this resource.
     */
    protected string $uriTemplate = 'role://{name}';

    /**
     * The MIME type of the resource.
     */
    protected string $mimeType = 'application/json';

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        $name = $request->get('name');
        $role = Role::with('permissions')->where('name', $name)->first();

        if (!$role) {
            return Response::error("Role '{$name}' not found.");
        }

        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
            'users_count' => $role->users()->count(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
