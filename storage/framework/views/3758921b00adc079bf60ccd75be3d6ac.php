<?php $__env->startSection('title', 'Mazungumzo'); ?>
<?php $__env->startSection('page-title', 'Mazungumzo na ' . $otherPartyName); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8 mx-auto">
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center">
                    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary btn-sm me-3 rounded-circle" style="width: 36px; height: 36px; padding: 0;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-chat-dots me-2 text-primary"></i>
                            <?php echo e($otherPartyName); ?>

                        </h5>
                        <p class="text-muted small mb-0">
                            Agizo #<?php echo e($order->order_number); ?> • 
                            <?php if($order->status == 'pending'): ?> Inasubiri
                            <?php elseif($order->status == 'accepted'): ?> Imekubaliwa
                            <?php elseif($order->status == 'picked_up'): ?> Imeshachukuliwa
                            <?php elseif($order->status == 'out_for_delivery'): ?> Njiani
                            <?php elseif($order->status == 'delivered'): ?> Imekamilika
                            <?php else: ?> Imefutwa
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if($otherPartyPhone): ?>
                    <div class="ms-auto">
                        <a href="tel:<?php echo e($otherPartyPhone); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-telephone"></i> Piga Simu
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <hr>
            </div>
            <div class="card-body">
                
                <div class="chat-messages" id="chatMessages">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // Determine if message is from current user
                            $isMine = $msg->sender_id == Auth::id();
                            $senderName = $isMine ? 'Wewe' : ($msg->sender->full_name ?? $otherPartyName);
                        ?>
                        
                        <?php if($isMine): ?>
                            
                            <div class="message-row message-row-right">
                                <div class="message-bubble message-bubble-mine">
                                    <div class="message-content"><?php echo e($msg->message); ?></div>
                                    <div class="message-meta">
                                        <span class="message-sender"><?php echo e($senderName); ?></span>
                                        <span class="message-time"><?php echo e($msg->created_at->format('H:i')); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            
                            <div class="message-row message-row-left">
                                <div class="message-bubble message-bubble-other">
                                    <div class="message-content"><?php echo e($msg->message); ?></div>
                                    <div class="message-meta">
                                        <span class="message-sender"><?php echo e($senderName); ?></span>
                                        <span class="message-time"><?php echo e($msg->created_at->format('H:i')); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-chat">
                            <i class="bi bi-chat-dots"></i>
                            <p class="mt-3">Bado hakuna mazungumzo.</p>
                            <p class="small">Anza mazungumzo kwa kutuma ujumbe hapa chini.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                
                <form method="POST" action="<?php echo e(route('chat.send', $order->id)); ?>" class="chat-form mt-4" id="chatForm">
                    <?php echo csrf_field(); ?>
                    <div class="chat-input-wrapper">
                        <input type="text" name="message" class="chat-input" id="messageInput" 
                               placeholder="Andika ujumbe..." required autocomplete="off" autofocus>
                        <button type="submit" class="chat-send-btn" id="sendMessageBtn">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        
        <div class="card border-0 shadow-sm rounded-4 mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Maelezo ya Agizo</h6>
                        <p class="mb-1"><strong>#<?php echo e($order->order_number); ?></strong></p>
                        <p class="mb-1">
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($item->quantity); ?>x <?php echo e($item->product->name ?? 'Bidhaa'); ?><br>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </p>
                        <p class="mb-0 fw-bold text-primary">Jumla: TZS <?php echo e(number_format($order->total_amount)); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Anwani ya Kufikishia</h6>
                        <p class="mb-1"><i class="bi bi-geo-alt me-1"></i> <?php echo e($order->delivery_address); ?></p>
                        <p class="mb-1"><i class="bi bi-truck me-1"></i> 
                            <?php if($order->urgency_level == 'urgent'): ?>
                                <span class="badge bg-danger">Haraka</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Kawaida</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ==================================================== */
    /* CHAT MESSAGES CONTAINER */
    /* ==================================================== */
    .chat-messages {
        background: linear-gradient(145deg, #f8fafc 0%, #eef2f6 100%);
        border-radius: 24px;
        padding: 1.5rem 1rem;
        height: 400px;
        overflow-y: auto;
        scroll-behavior: smooth;
        display: flex;
        flex-direction: column;
    }

    .chat-messages::-webkit-scrollbar {
        width: 5px;
    }
    
    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }

    /* ==================================================== */
    /* MESSAGE ROWS - ALIGNMENT */
    /* ==================================================== */
    .message-row {
        display: flex;
        margin-bottom: 16px;
        animation: messageIn 0.3s ease forwards;
    }

    .message-row-left {
        justify-content: flex-start;
    }

    .message-row-right {
        justify-content: flex-end;
    }

    /* ==================================================== */
    /* MESSAGE BUBBLES */
    /* ==================================================== */
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        word-wrap: break-word;
    }

    /* Ujumbe wa mpokeaji - Kushoto, Rangi Nyeupe */
    .message-bubble-other {
        background: white;
        border-bottom-left-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .message-bubble-other .message-content {
        color: #1e293b;
    }

    /* Ujumbe wangu - Kulia, Rangi ya Orange */
    .message-bubble-mine {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        border-bottom-right-radius: 4px;
    }

    .message-bubble-mine .message-content {
        color: white;
    }

    /* ==================================================== */
    /* MESSAGE CONTENT */
    /* ==================================================== */
    .message-content {
        font-size: 0.95rem;
        line-height: 1.5;
        word-wrap: break-word;
    }

    /* ==================================================== */
    /* MESSAGE META (Sender na Time) */
    /* ==================================================== */
    .message-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        font-size: 0.7rem;
    }

    .message-bubble-other .message-meta {
        color: #64748b;
    }

    .message-bubble-mine .message-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    .message-sender {
        font-weight: 600;
    }

    .message-time {
        margin-left: 12px;
    }

    /* ==================================================== */
    /* EMPTY CHAT STATE */
    /* ==================================================== */
    .empty-chat {
        text-align: center;
        color: #94a3b8;
        padding: 2rem;
        margin: auto;
    }

    .empty-chat i {
        font-size: 3rem;
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    /* ==================================================== */
    /* CHAT INPUT FORM */
    /* ==================================================== */
    .chat-form {
        margin-top: 1.5rem;
    }

    .chat-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        background: white;
        padding: 6px 6px 6px 20px;
        border-radius: 60px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1.5px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .chat-input-wrapper:focus-within {
        border-color: #FF6B35;
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.15);
    }

    .chat-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        padding: 14px 0;
        font-size: 1rem;
        color: #1A1A2E;
    }

    .chat-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .chat-send-btn {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .chat-send-btn i {
        font-size: 1.3rem;
        transition: transform 0.2s;
    }

    .chat-send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 18px rgba(255, 107, 53, 0.4);
    }

    .chat-send-btn:active {
        transform: scale(0.98);
    }

    .chat-send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* ==================================================== */
    /* ANIMATIONS */
    /* ==================================================== */
    @keyframes messageIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ==================================================== */
    /* RESPONSIVE */
    /* ==================================================== */
    @media (max-width: 576px) {
        .message-bubble {
            max-width: 85%;
            padding: 10px 14px;
        }
        
        .chat-input-wrapper {
            padding: 4px 4px 4px 16px;
        }
        
        .chat-input {
            padding: 12px 0;
            font-size: 0.95rem;
        }
        
        .chat-send-btn {
            width: 46px;
            height: 46px;
        }
        
        .chat-send-btn i {
            font-size: 1.1rem;
        }
        
        .chat-messages {
            height: 350px;
            padding: 1rem 0.75rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendMessageBtn');

        // Auto-scroll to bottom on load
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Handle form submission with AJAX
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (!message) return;
                
                const formData = new FormData(this);
                
                // Disable input and button
                sendBtn.disabled = true;
                messageInput.disabled = true;
                sendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const now = new Date();
                        const time = now.toLocaleTimeString('sw-TZ', { 
                            hour: '2-digit', 
                            minute: '2-digit',
                            timeZone: 'Africa/Dar_es_Salaam',
                            hour12: false 
                        });
                        
                        // Create new message HTML
                        const messageHtml = `
                            <div class="message-row message-row-right">
                                <div class="message-bubble message-bubble-mine">
                                    <div class="message-content">${escapeHtml(message)}</div>
                                    <div class="message-meta">
                                        <span class="message-sender">Wewe</span>
                                        <span class="message-time">${time}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remove empty state if exists
                        const emptyChat = chatMessages.querySelector('.empty-chat');
                        if (emptyChat) {
                            emptyChat.remove();
                        }
                        
                        // Add message to chat
                        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                        
                        // Auto-scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        
                        // Clear input
                        messageInput.value = '';
                    } else {
                        alert(data.message || 'Imeshindikana kutuma ujumbe.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Imeshindikana kutuma ujumbe. Tafadhali jaribu tena.');
                })
                .finally(() => {
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                    sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
                    messageInput.focus();
                });
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Auto-refresh chat every 10 seconds
        const orderId = <?php echo e($order->id); ?>;
        let lastMessageCount = <?php echo e(count($messages)); ?>;
        
        setInterval(function() {
            fetch(`/chat/messages/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > lastMessageCount) {
                        location.reload();
                    }
                })
                .catch(err => console.log('Chat refresh error:', err));
        }, 10000);
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\chat\index.blade.php ENDPATH**/ ?>