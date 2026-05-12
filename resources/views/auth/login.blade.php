<x-guest-layout>
    <div class="card-header-block">
        <div style="font-size:32px;margin-bottom:10px;">🔐</div>
        <div class="card-title">Selamat Datang</div>
        <div class="card-subtitle">Masuk ke Ocean Plastic Intelligence System<br>untuk memulai analisis data</div>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">
            <span>✓</span> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon">📧</span>
                <input
                    id="email"
                    class="form-control"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="masukkan email kamu"
                >
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
                <input
                    id="password"
                    class="form-control"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember + Forgot -->
        <div class="row-between">
            <label class="check-row">
                <input type="checkbox" name="remember">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Lupa password?</a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-submit">
            🌊 Masuk ke Dashboard
        </button>

        <!-- Register link -->
        @if (Route::has('register'))
            <div class="form-divider">atau</div>
            <div class="center-text">
                <span style="font-size:13px;color:var(--text3);">Belum punya akun? </span>
                <a href="{{ route('register') }}" class="link">Daftar sekarang</a>
            </div>
        @endif
    </form>
</x-guest-layout>