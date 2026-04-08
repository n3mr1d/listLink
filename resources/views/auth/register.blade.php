<x-app.layouts title="Register">
    <div class="max-w-[450px] mx-auto py-12">
        <div class="text-center mb-10">
            <x-app.logo class="w-16 h-16 mx-auto mb-6 drop-shadow-[0_0_15px_rgba(88,166,255,0.2)]" />
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Create Account</h1>
            <p class="text-gh-dim text-sm">Join the network. Submit and manage links privately.</p>
        </div>

        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-8">
                <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-6">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="username" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Username *</label>
                        <div class="relative items-center flex">
                            <i class="fas fa-user absolute left-4 text-xs text-gh-dim/50"></i>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" 
                                placeholder="Choose your alias" required minlength="3" maxlength="20"
                                class="w-full bg-gh-bg border border-gh-border rounded-xl pl-10 pr-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                        </div>
                        <p class="text-[0.6rem] text-gh-dim/60 ml-1">3-20 characters. Must be unique.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Password *</label>
                        <div class="relative items-center flex">
                            <i class="fas fa-lock absolute left-4 text-xs text-gh-dim/50"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6"
                                class="w-full bg-gh-bg border border-gh-border rounded-xl pl-10 pr-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                        </div>
                        <p class="text-[0.6rem] text-gh-dim/60 ml-1">Minimum 6 characters. Use something strong.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-gh-accent text-gh-bg py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-400 active:scale-95 transition-all shadow-lg shadow-blue-500/10">
                            Register Identity
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-gh-dim text-xs">
                        Already registered? <a href="{{ route('login.form') }}" class="text-gh-accent font-bold no-underline hover:underline">Sign in to your account</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-4">
            <div class="bg-blue-500/5 border border-blue-500/20 rounded-xl p-6">
                <h4 class="text-[0.65rem] font-black text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-user-shield"></i> Privacy Guarantee
                </h4>
                <p class="text-xs text-gh-dim leading-relaxed m-0">
                    We do not require email addresses or any PII. Your account is tied only to your username. If you lose your password, there is no recovery option.
                </p>
            </div>
        </div>
    </div>
</x-app.layouts>