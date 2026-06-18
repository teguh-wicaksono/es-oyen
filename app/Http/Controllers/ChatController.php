<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient' => ['required', 'string', 'max:20'],
            'message' => ['required', 'string'],
        ]);

        $recipientRole = strtolower($data['recipient']);
        abort_if($recipientRole === $request->user()->role, 422, 'Penerima chat tidak valid.');

        $recipient = User::query()
            ->where('role', $recipientRole)
            ->orderBy('id')
            ->firstOrFail();

        ChatMessage::query()
            ->where('from_user_id', $recipient->id)
            ->where('to_user_id', $request->user()->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $message = ChatMessage::query()->create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $recipient->id,
            'pesan' => $data['message'],
            'dibaca' => false,
        ]);

        return response()->json(['chat' => $this->serializeChat($message->load(['fromUser', 'toUser']))], 201);
    }

    public function markRead(Request $request): JsonResponse
    {
        ChatMessage::query()
            ->where('to_user_id', $request->user()->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $chats = ChatMessage::query()
            ->with(['fromUser', 'toUser'])
            ->where(function ($chatQuery) use ($request) {
                $chatQuery
                    ->where('from_user_id', $request->user()->id)
                    ->orWhere('to_user_id', $request->user()->id);
            })
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $message) => self::serializeChat($message))
            ->values();

        return response()->json(['chats' => $chats]);
    }

    public static function serializeChat(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender' => ucfirst($message->fromUser?->role ?? 'Sistem'),
            'recipient' => ucfirst($message->toUser?->role ?? 'Tim'),
            'message' => $message->pesan,
            'read' => (bool) $message->dibaca,
            'time' => $message->created_at?->timezone(config('app.timezone'))->format('H:i') ?? now()->format('H:i'),
            'createdAt' => $message->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
        ];
    }
}
