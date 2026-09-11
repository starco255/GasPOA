@props(['messages' => [], 'orderId' => null, 'height' => '300px'])

<div class="chat-container border rounded-3 p-3 bg-light">
    <div class="chat-messages mb-3" style="height: {{ $height }}; overflow-y: auto;" id="chatMessages">
        @forelse($messages as $msg)
            <div class="d-flex {{ $msg['sender_id'] == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                <div class="p-2 rounded-3 {{ $msg['sender_id'] == Auth::id() ? 'bg-primary text-white' : 'bg-white border' }}" style="max-width: 75%;">
                    <div class="small">{{ $msg['message'] }}</div>
                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                        {{ $msg['sender_name'] ?? 'Mimi' }} • {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-chat-dots fs-1"></i>
                <p>Bado hakuna mazungumzo. Anza kuandika hapa chini.</p>
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('chat.send', $orderId) }}" class="d-flex gap-2">
        @csrf
        <input type="text" name="message" class="form-control" placeholder="Andika ujumbe..." required>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
    </form>
</div>