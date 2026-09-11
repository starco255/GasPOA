<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InterfacePreferenceController extends Controller
{
    /**
     * Store the signed-in user's display preferences.
     *
     * Guest preferences are kept in the browser until the visitor signs in.
     */
    public function update(Request $request): JsonResponse
    {
        $preferences = $request->validate([
            'interface_language' => ['required', 'in:sw,en'],
            'interface_theme' => ['required', 'in:light,dark'],
        ]);

        // A restored legacy database may not yet contain these optional columns.
        // In that case the browser cookies still save the choice, without changing
        // any existing table or data.
        if (Schema::hasColumns('users', ['interface_language', 'interface_theme'])) {
            $request->user()->forceFill($preferences)->save();
        }

        return response()
            ->json([
                'success' => true,
                'message' => 'Interface preferences saved.',
            ])
            ->cookie('gaspoa_language', $preferences['interface_language'], 60 * 24 * 365)
            ->cookie('gaspoa_theme', $preferences['interface_theme'], 60 * 24 * 365);
    }
}
