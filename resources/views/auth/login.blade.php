<x-guest-layout>

    @if(session('status'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#065f46;font-size:13px">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">البريد الإلكتروني</label>
            <input id="email" class="form-input" type="email" name="email"
                value="{{ old('email') }}" required autofocus
                placeholder="أدخل بريدك الإلكتروني">
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">كلمة المرور</label>
            <input id="password" class="form-input" type="password" name="password"
                required placeholder="أدخل كلمة المرور">
            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="remember-row">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">تذكرني</label>
        </div>

        <button type="submit" class="btn-login">دخول ←</button>
    </form>

</x-guest-layout>
