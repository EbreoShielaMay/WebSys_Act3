<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Application') }} - Login</title>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <main>
        <section class="hero-section" style="min-height:100vh; display:flex; align-items:center;">
            <div class="section-container" style="max-width:48rem; margin:0 auto; width:100%; padding:4rem 1rem;">
                <div class="contact-form-container" style="max-width:36rem; margin:0 auto;">
                    <h2 class="section-title" style="text-align:center; margin-bottom:0.5rem;">Sign in to your account</h2>
                    <p class="section-description" style="text-align:center; margin-bottom:1.5rem; color:rgba(224,204,250,0.8);">Access your editable resume and profile settings.</p>

                    <form method="POST" action="{{ route('login') }}" class="contact-form">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                            @error('email') <div class="input-error" style="color:#ffb4c6;margin-top:0.5rem;">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group" style="margin-top:1rem;">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required placeholder="••••••••">
                            @error('password') <div class="input-error" style="color:#ffb4c6;margin-top:0.5rem;">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-row" style="display:flex; align-items:center; justify-content:space-between; margin-top:1rem;">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember">
                                <span style="margin-left:0.5rem; color:rgba(224,204,250,0.85);">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="underline" style="color:rgba(224,204,250,0.85);">Forgot your password?</a>
                            @endif
                        </div>

                        <button type="submit" class="form-submit-btn" style="margin-top:1.25rem;">{{ __('Log in') }}</button>

                        <div style="text-align:center; margin-top:1rem; color:rgba(224,204,250,0.8);">
                            @if (Route::has('register'))
                                <span>Don't have an account? <a href="{{ route('register') }}" class="underline">Register</a></span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script> if (typeof lucide !== 'undefined') try { lucide.createIcons(); } catch (e) {}</script>
</body>
</html>
