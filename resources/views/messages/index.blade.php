@extends('layouts.app')

@section('title', 'Messages — Green Paw LMS')
@section('page_title', 'Messages')

@section('content')
    <div class="grid-2" style="grid-template-columns: 320px 1fr; align-items: start; min-height: 500px;">
        <!-- Conversation List -->
        <div class="card" style="height: 100%;">
            <div class="card-header">
                <h3 class="card-title">Conversations</h3>
            </div>

            <!-- New conversation button -->
            <div style="padding: 8px 12px; border-bottom: 1px solid var(--border);">
                <select id="newConvo" class="form-input form-select" style="font-size: 13px;"
                    onchange="if(this.value) window.location='/messages/'+this.value">
                    <option value="">+ New conversation...</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            @forelse($conversationData as $convo)
                <a href="{{ route('messages.conversation', $convo->partner) }}"
                    style="display: flex; align-items: center; gap: 10px; padding: 12px; border-bottom: 1px solid var(--border); text-decoration: none; background: {{ isset($activeUser) && $activeUser->id === $convo->partner->id ? 'rgba(52, 211, 153, 0.08)' : 'transparent' }};">
                    <div
                        style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                        {{ strtoupper(substr($convo->partner->name, 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 600; font-size: 13px;">{{ $convo->partner->name }}</span>
                            @if($convo->unread > 0)
                                <span
                                    style="background: var(--accent); color: #000; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 10px;">{{ $convo->unread }}</span>
                            @endif
                        </div>
                        <div
                            style="font-size: 12px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ Str::limit($convo->lastMessage->body ?? '', 40) }}
                        </div>
                    </div>
                </a>
            @empty
                <div style="padding: 32px; text-align: center; color: var(--text-muted); font-size: 13px;">No conversations yet.
                </div>
            @endforelse
        </div>

        <!-- Chat Area -->
        <div class="card" style="height: 100%; display: flex; flex-direction: column;">
            @if(isset($activeUser))
                <div class="card-header">
                    <h3 class="card-title">{{ $activeUser->name }}</h3>
                    <span style="font-size: 12px; color: var(--text-muted);">{{ $activeUser->email }}</span>
                </div>

                <!-- Messages -->
                <div id="chatMessages" style="flex: 1; overflow-y: auto; padding: 16px; max-height: 400px;">
                    @foreach($messages as $msg)
                        @php $isMine = $msg->sender_id === auth()->id(); @endphp
                        <div style="display: flex; justify-content: {{ $isMine ? 'flex-end' : 'flex-start' }}; margin-bottom: 8px;">
                            <div style="max-width: 70%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.4;
                                        background: {{ $isMine ? 'rgba(52, 211, 153, 0.15)' : 'var(--bg-input)' }};
                                        border-bottom-{{ $isMine ? 'right' : 'left' }}-radius: 2px;">
                                <div style="white-space: pre-wrap;">{{ $msg->body }}</div>
                                <div
                                    style="font-size: 10px; color: var(--text-muted); margin-top: 4px; text-align: {{ $isMine ? 'right' : 'left' }};">
                                    {{ $msg->created_at->format('h:i A') }}
                                    @if($isMine && $msg->read_at)
                                        <span style="color: var(--accent);">✓✓</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Send form -->
                <div style="border-top: 1px solid var(--border); padding: 12px;">
                    <form method="POST" action="{{ route('messages.send', $activeUser) }}" style="display: flex; gap: 8px;">
                        @csrf
                        <input type="text" name="body" class="form-input" placeholder="Type a message..." required autofocus
                            style="flex: 1;">
                        <button type="submit" class="btn btn-primary" style="width: auto; white-space: nowrap;">Send</button>
                    </form>
                </div>
            @else
                <div
                    style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 14px;">
                    Select a conversation or start a new one
                </div>
            @endif
        </div>
    </div>

    @if(isset($activeUser))
        <script>
            const chat = document.getElementById('chatMessages');
            if (chat) chat.scrollTop = chat.scrollHeight;
        </script>
    @endif
@endsection