<x-app.layouts title="Login">
    <form action="{{ route('login.store') }}" method="POST">
        @csrf

        <div class="flex flex-col gap-4">
            @error('login')
                <div class="text-red-500 mb-4">
                    {{ $message }}
                </div>
            @enderror

            {{-- Username --}}
            <div class="flex flex-col gap-2">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Username"
                    class="@error('login') invalidValidation @enderror" required>


            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-2">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password"
                    class="@error('login') invalidValidation @enderror" required>


            </div>

        </div>

        <button type="submit" class="mt-4">
            Login
        </button>
    </form>
</x-app.layouts>
