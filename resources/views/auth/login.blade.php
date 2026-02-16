<x-app.layouts title="Login">

    <div class="page-full">
        <div class="page-header">
            <h1>Login</h1>
            <p>Access your account to manage submissions and track your links.</p>
        </div>

        <div class="card">
            <div class="card-header">Sign In</div>
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                            placeholder="Your username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <p class="mt-2 text-center" style="font-size:0.85rem;">
                    Don't have an account? <a href="{{ route('register.form') }}">Register here</a>
                </p>
            </div>
        </div>
    </div>

</x-app.layouts>