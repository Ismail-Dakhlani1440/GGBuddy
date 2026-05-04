@extends($layout, ['title' => 'My Orders'])

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;" x-data="{ tab: 'all' }">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.02em;">My Orders</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:3px;">Sessions you've booked with E-Buddies.</p>
        </div>
        <a href="{{ route('browse.index') }}" class="btn btn-primary btn-sm">Find an E-Buddy</a>
    </div>

    {{-- Filter tabs --}}
    <div style="display:flex;background:var(--surface);border-radius:100px;padding:4px;border:1px solid var(--border);gap:2px;width:fit-content;">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Active', 'refused' => 'Refused'] as $val => $label)
        <button @click="tab='{{ $val }}'"
                :style="tab==='{{ $val }}' ? 'background:var(--accent);color:#fff;box-shadow:0 2px 8px rgba(124,58,237,0.4);' : 'color:var(--text-2);'"
                style="padding:7px 18px;border-radius:100px;font-size:12px;font-weight:700;border:none;cursor:pointer;font-family:Inter,sans-serif;transition:all 0.15s;background:transparent;">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Orders list --}}
    <div style="display:flex;flex-direction:column;gap:10px;">
        @forelse($orders as $order)
        <div x-show="tab === 'all' || tab === '{{ $order->status }}'"
             class="card" style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">

            <div style="display:flex;align-items:center;gap:14px;">
                {{-- E-Buddy avatar --}}
                <div style="width:46px;height:46px;border-radius:12px;overflow:hidden;border:1px solid var(--border-2);flex-shrink:0;">
                    <img src="{{ $order->eBuddy->avatar ? asset('storage/'.$order->eBuddy->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$order->eBuddy->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                        <h3 style="font-size:14px;font-weight:700;">{{ $order->eBuddy->display_name ?? $order->eBuddy->name }}</h3>
                        <span style="padding:2px 10px;border-radius:100px;font-size:10px;font-weight:700;letter-spacing:0.06em;
                            {{ $order->status === 'pending'   ? 'background:rgba(245,158,11,0.1);color:var(--yellow);border:1px solid rgba(245,158,11,0.2);' : '' }}
                            {{ $order->status === 'confirmed' ? 'background:rgba(34,197,94,0.1);color:var(--green);border:1px solid rgba(34,197,94,0.2);' : '' }}
                            {{ $order->status === 'refused'   ? 'background:rgba(239,68,68,0.1);color:var(--red);border:1px solid rgba(239,68,68,0.2);' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p style="font-size:12px;color:var(--text-2);">
                        {{ $order->service->title }}
                        &nbsp;·&nbsp;
                        {{ $order->service->game->title }}
                        &nbsp;·&nbsp;
                        <strong style="color:var(--text);">${{ number_format($order->total_price, 2) }}</strong>
                    </p>
                    @if($order->status === 'refused' && $order->refusal_reason)
                    <p style="font-size:12px;color:var(--red);margin-top:4px;">Reason: {{ $order->refusal_reason }}</p>
                    @endif
                </div>
            </div>

            <div style="flex-shrink:0;">
                <p style="font-size:11px;color:var(--text-3);text-align:right;">{{ $order->created_at->diffForHumans() }}</p>
                @if($order->status === 'confirmed')
                    <button class="btn btn-ghost btn-sm" style="margin-top:8px;">Message</button>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:60px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
            <p style="color:var(--text-2);margin-bottom:16px;font-size:14px;">No orders yet. Find an E-Buddy to get started!</p>
            <a href="{{ route('browse.index') }}" class="btn btn-primary">Browse E-Buddies</a>
        </div>
        @endforelse
    </div>

</div>
@endsection
