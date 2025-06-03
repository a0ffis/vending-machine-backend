<?php

namespace App\Http\Middleware;

use App\Models\VendingMachine;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMachineIdIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('x-api-key')) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        $machineId = $request->header('x-api-key');
        // Validate the machine ID format (e.g., UUID)
        if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $machineId)) {
            return response()->json(['error' => 'Invalid Machine ID format'], 400);
        }

        // Check if the machine ID exists in the database
        if (!VendingMachine::where('id', $machineId)->where('status', 'active')->exists()) {
            return response()->json(['error' => 'Machine ID not found'], 404);
        }

        return $next($request);
    }
}
