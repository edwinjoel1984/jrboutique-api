<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Update the printer tunnel URL for a user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePrinterTunnelUrl(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'printer_tunnel_url' => 'nullable|string|max:255|url'
            ]);

            $user = User::findOrFail($id);

            $user->update([
                'printer_tunnel_url' => $request->printer_tunnel_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Printer tunnel URL updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'printer_tunnel_url' => $user->printer_tunnel_url
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the printer tunnel URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the authenticated user's printer tunnel URL.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMyPrinterTunnelUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'printer_tunnel_url' => 'nullable|string|max:255|url'
            ]);

            $user = $request->user();

            $user->update([
                'printer_tunnel_url' => $request->printer_tunnel_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Printer tunnel URL updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'printer_tunnel_url' => $user->printer_tunnel_url
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the printer tunnel URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
