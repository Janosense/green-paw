<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists users in the LMS with optional filters by role, search query, or limit.')]
class ListUsersTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('users.view')) {
            return Response::error('Permission denied. You need the users.view permission.');
        }

        $query = User::with('roles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->role($role);
        }

        $limit = min((int) ($request->get('limit') ?? 20), 100);

        $users = $query->latest()->limit($limit)->get();

        $result = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
                'created_at' => $user->created_at->toISOString(),
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
        return [
            'search' => $schema->string()
                ->description('Search users by name or email.')
                ->optional(),
            'role' => $schema->string()
                ->description('Filter users by role name (e.g., student, instructor, admin).')
                ->optional(),
            'limit' => $schema->integer()
                ->description('Maximum number of users to return (default: 20, max: 100).')
                ->optional(),
        ];
    }
}
