<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Get all addresses for the authenticated user.
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Store a new address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_default'] = $request->boolean('is_default', false);

        // If this is the first address, make it default automatically
        $existingCount = Address::where('user_id', Auth::id())->count();
        if ($existingCount === 0) {
            $validated['is_default'] = true;
        }

        $address = Address::create($validated);

        Log::info('Address created', [
            'user_id' => Auth::id(),
            'address_id' => $address->id,
            'is_default' => $address->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anwani imehifadhiwa kwa ufanisi.',
            'address' => $address,
        ]);
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);

        $address->update($validated);

        Log::info('Address updated', [
            'user_id' => Auth::id(),
            'address_id' => $address->id,
            'is_default' => $address->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anwani imesasishwa kwa ufanisi.',
            'address' => $address,
        ]);
    }

    /**
     * Delete an address.
     */
    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        // Check if this is the default address
        if ($address->is_default) {
            $otherAddressesCount = Address::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->count();

            if ($otherAddressesCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hauwezi kufuta anwani ya mwisho. Ongeza anwani nyingine kwanza.',
                ], 400);
            }
        }

        $wasDefault = $address->is_default;
        $addressId = $address->id;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $newDefault = Address::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->first();
                
            if ($newDefault) {
                $newDefault->is_default = true;
                $newDefault->save();
                
                Log::info('New default address set after deletion', [
                    'user_id' => Auth::id(),
                    'new_default_id' => $newDefault->id,
                ]);
            }
        }

        Log::info('Address deleted', [
            'user_id' => Auth::id(),
            'address_id' => $addressId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anwani imefutwa kwa ufanisi.',
        ]);
    }

    /**
     * Set an address as default.
     */
    public function setDefault($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        // Check if already default
        if ($address->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Anwani hii tayari ni chaguo-msingi.',
            ], 400);
        }

        // Remove default from others
        Address::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->update(['is_default' => false]);

        $address->is_default = true;
        $address->save();

        Log::info('Default address changed', [
            'user_id' => Auth::id(),
            'new_default_id' => $address->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anwani imewekwa kama chaguo-msingi.',
            'address' => $address,
        ]);
    }

    /**
     * Remove default status from an address.
     * IMESAHIHISHWA: Method hii ilikuwa haipo - imeongezwa
     */
    public function removeDefault($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        // Check if this is the only address
        $totalAddresses = Address::where('user_id', Auth::id())->count();
        
        if ($totalAddresses <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Hauwezi kuondoa chaguo-msingi kwa anwani ya pekee.',
            ], 400);
        }

        // Check if already not default
        if (!$address->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Anwani hii si chaguo-msingi.',
            ], 400);
        }

        // Remove default status
        $address->is_default = false;
        $address->save();

        // Set another address as default (the most recent one)
        $newDefault = Address::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->orderByDesc('created_at')
            ->first();
            
        if ($newDefault) {
            $newDefault->is_default = true;
            $newDefault->save();
        }

        Log::info('Default address removed', [
            'user_id' => Auth::id(),
            'old_default_id' => $address->id,
            'new_default_id' => $newDefault->id ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chaguo-msingi kimeondolewa. Anwani nyingine imewekwa kama msingi.',
        ]);
    }
}