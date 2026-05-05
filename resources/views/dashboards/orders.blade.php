@extends('layouts.dashboard', ['title' => 'Orders'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:20px;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">Manage Orders</h1>
            <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Track your session lifecycle from request to completion.</p>
        </div>

        @can('viewIncoming', App\Models\Order::class)
        <div style="display:flex;background:rgba(255,255,255,0.03);padding:4px;border-radius:12px;border:1px solid var(--border);">
            <a href="{{ route('orders', ['type' => 'incoming', 'status' => $status]) }}" 
               style="text-decoration:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;transition:all 0.2s;
               {{ $type === 'incoming' ? 'background:var(--accent);color:white;' : 'color:var(--text-2);' }}">
               Incoming
            </a>
            <a href="{{ route('orders', ['type' => 'outgoing', 'status' => $status]) }}" 
               style="text-decoration:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;transition:all 0.2s;
               {{ $type === 'outgoing' ? 'background:var(--accent);color:white;' : 'color:var(--text-2);' }}">
               Outgoing
            </a>
        </div>
        @endcan
    </div>

    {{-- Status Filters --}}
    <div style="display:flex;gap:10px;margin-bottom:24px;overflow-x:auto;padding-bottom:10px;scrollbar-width:none;">
        @php 
            $filterStatus = ['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'paid' => 'Paid', 'completed' => 'Completed', 'refused' => 'Refused', 'cancelled' => 'Cancelled'];
        @endphp
        @foreach($filterStatus as $key => $label)
            <a href="{{ route('orders', ['type' => $type, 'status' => $key]) }}" 
               class="btn {{ $status === $key ? 'btn-primary' : 'btn-ghost' }} btn-sm"
               style="border-radius:20px; white-space:nowrap; padding: 6px 16px; font-size:12px;">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="grid" style="grid-template-columns: 1fr; gap: 16px;">
        @forelse($orders as $order)
            <div class="card" style="padding:24px;display:flex;align-items:center;gap:24px;position:relative;" x-data="{ refuseOpen: false, payOpen: false, reviewOpen: false }">
                
                {{-- Other Party Info --}}
                @php 
                    $otherUser = ($type === 'incoming') ? $order->player : $order->eBuddy->user;
                @endphp
                <div style="display:flex;align-items:center;gap:16px;width:220px;flex-shrink:0;">
                    <div style="width:52px;height:52px;border-radius:14px;overflow:hidden;border:2px solid var(--border);">
                        <img src="{{ $otherUser->avatar ? asset('storage/'.$otherUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$otherUser->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:14px;font-weight:800;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $otherUser->display_name ?? $otherUser->name }}</p>
                        <p style="font-size:11px;color:var(--text-3);margin:0;text-transform:uppercase;font-weight:700;">{{ $type === 'incoming' ? 'Player' : 'E-Buddy' }}</p>
                    </div>
                </div>

                {{-- Service Info --}}
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="font-size:10px;background:rgba(124,58,237,0.1);color:var(--accent-light);padding:2px 8px;border-radius:4px;font-weight:800;">{{ $order->service->game->title }}</span>
                        <span style="font-size:12px;color:var(--text-2);font-weight:500;">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-size:14px;font-weight:600;margin:0;color:var(--text);">{{ $order->service->title }}</p>
                    @if($order->isRefused())
                        <p style="font-size:12px;color:var(--red);margin-top:4px;font-style:italic;">Reason: {{ $order->refuse_reason }}</p>
                    @endif
                </div>

                {{-- Price/Status --}}
                <div style="text-align:right;width:140px;flex-shrink:0;">
                    <p style="font-size:18px;font-weight:900;color:var(--text);margin:0;">${{ number_format($order->total_amount, 2) }}</p>
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-top:4px;">
                        @php 
                            $statusColors = [
                                'pending'   => ['#f59e0b', 'Pending'],
                                'confirmed' => ['#3b82f6', 'Confirmed'],
                                'refused'   => ['#ef4444', 'Refused'],
                                'paid'      => ['#10b981', 'Paid'],
                                'cancelled' => ['#9ca3af', 'Cancelled'],
                                'completed' => ['#7c3aed', 'Completed'],
                                'expired'   => ['#4b5563', 'Expired'],
                            ];
                            $stat = $statusColors[$order->status] ?? ['#9ca3af', 'Unknown'];
                        @endphp
                        <span style="width:6px;height:6px;background:{{ $stat[0] }};border-radius:50%;box-shadow:0 0 8px {{ $stat[0] }};"></span>
                        <span style="font-size:11px;font-weight:800;color:{{ $stat[0] }};text-transform:uppercase;">{{ $stat[1] }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="width:200px;flex-shrink:0;display:flex;justify-content:flex-end;align-items:center;gap:12px;">
                    <div style="display:flex; flex-direction:column; gap:6px; flex:1;">
                        @if($type === 'incoming')
                            @if($order->isPending())
                                <form action="{{ route('orders.accept', $order) }}" method="POST" style="width:100%;">@csrf<button type="submit" class="btn btn-primary btn-sm" style="width:100%; background:#10b981; border-color:#10b981;">Accept</button></form>
                                <button @click="refuseOpen = true" class="btn btn-ghost btn-sm" style="width:100%; color:#ef4444; font-size:11px;">Refuse</button>
                            @elseif($order->isPaid())
                                <form action="{{ route('orders.complete', $order) }}" method="POST" style="width:100%;">@csrf<button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Complete Session</button></form>
                            @endif
                        @else
                            @if($order->isConfirmed())
                                <button @click="payOpen = true" class="btn btn-primary btn-sm" style="width:100%; background:#10b981; border-color:#10b981;">Pay Now</button>
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" style="width:100%;">@csrf<button type="submit" class="btn btn-ghost btn-sm" style="width:100%; font-size:11px;">Cancel</button></form>
                            @elseif($order->isPending())
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" style="width:100%;">@csrf<button type="submit" class="btn btn-ghost btn-sm" style="width:100%; font-size:11px;">Cancel Request</button></form>
                            @elseif($order->isCompleted())
                                @if(!$order->isReviewed() && auth()->id() === $order->player_id)
                                    <button @click="reviewOpen = true" class="btn btn-primary btn-sm" style="width:100%; background:var(--accent); border-color:var(--accent);">Review Session</button>
                                @else
                                    <div style="display:flex; justify-content:flex-end;">
                                        <span class="status-badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px; display: flex; align-items: center;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:4px;"><path d="M5 13l4 4L19 7"></path></svg>
                                            Finished
                                        </span>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>

                    <a href="{{ route('chat.start', ['userId' => $otherUser->id]) }}" class="btn btn-ghost btn-sm" style="padding:10px; border-radius:12px; background:rgba(255,255,255,0.03); border:1px solid var(--border);"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg></a>
                </div>

                {{-- Teleport Modals to Body --}}
                <template x-teleport="body">
                    <div x-show="refuseOpen" x-cloak>
                        {{-- Backdrop --}}
                        <div style="position:fixed; inset:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(10px); z-index:99998;" @click="refuseOpen = false"></div>
                        {{-- Modal Card --}}
                        <div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:99999; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:32px; width:90%; max-width:400px;">
                            <h3 style="margin-bottom:8px; font-weight:800;">Refuse Order</h3>
                            <p style="font-size:13px; color:var(--text-2); margin-bottom:20px;">Provide a reason for refusing this request.</p>
                            <form action="{{ route('orders.refuse', $order) }}" method="POST" style="display:flex; flex-direction:column; gap:16px;">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">Reason</label>
                                    <textarea name="refuse_reason" class="form-input" rows="3" required placeholder="Reason for refusal..."></textarea>
                                </div>
                                <div style="display:flex; gap:10px;">
                                    <button type="button" @click="refuseOpen = false" class="btn btn-ghost" style="flex:1;">Cancel</button>
                                    <button type="submit" class="btn btn-primary" style="flex:1; background:var(--red); border-color:var(--red);">Confirm Refusal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-teleport="body">
                    <div x-show="payOpen" x-cloak>
                        {{-- Backdrop --}}
                        <div style="position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(12px); z-index:99998;" @click="payOpen = false"></div>
                        {{-- Modal Card --}}
                        <div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:99999; background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:40px; width:90%; max-width:450px;">
                            <div style="text-align:center; margin-bottom:24px;">
                                <div style="width:64px; height:64px; background:rgba(16,185,129,0.1); color:#10b981; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.646 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.646-1M12 16c-1.657 0-3-.895-3-2s1.343-2 3-2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"></path></svg>
                                </div>
                                <h3 style="font-size:1.4rem; font-weight:800; margin-bottom:8px;">Order Payment</h3>
                                <p style="font-size:14px; color:var(--text-2);">Complete your payment for <strong>{{ $order->service->title }}</strong></p>
                            </div>

                            <div style="background:rgba(255,255,255,0.02); border-radius:16px; padding:20px; border:1px solid var(--border); margin-bottom:32px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                    <span style="color:var(--text-3); font-size:14px;">Hours</span>
                                    <span style="font-weight:700;">{{ $order->hours }} hrs</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                    <span style="color:var(--text-3); font-size:14px;">Rate</span>
                                    <span style="font-weight:700;">${{ number_format($order->total_amount / $order->hours, 2) }}/hr</span>
                                </div>
                                <div style="height:1px; background:var(--border); margin:12px 0;"></div>
                                <div style="display:flex; justify-content:space-between;">
                                    <span style="font-weight:800;">Total Amount</span>
                                    <span style="font-size:20px; font-weight:900; color:var(--accent-light);">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>

                            <form action="{{ route('orders.pay', $order) }}" method="POST">
                                @csrf
                                <div style="display:flex; gap:12px;">
                                    <button type="button" @click="payOpen = false" class="btn btn-ghost" style="flex:1;">Back</button>
                                    <button type="submit" class="btn btn-primary" style="flex:1; background:#10b981; border-color:#10b981;">Confirm Payment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                <template x-teleport="body">
                    <div x-show="reviewOpen" x-cloak>
                        {{-- Backdrop --}}
                        <div style="position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(10px); z-index:99998;" @click="reviewOpen = false"></div>
                        {{-- Modal Card --}}
                        <div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:99999; background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:40px; width:90%; max-width:450px;">
                            <div style="text-align:center; margin-bottom:24px;">
                                <div style="width:64px; height:64px; background:rgba(124,58,237,0.1); color:var(--accent-light); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                </div>
                                <h3 style="font-size:1.4rem; font-weight:800; margin-bottom:8px;">Review Session</h3>
                                <p style="font-size:14px; color:var(--text-2);">How was your experience with <strong>{{ $order->eBuddy->user->display_name ?? $order->eBuddy->user->name }}</strong>?</p>
                            </div>

                            <form action="{{ route('orders.review', $order) }}" method="POST" x-data="{ rating: 5 }">
                                @csrf
                                <input type="hidden" name="rating" :value="rating">
                                
                                {{-- Star Rating --}}
                                <div style="display:flex; justify-content:center; gap:8px; margin-bottom:24px;">
                                    <template x-for="i in 5">
                                        <button type="button" @click="rating = i" style="background:none; border:none; cursor:pointer; padding:4px; transition:transform 0.2s;" :style="rating >= i ? 'color:var(--yellow); transform:scale(1.2);' : 'color:var(--text-3);'">
                                            <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                        </button>
                                    </template>
                                </div>

                                <div class="form-group" style="margin-bottom:24px;">
                                    <label class="form-label">Feedback (optional)</label>
                                    <textarea name="comment" class="form-input" rows="4" placeholder="What did you like? Anything to improve?"></textarea>
                                </div>

                                <div style="display:flex; gap:12px;">
                                    <button type="button" @click="reviewOpen = false" class="btn btn-ghost" style="flex:1;">Maybe Later</button>
                                    <button type="submit" class="btn btn-primary" style="flex:1;">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

            </div>
        @empty
            <div class="card" style="padding:80px 20px; text-align:center; background:rgba(255,255,255,0.01); border:2px dashed var(--border);">
                <h3 style="font-size:1.1rem; font-weight:800; margin-bottom:8px;">No {{ $status !== 'all' ? $status : '' }} {{ $type }} orders</h3>
                <p style="font-size:14px; color:var(--text-3); max-width:300px; margin:0 auto;">Your gaming journey starts here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
