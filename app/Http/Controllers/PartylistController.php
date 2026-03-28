<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Partylist;

class PartylistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:partylists,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partylists', 'public');
        }

        $partylist = Partylist::create([
            'name' => $request->name,
            'description' => $request->description,
            'logo_path' => $logoPath,
        ]);

        return response()->json([
            'success' => true,
            'name' => $partylist->name,
        ]);
    }
}