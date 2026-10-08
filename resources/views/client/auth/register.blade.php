<x-client.layout.guest title="Register">
<main class="container">
    <div class="auth-card">
        <div class="auth-header"><h2>Create New Account</h2><p>Create your SMART DPS organization account</p></div>
        <form id="registrationForm" method="POST" action="{{ route('client.register.store') }}">@csrf
            <input type="hidden" name="package_id" value="{{ $package?->id }}">
            <div class="flex-gap-12">
                <div class="form-group flex-1"><label class="form-label">Organization Name</label><input class="form-control" name="organization_name" value="{{ old('organization_name') }}" placeholder="Organization name" required></div>
                <div class="form-group flex-1"><label class="form-label">Short Name</label><input class="form-control" name="short_name" value="{{ old('short_name') }}" placeholder="Short name" required></div>
            </div>
            <div class="flex-gap-12">
                <div class="form-group flex-1"><label class="form-label">Contact Email</label><input class="form-control" name="contact_email" type="email" value="{{ old('contact_email') }}" placeholder="organization@example.com" required></div>
                <div class="form-group flex-1"><label class="form-label">Contact Phone</label><input class="form-control" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="01XXXXXXXXX" required></div>
            </div>
            <div class="flex-gap-12">
                <div class="form-group flex-1"><label class="form-label">First Name</label><input class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required></div>
                <div class="form-group flex-1"><label class="form-label">Last Name</label><input class="form-control" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required></div>
            </div>
            <div class="form-group"><label class="form-label">Email</label><input class="form-control" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required></div>
            <div class="flex-gap-12">
                <div class="form-group flex-1"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" required></div>
                <div class="form-group flex-1"><label class="form-label">Password</label><input class="form-control" name="password" type="password" placeholder="••••••••" required></div>
            </div>
            @if($package)<div class="template-item"><div class="flex-between-mb0"><span class="text-xs-bold-muted">Package</span><strong>{{ $package->name }} · {{ $settings?->currency ?? '৳' }} {{ number_format($package->price) }}</strong></div></div>@endif
            <button class="btn btn-primary mt-20" type="submit">Create Account</button>
        </form>
        <p class="auth-footer">Already have an account? <a href="{{ route('client.login') }}" class="text-primary">Login</a></p>
    </div>
</main>
</x-client.layout.guest>
