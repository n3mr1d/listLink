<x-app.layouts title="Register">

    <div class="page-full">
        <div class="page-header">
            <h1>Register</h1>
            <p>Create an account to submit and manage links.</p>
        </div>

        <div class="card">
            <div class="card-header">Create Account</div>
            <div class="card-body">
                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username *</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            placeholder="Choose a username" required minlength="3" maxlength="20">
                        <div class="form-hint">3-20 characters. Must be unique.</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" name="password" id="password" placeholder="Choose a password" required
                            minlength="6">
                        <div class="form-hint">Minimum 6 characters.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </form>

                <p class="mt-2 text-center" style="font-size:0.85rem;">
                    Already have an account? <a href="{{ route('login.form') }}">Login here</a>
                </p>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body" style="font-size:0.85rem;color:var(--text-secondary);">
                <strong>Privacy Note:</strong> We do not require email addresses. Your account uses only a username and
                password.
                We store zero personal information beyond your chosen username and a securely hashed password.
            </div>
        </div>
    </div>

</x-app.layouts>