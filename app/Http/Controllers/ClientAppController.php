<?php

namespace App\Http\Controllers;

use App\Models\ClientApp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientAppController extends Controller
{
    public function index(Request $request)
    {
        $apps = $request->user()
            ->clientApps()
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $apps]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120'],
            'environment' => ['nullable', 'in:dev,staging,prod'],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        // Ensure unique per user
        $exists = ClientApp::where('user_id', $request->user()->id)
            ->where('slug', $slug)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Slug already exists for this user.'
            ], 422);
        }

        $app = ClientApp::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'environment' => $validated['environment'] ?? 'prod',
            'api_key' => Str::random(64),
        ]);

        return response()->json(['data' => $app], 201);
    }

    public function show(Request $request, ClientApp $app)
    {
        $this->authorizeApp($request, $app);

        return response()->json(['data' => $app]);
    }

    public function update(Request $request, ClientApp $app)
    {
        $this->authorizeApp($request, $app);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'slug' => ['sometimes', 'string', 'max:120'],
            'environment' => ['sometimes', 'in:dev,staging,prod'],
        ]);

        if (isset($validated['slug'])) {
            $exists = ClientApp::where('user_id', $request->user()->id)
                ->where('slug', $validated['slug'])
                ->where('id', '!=', $app->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Slug already exists for this user.'
                ], 422);
            }
        }

        $app->update($validated);

        return response()->json(['data' => $app]);
    }

    public function destroy(Request $request, ClientApp $app)
    {
        $this->authorizeApp($request, $app);

        $app->delete();

        return response()->json(['message' => 'Deleted.']);
    }

    private function authorizeApp(Request $request, ClientApp $app): void
    {
        if ($app->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }
}