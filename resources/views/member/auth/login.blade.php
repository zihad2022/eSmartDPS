<x-member.layout.guest title="Member Login">
<main class="container">
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="auth-card">
        <div class="auth-header">
            <h2>Member Login</h2>
            <p>Access your SMART DPS member account</p>
        </div>

        <form action="{{ route('member.authenticate') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="member_id">Member ID</label>
                <input class="form-control" name="member_id" type="text" id="member_id" value="{{ old('member_id') }}" placeholder="Enter your member ID" required autofocus>
                @error('member_id')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" name="password" type="password" id="password" placeholder="••••••••" required>
                @error('password')<span class="text-danger">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn btn-primary">Login as Member</button>
        </form>

        <p class="auth-footer">Organization owner or staff? <a href="{{ route('client.login') }}" class="text-primary">Client Login</a></p>
    </div>
</main>
</x-member.layout.guest>
