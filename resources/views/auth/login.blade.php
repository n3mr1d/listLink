<x-app.layouts title="Login">
    <div class="max-w-[450px] mx-auto py-16">
        <div class="text-center mb-10">
            <x-app.logo class="w-16 h-16 mx-auto mb-6 drop-shadow-[0_0_15px_rgba(88,166,255,0.2)]" />
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Welcome Back</h1>
            <p class="text-gh-dim text-sm">Security starts with privacy. Access your dashboard.</p>
        </div>

        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-8">
                <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-6">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="username" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Username</label>
                        <div class="relative items-center flex">
                            <i class="fas fa-user absolute left-4 text-xs text-gh-dim/50"></i>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" 
                                placeholder="Enter your identity" required autofocus
                                class="w-full bg-gh-bg border border-gh-border rounded-xl pl-10 pr-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="text-xs font-bold text-gh-dim uppercase tracking-wider">Password</label>
                        </div>
                        <div class="relative items-center flex">
                            <i class="fas fa-lock absolute left-4 text-xs text-gh-dim/50"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                class="w-full bg-gh-bg border border-gh-border rounded-xl pl-10 pr-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-gh-accent text-gh-bg py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-400 active:scale-95 transition-all shadow-lg shadow-blue-500/10">
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-gh-dim text-xs">
                        Don't have an account? <a href="{{ route('register.form') }}" class="text-gh-accent font-bold no-underline hover:underline">Register anonymously</a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 bg-white/5 border border-gh-border/50 rounded-xl p-4 text-center">
            <p class="text-[0.65rem] text-gh-dim leading-relaxed m-0 italic">
                <i class="fas fa-shield-alt mr-1"></i> Data is encrypted and stored on offshore servers. No logs, no tracking.
            </p>
        </div>
    </div>
</x-app.layouts>