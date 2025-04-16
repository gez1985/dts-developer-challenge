<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *     title="Task Management API",
 *     version="1.0.0",
 *     description="API documentation for the task management system."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Localhost API Server"
 * )
 */
class ApiDocController extends Controller {}
