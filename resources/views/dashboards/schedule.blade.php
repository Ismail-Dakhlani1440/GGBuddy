@extends('layouts.dashboard', ['title' => 'Schedule'])

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;" x-data="{ tab: 'weekly' }">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.02em;">Schedule</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:3px;">Set your weekly availability and time-off blocks.</p>
        </div>
        <div style="display:flex;background:var(--surface2);border-radius:100px;padding:4px;border:1px solid var(--border);">
            <button @click="tab='weekly'" :style="tab==='weekly' ? 'background:var(--accent);color:#fff;box-shadow:0 2px 8px rgba(124,58,237,0.4);' : 'color:var(--text-2);'" style="padding:7px 20px;border-radius:100px;font-size:12px;font-weight:700;border:none;cursor:pointer;font-family:Inter,sans-serif;transition:all 0.15s;">
                Weekly Routine
            </button>
            <button @click="tab='off'" :style="tab==='off' ? 'background:var(--accent);color:#fff;box-shadow:0 2px 8px rgba(124,58,237,0.4);' : 'color:var(--text-2);'" style="padding:7px 20px;border-radius:100px;font-size:12px;font-weight:700;border:none;cursor:pointer;font-family:Inter,sans-serif;transition:all 0.15s;">
                Time Off
            </button>
        </div>
    </div>

    <div class="side-col">

        {{-- Forms --}}
        <div>
            {{-- Weekly form --}}
            <div x-show="tab==='weekly'" x-transition class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:18px;">Add Weekly Slot</p>
                <form action="{{ route('ebuddy.schedule.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Day of Week</label>
                        <select name="day_of_week" class="form-input">
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Start</label>
                            <input type="time" name="start_time" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">End</label>
                            <input type="time" name="end_time" class="form-input" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Add Slot</button>
                </form>
            </div>

            {{-- Time off form --}}
            <div x-show="tab==='off'" x-transition class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:18px;">Block Time Off</p>
                <form action="{{ route('ebuddy.unavailability.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reason (Optional)</label>
                        <input type="text" name="reason" class="form-input" placeholder="e.g. Tournament day">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Block Period</button>
                </form>
            </div>
        </div>

        {{-- Current entries --}}
        <div class="card" style="padding:24px;min-height:300px;">
            <p class="section-title" style="margin-bottom:16px;" x-text="tab==='weekly' ? 'Weekly Routine' : 'Scheduled Breaks'"></p>

            {{-- Weekly slots --}}
            <div x-show="tab==='weekly'" x-cloak style="display:flex;flex-direction:column;gap:8px;">
                @forelse($schedules as $slot)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);" onmouseover="this.querySelector('.del-btn').style.opacity='1'" onmouseout="this.querySelector('.del-btn').style.opacity='0'">
                    <div>
                        <p style="font-size:14px;font-weight:600;color:var(--text);">{{ $slot->day_of_week }}</p>
                        <p style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</p>
                    </div>
                    <form action="{{ route('ebuddy.schedule.destroy', $slot) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm del-btn" style="opacity:0;transition:opacity 0.2s;font-size:11px;padding:5px 12px;">Remove</button>
                    </form>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:var(--text-2);">No weekly slots added yet.</div>
                @endforelse
            </div>

            {{-- Time off blocks --}}
            <div x-show="tab==='off'" x-cloak style="display:flex;flex-direction:column;gap:8px;">
                @forelse($unavailabilities as $block)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);" onmouseover="this.querySelector('.del-btn').style.opacity='1'" onmouseout="this.querySelector('.del-btn').style.opacity='0'">
                    <div>
                        <p style="font-size:14px;font-weight:600;color:var(--text);">{{ $block->reason ?? 'Unavailable' }}</p>
                        <p style="font-size:12px;color:var(--text-2);margin-top:2px;">
                            {{ \Carbon\Carbon::parse($block->start_datetime)->format('M d, H:i') }} — {{ \Carbon\Carbon::parse($block->end_datetime)->format('M d, H:i') }}
                        </p>
                    </div>
                    <form action="{{ route('ebuddy.unavailability.destroy', $block) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm del-btn" style="opacity:0;transition:opacity 0.2s;font-size:11px;padding:5px 12px;">Remove</button>
                    </form>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:var(--text-2);">No time-off blocks scheduled.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
