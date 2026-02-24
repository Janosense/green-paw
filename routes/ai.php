<?php

use App\Mcp\Servers\LmsServer;
use Laravel\Mcp\Facades\Mcp;

// Web server — accessible to remote AI clients via HTTP (Sanctum-protected)
Mcp::web('/mcp/lms', LmsServer::class)
    ->middleware(['auth:sanctum', 'throttle:mcp']);

// Local server — accessible via Artisan for local AI assistants
Mcp::local('lms', LmsServer::class);
