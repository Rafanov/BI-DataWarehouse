<x-guest-layout>
    <div class="card-header-block">
        <div style="font-size:32px;margin-bottom:10px;">🌊</div>
        <div class="card-title">Buat Akun</div>
        <div class="card-subtitle">Bergabung dengan Ocean Plastic Intelligence System</div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <div class="input-wrap">
                <span class="input-icon">👤</span>
                <input id="name" class="form-control" type="text" name="name"
                    value="{{ old('name') }}" required autofocus placeholder="nama kamu">
            </div>
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon">📧</span>
                <input id="email" class="form-control" type="email" name="email"
                    value="{{ old('email') }}" required placeholder="email@example.com">
            </div>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">🔑</span>
                <input id="password" class="form-control" type="password" name="password"
                    required autocomplete="new-password" placeholder="min. 8 karakter">
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input id="password_confirmation" class="form-control" type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="ulangi password">
            </div>
            @error('password_confirmation')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            🌊 Daftar Sekarang
        </button>

        <div class="form-divider">atau</div>
        <div class="center-text">
            <span style="font-size:13px;color:var(--text3);">Sudah punya akun? </span>
            <a href="{{ route('login') }}" class="link">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>