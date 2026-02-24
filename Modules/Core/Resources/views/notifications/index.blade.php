@extends('core::layouts.app')

@section('title', 'Notifications')
@section('heading', 'Notifications')

@section('content')
<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="wise-link hover:underline">← Back to Dashboard</a>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800 dark:text-slate-100">All notifications</h2>
        @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-read') }}">
            @csrf
            <button type="submit" class="text-sm wise-link">Mark all as read</button>
        </form>
        @endif
    </div>
    <ul class="divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($notifications as $n)
        <li class="px-4 py-3 {{ $n->read_at ? '' : 'bg-slate-50/50 dark:bg-slate-700/20' }}">
            <a href="{{ $n->read_at ? ($n->data['url'] ?? '#') : route('notifications.read', $n->id) }}" class="block">
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $n->data['message'] ?? 'Notification' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $n->created_at->format('M j, Y H:i') }} · {{ $n->created_at->diffForHumans() }}</p>
            </a>
        </li>
        @empty
        <li class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No notifications.</li>
        @endforelse
    </ul>
    @if($notifications->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
