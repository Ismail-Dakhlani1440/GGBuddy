@extends('layouts.dashboard', ['title' => 'Edit Profile'])

@section('content')
<div style="max-width:780px;">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.3rem;font-weight:800;letter-spacing:-0.02em;">Edit Profile</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:4px;">Update your public identity.</p>
        </div>
        <a href="{{ route('profile') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Avatar + Identity --}}
        <div class="card" style="padding:28px;margin-bottom:14px;">
            <p class="section-title" style="margin-bottom:22px;">Identity</p>
            <div style="display:flex;align-items:flex-start;gap:28px;flex-wrap:wrap;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;flex-shrink:0;">
                    <div style="width:90px;height:90px;border-radius:18px;overflow:hidden;border:2px solid rgba(124,58,237,0.3);">
                        <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <label for="avatar" style="font-size:12px;font-weight:600;color:var(--accent-light);cursor:pointer;">Change Photo</label>
                    <input type="file" id="avatar" name="avatar" class="hidden" onchange="document.getElementById('avatar-preview').src=URL.createObjectURL(this.files[0]); this.form.submit();">
                    @error('avatar')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div style="flex:1;min-width:260px;display:flex;flex-direction:column;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="display_name">Display Name</label>
                        <input class="form-input" type="text" id="display_name" name="display_name"
                               value="{{ old('display_name', $user->display_name) }}" placeholder="Your gamer tag">
                        @error('display_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timezone">Timezone</label>
                        <select class="form-input" id="timezone" name="timezone">
                            @foreach(['UTC','CET','EST','PST','GMT'] as $tz)
                                <option value="{{ $tz }}" {{ $user->timezone == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                        @error('timezone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Banner & Bio (Only for E-Buddies) --}}
        @can('access-ebuddy-features')
        <div class="card" style="padding:28px;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:16px;">
                <p class="section-title" style="margin-bottom:0;">Banner</p>
                <label for="banner" style="font-size:12px;font-weight:600;color:var(--accent-light);cursor:pointer;background:rgba(124,58,237,0.1);padding:6px 12px;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background='rgba(124,58,237,0.2)'" onmouseout="this.style.background='rgba(124,58,237,0.1)'">Change Banner</label>
                <input type="file" id="banner" name="banner" accept="image/*" class="hidden" onchange="
                    if(this.files[0].size > 2 * 1024 * 1024) { 
                        alert('Your image is larger than the 2MB server limit. Please choose a smaller image.'); 
                        this.value = ''; 
                    } else { 
                        document.getElementById('banner-preview').src = URL.createObjectURL(this.files[0]); 
                        this.form.submit();
                    }
                ">
            </div>
            
            <div style="width:100%;height:140px;border-radius:12px;overflow:hidden;border:2px solid rgba(124,58,237,0.2);margin-bottom:8px;background:var(--surface2);">
                <img id="banner-preview" src="{{ $ebuddy->banner ? asset('storage/'.$ebuddy->banner) : 'https://placehold.co/1200x400/130b2e/7c3aed?text=Banner+Placeholder' }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <p style="font-size:12px;color:var(--text-2);margin-bottom:24px;">Recommended size: 1200x400 pixels or larger.</p>
            @error('banner')<p class="form-error" style="margin-top:-14px;margin-bottom:24px;">{{ $message }}</p>@enderror

            <p class="section-title" style="margin-bottom:16px;">Bio</p>
            <div class="form-group">
                <label class="form-label" for="bio">Tell players about your experience and playstyle</label>
                <textarea class="form-input" id="bio" name="bio" rows="5"
                          placeholder="e.g. Diamond support main. I specialize in helping AD carries improve their positioning...">{{ old('bio', $ebuddy->bio ?? '') }}</textarea>
                @error('bio')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        @endcan

        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('profile') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection
