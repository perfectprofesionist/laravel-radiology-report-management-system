@extends('layouts.app')

@section("breadcrumb")
    {{-- Breadcrumb navigation showing the request modality and current page --}}
    <li class="breadcrumb-item"><a href="{{ route('request-listing.view', [$uuid]) }}">{{ $request->modality }} Case Issue</a></li>
    <li class="breadcrumb-item active" aria-current="page">Chat</li>
@endsection

@section('content')





{{-- Main chat interface container --}}
<div class="chat-box">
    {{-- Chat header displaying the modality case information --}}
    <div class="chat-header">
        {{ $request->modality }} Case Issue
    </div>

    {{-- Chat messages display area --}}
    <div id="chat-messages">

    </div>

    

    {{-- Chat input form with request ID for message handling --}}
    <div class="chat-footer" id="chat-box" data-request-id="{{ $uuid }}">
        <form id="chat-form">
            <label class="form-label mb-2">Type Your Message Here...</label>
            <textarea class="form-control message-input mb-3" id="chat-message"  placeholder="Type a message..."></textarea>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
         </form>
    </div>
</div>















@push('scripts')
<script>
$(function() {
    {{-- Initialize chat interface elements --}}
    const chatBox = $('#chat-box');
    const requestId = chatBox.data('request-id'); // Get request ID from data attribute
    const chatMessages = $('#chat-messages');
    const chatForm = $('#chat-form');
    const chatInput = $('#chat-message');

    {{-- Check if user is near bottom of chat messages for smart scrolling --}}
    function isUserNearBottom() {
        const threshold = 50; // px from bottom to consider "near"
        const scrollTop = chatMessages.scrollTop();
        const scrollHeight = chatMessages[0].scrollHeight;
        const clientHeight = chatMessages.innerHeight();
        return (scrollHeight - (scrollTop + clientHeight)) < threshold;
    }

    {{-- Scroll chat messages to the bottom --}}
    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    {{-- Load and display chat messages from server --}}
    function loadMessages() {
        // Save current scroll position relative to bottom
        const wasNearBottom = isUserNearBottom();

        $.ajax({
            url: '/chat/' + requestId + '/messages',
            method: 'GET',
            success: function(data) {
                chatMessages.html(data.html);
                // Only auto-scroll if user was already near bottom
                if (wasNearBottom) {
                    scrollToBottom();
                }
                // Otherwise keep the scroll position (don't force scroll)
            }
        });
    }

    {{-- Handle Enter key press in message input --}}
    chatInput.on('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // prevent newline
            chatForm.submit();  // trigger form submit
        }
    });

    {{-- Handle chat form submission --}}
    chatForm.on('submit', function(e) {
        e.preventDefault();
        const message = chatInput.val().trim();
        if (!message) return; // Don't send empty messages

        // Send message to server via AJAX
        $.ajax({
            url: '/chat/' + requestId + '/send',
            method: 'POST',
            data: {
                message: message,
                _token: '{{ csrf_token() }}' // CSRF protection
            },
            success: function() {
                chatInput.val(''); // Clear input field
                loadMessages(); // Reload messages after sending
            }
        });
    });

    {{-- Auto-refresh messages every 5 seconds for real-time updates --}}
    setInterval(loadMessages, 5000);
    
    {{-- Load initial messages when page loads --}}
    loadMessages();
});
</script>
@endpush


@endsection
