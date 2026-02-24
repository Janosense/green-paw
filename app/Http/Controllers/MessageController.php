<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get unique conversations (latest message per partner)
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($m) => $m->sender_id === $user->id ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $partners = User::whereIn('id', $conversations)->get()->keyBy('id');

        // Get last message and unread count per conversation
        $conversationData = $conversations->map(function ($partnerId) use ($user, $partners) {
            $partner = $partners[$partnerId] ?? null;
            if (!$partner)
                return null;

            $lastMessage = Message::where(function ($q) use ($user, $partnerId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $partnerId);
            })->orWhere(function ($q) use ($user, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $user->id);
            })->latest()->first();

            $unread = Message::where('sender_id', $partnerId)
                ->where('receiver_id', $user->id)
                ->unread()
                ->count();

            return (object) compact('partner', 'lastMessage', 'unread');
        })->filter();

        // All users for new conversation
        $allUsers = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('messages.index', compact('conversationData', 'allUsers'));
    }

    public function conversation(User $user)
    {
        $me = Auth::user();

        // Mark incoming messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->unread()
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
        })->orderBy('created_at')->get();

        // Conversations list for sidebar
        $conversations = Message::where('sender_id', $me->id)
            ->orWhere('receiver_id', $me->id)
            ->latest()
            ->get()
            ->map(fn($m) => $m->sender_id === $me->id ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $partners = User::whereIn('id', $conversations)->get()->keyBy('id');
        $conversationData = $conversations->map(function ($partnerId) use ($me, $partners) {
            $partner = $partners[$partnerId] ?? null;
            if (!$partner)
                return null;

            $lastMessage = Message::where(function ($q) use ($me, $partnerId) {
                $q->where('sender_id', $me->id)->where('receiver_id', $partnerId);
            })->orWhere(function ($q) use ($me, $partnerId) {
                $q->where('sender_id', $partnerId)->where('receiver_id', $me->id);
            })->latest()->first();

            $unread = Message::where('sender_id', $partnerId)
                ->where('receiver_id', $me->id)
                ->unread()
                ->count();

            return (object) compact('partner', 'lastMessage', 'unread');
        })->filter();

        $allUsers = User::where('id', '!=', $me->id)->orderBy('name')->get();

        return view('messages.index', [
            'conversationData' => $conversationData,
            'allUsers' => $allUsers,
            'activeUser' => $user,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, User $user)
    {
        $request->validate(['body' => 'required|string|max:5000']);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.conversation', $user);
    }
}
