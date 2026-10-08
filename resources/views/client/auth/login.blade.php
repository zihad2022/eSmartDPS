<x-client.layout.guest title="Login">
<main class="container">
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="auth-card">
        <div class="auth-header"><h2>Welcome Back</h2><p>Login to your account</p></div>
        <form method="POST" action="{{ route('client.authenticate') }}" id="loginForm">@csrf
            <div class="form-group">
                <label class="form-label" for="user_id">User ID</label>
                <input class="form-control" id="user_id" name="user_id" type="text" value="{{ old('user_id') }}" placeholder="Enter your user ID" required>
                @error('user_id')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" id="password" name="password" type="password" placeholder="••••••••" required>
                @error('password')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div class="remember-forgot">
                <label class="remember-me"><input type="checkbox" name="remember" checked><span>Remember me</span></label>
                <a href="{{ route('client.password.forgot') }}" class="forgot-pass">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary" id="loginSubmitBtn">Login Now</button>
        </form>
        <p class="auth-footer">Don't have an account? <a href="{{ route('client.register') }}" class="text-primary">Register</a></p>
    </div>
</main>
</x-client.layout.guest>
