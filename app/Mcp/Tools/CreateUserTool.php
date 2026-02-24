<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Creates a new user in the LMS with a specified name, email, and role.')]
class CreateUserTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('users.create')) {
            return Response::error('Permission denied. You need the users.create permission.');
        }

        $email = $request->get('email');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            return Response::error("A user with email '{$email}' already exists.");
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $email,
            'password' => $request->get('password') ?? 'changeme123',
        ]);

        $role = $request->get('role') ?? 'student';
        $user->assignRole($role);

        return Response::text(json_encode([
            'success' => true,
            'message' => "User '{$user->name}' created successfully with role '{$role}'.",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
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
            'name' => $schema->string()
                ->description('Full name of the user.')
                ->required(),
            'email' => $schema->string()
                ->description('Email address for the user (must be unique).')
                ->required(),
            'password' => $schema->string()
                ->description('Password for the user. Defaults to "changeme123" if not provided.')
                ->optional(),
            'role' => $schema->string()
                ->description('Role to assign (e.g., student, instructor, admin). Defaults to "student".')
                ->optional(),
        ];
    }
}
