@extends('layouts.dashboard', ['title' => 'Schedule'])

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;" x-data="{ tab: 'weekly' }">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.02em;">Schedule</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:3px;">Set your weekly availability and time-off blocks.</p>
        </div>

        {{-- Clean Underline Tabs (Moved to the right) --}}
        <div style="display:flex;align-items:center;gap:24px;border-bottom:1px solid var(--border);">
            <button @click="tab='weekly'" 
                    :style="tab==='weekly' ? 'color:var(--text);border-bottom:2px solid var(--accent);' : 'color:var(--text-2);border-bottom:2px solid transparent;'" 
                    style="padding-bottom:12px;font-size:14px;font-weight:600;background:none;border:none;border-top:2px solid transparent;cursor:pointer;transition:all 0.2s ease;">
                Weekly Routine
            </button>
            <button @click="tab='off'" 
                    :style="tab==='off' ? 'color:var(--text);border-bottom:2px solid var(--accent);' : 'color:var(--text-2);border-bottom:2px solid transparent;'" 
                    style="padding-bottom:12px;font-size:14px;font-weight:600;background:none;border:none;border-top:2px solid transparent;cursor:pointer;transition:all 0.2s ease;">
                Time Off
            </button>
        </div>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:24px;align-items:flex-start;">
        
        {{-- Forms (60%) --}}
        <div style="flex:6;min-width:320px;display:flex;flex-direction:column;gap:24px;">
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
                            @error('start_time') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">End</label>
                            <input type="time" name="end_time" class="form-input" required>
                            @error('end_time') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
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
                        @error('start_datetime') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" class="form-input" required>
                        @error('end_datetime') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reason (Optional)</label>
                        <input type="text" name="reason" class="form-input" placeholder="e.g. Tournament day">
                        @error('reason') <span style="color:var(--danger);font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Block Period</button>
                </form>
            </div>
        </div>

        {{-- Current entries (40%) --}}
        <div class="card" style="flex:4;min-width:320px;padding:32px;min-height:300px;">
            <p class="section-title" style="margin-bottom:24px;" x-text="tab==='weekly' ? 'Weekly Routine' : 'Scheduled Breaks'"></p>

            {{-- Weekly slots --}}
            <div x-show="tab==='weekly'" x-cloak style="display:flex;flex-direction:column;gap:20px;">
                @forelse($schedules as $slot)
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);" onmouseover="this.querySelector('.del-btn').style.opacity='1'" onmouseout="this.querySelector('.del-btn').style.opacity='0'">
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <p style="font-size:14px;font-weight:700;color:var(--text);width:90px;">{{ $slot->day_of_week }}</p>
                        <p style="font-size:13px;color:var(--text-2);">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} — {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</p>
                    </div>
                    <form action="{{ route('ebuddy.schedule.destroy', $slot) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm del-btn" style="opacity:0;transition:opacity 0.2s;font-size:12px;padding:6px 14px;">Remove</button>
                    </form>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:var(--text-2);">No weekly slots added yet.</div>
                @endforelse
            </div>

            {{-- Time off blocks --}}
            <div x-show="tab==='off'" x-cloak style="display:flex;flex-direction:column;gap:16px;">
                @forelse($unavailabilities as $block)
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);" onmouseover="this.querySelector('.del-btn').style.opacity='1'" onmouseout="this.querySelector('.del-btn').style.opacity='0'">
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <p style="font-size:14px;font-weight:700;color:var(--text);min-width:120px;">{{ $block->reason ?? 'Unavailable' }}</p>
                        <p style="font-size:13px;color:var(--text-2);">
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
