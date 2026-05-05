@extends('layouts.dashboard', ['title' => 'Orders'])

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;" x-data="{ tab: 'all' }">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.02em;">Orders</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:3px;">Manage your incoming session requests.</p>
        </div>

        {{-- Clean Underline Tabs (Moved to the right) --}}
        <div style="display:flex;align-items:center;gap:24px;border-bottom:1px solid var(--border);overflow-x:auto;">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Active', 'refused' => 'History'] as $val => $label)
            <button @click="tab='{{ $val }}'"
                    :style="tab==='{{ $val }}' ? 'color:var(--text);border-bottom:2px solid var(--accent);' : 'color:var(--text-2);border-bottom:2px solid transparent;'"
                    style="padding-bottom:12px;font-size:14px;font-weight:600;background:none;border:none;border-top:2px solid transparent;cursor:pointer;transition:all 0.2s ease;white-space:nowrap;">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
        @forelse($orders as $order)
        <div x-show="tab === 'all' || tab === '{{ $order->status }}'" x-cloak
             class="card" style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">

            {{-- Player Info --}}
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;border-radius:12px;overflow:hidden;border:1px solid var(--border-2);flex-shrink:0;">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $order->player->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <h3 style="font-size:14px;font-weight:700;">{{ $order->player->display_name ?? $order->player->name }}</h3>
                        <span style="padding:2px 10px;border-radius:100px;font-size:10px;font-weight:700;letter-spacing:0.06em;
                            {{ $order->status === 'pending' ? 'background:rgba(245,158,11,0.1);color:var(--yellow);border:1px solid rgba(245,158,11,0.2);' : '' }}
                            {{ $order->status === 'confirmed' ? 'background:rgba(34,197,94,0.1);color:var(--green);border:1px solid rgba(34,197,94,0.2);' : '' }}
                            {{ $order->status === 'refused' ? 'background:rgba(239,68,68,0.1);color:var(--red);border:1px solid rgba(239,68,68,0.2);' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p style="font-size:12px;color:var(--text-2);">
                        {{ $order->service->title }} &nbsp;·&nbsp;
                        <strong style="color:var(--text);">${{ number_format($order->total_price, 2) }}</strong>
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($order->isPending())
                    <form action="{{ route('ebuddy.orders.accept', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Accept</button>
                    </form>

                    {{-- Refuse modal --}}
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="btn btn-danger btn-sm">Refuse</button>
                        <div x-show="open" style="position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);" @click.away="open=false">
                            <div class="card" style="padding:28px;width:100%;max-width:440px;" @click.stop>
                                <h3 style="font-size:1.1rem;font-weight:800;margin-bottom:6px;">Refuse Order</h3>
                                <p style="font-size:13px;color:var(--text-2);margin-bottom:20px;">Give the player a reason (optional).</p>
                                <form action="{{ route('ebuddy.orders.refuse', $order) }}" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Reason</label>
                                        <textarea name="reason" class="form-input" rows="3" placeholder="Not available at this time..."></textarea>
                                    </div>
                                    <div style="display:flex;gap:10px;">
                                        <button type="button" @click="open=false" class="btn btn-ghost" style="flex:1;">Cancel</button>
                                        <button type="submit" class="btn btn-danger" style="flex:1;">Confirm Refusal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @elseif($order->status === 'confirmed')
                    <button class="btn btn-ghost btn-sm">Message Player</button>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:60px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
            <p style="color:var(--text-2);font-size:14px;">No orders found.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
