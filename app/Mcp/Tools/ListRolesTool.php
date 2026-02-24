<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Spatie\Permission\Models\Role;

#[Description('Lists all available roles in the LMS with their assigned permissions.')]
class ListRolesTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('roles.manage')) {
            return Response::error('Permission denied. You need the roles.manage permission.');
        }

        $roles = Role::with('permissions')->get();

        $result = $roles->map(function (Role $role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
                'users_count' => $role->users()->count(),
            ];
        })->toArray();

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
