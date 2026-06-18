<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:180'],
            'detail' => ['nullable', 'string'],
            'meta' => ['nullable', 'array'],
        ]);

        $log = ActivityLog::query()->create([
            'user_id' => $request->user()?->id,
            'tipe' => $data['type'],
            'judul' => $data['title'],
            'detail' => $data['detail'] ?? null,
            'meta' => $data['meta'] ?? [],
        ]);

        return response()->json(['activity' => self::serialize($log->load('user'))], 201);
    }

    public static function serialize(ActivityLog $log): array
    {
        return [
            'id' => 'ACT-' . $log->id,
            'type' => $log->tipe,
            'title' => $log->judul,
            'detail' => $log->detail,
            'meta' => $log->meta ?? [],
            'actor' => $log->user?->name ?? 'Sistem',
            'time' => $log->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ];
    }
}
