<x-app.layouts title="Register">
    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="flex flex-col gap-2">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
        </div>
        <button type="submit">Register</button>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</x-app.layouts>
