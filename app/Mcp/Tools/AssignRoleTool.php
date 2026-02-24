<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Spatie\Permission\Models\Role;

#[Description('Assigns or removes a role from a user in the LMS.')]
class AssignRoleTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('roles.manage')) {
            return Response::error('Permission denied. You need the roles.manage permission.');
        }

        $user = User::find($request->get('user_id'));
        if (!$user) {
            return Response::error('User not found.');
        }

        $roleName = $request->get('role');
        if (!Role::where('name', $roleName)->exists()) {
            return Response::error("Role '{$roleName}' does not exist.");
        }

        $action = $request->get('action') ?? 'assign';

        if ($action === 'remove') {
            $user->removeRole($roleName);
            $message = "Role '{$roleName}' removed from user '{$user->name}'.";
        } else {
            $user->assignRole($roleName);
            $message = "Role '{$roleName}' assigned to user '{$user->name}'.";
        }

        return Response::text(json_encode([
            'success' => true,
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->roles->pluck('name')->toArray(),
            ],
        ], JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()
                ->description('ID of the user to assign/remove the role from.')
                ->required(),
            'role' => $schema->string()
                ->description('Name of the role to assign or remove.')
                ->required(),
            'action' => $schema->string()
                ->description('Action to perform: "assign" (default) or "remove".')
                ->optional(),
        ];
    }
}
