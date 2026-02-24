<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\RoleResource;
use App\Mcp\Resources\UserResource;
use App\Mcp\Tools\AssignRoleTool;
use App\Mcp\Tools\CreateCourseTool;
use App\Mcp\Tools\CreateUserTool;
use App\Mcp\Tools\ListCoursesTool;
use App\Mcp\Tools\ListLessonsTool;
use App\Mcp\Tools\ListRolesTool;
use App\Mcp\Tools\ListUsersTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Green Paw LMS')]
#[Version('1.0.0')]
#[Instructions('LMS server for managing users, courses, roles, and analytics. Use tools to list/create users, manage roles, and manage courses. Use resources to read detailed user and role data.')]
class LmsServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        ListUsersTool::class,
        CreateUserTool::class,
        AssignRoleTool::class,
        ListRolesTool::class,
        ListCoursesTool::class,
        CreateCourseTool::class,
        ListLessonsTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        UserResource::class,
        RoleResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
