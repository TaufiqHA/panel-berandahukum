<div>
    <img
        src="{{ asset('logo-melinda.png') }}"
        alt="MelindaStore"
        style="width: 260px; height: auto; margin-bottom: 40px;"
    >

    <h2 style="font-weight: 400; color: #111827; margin: 0 0 6px; font-size: 26px;">
        Welcome to <span style="font-weight: 700;">Melindastore</span>
    </h2>
    <p style="color: #6b7280; margin: 0 0 28px; font-size: 14px;">Before you get started, you must login</p>

    <form wire:submit="authenticate">
        <div style="margin-bottom: 18px;">
            <label for="email" style="display: block; font-size: 13px; color: #111827; margin-bottom: 6px;">Email</label>
            <input
                id="email"
                type="email"
                wire:model="data.email"
                autofocus
                autocomplete="username"
                style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 10px 12px; font-size: 15px; color: #111827; background: #fff; box-sizing: border-box;"
            >
            @error('data.email')
                <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 18px;">
            <label for="password" style="display: block; font-size: 13px; color: #111827; margin-bottom: 6px;">Password</label>
            <input
                id="password"
                type="password"
                wire:model="data.password"
                autocomplete="current-password"
                style="width: 100%; border: 1px solid #cbd5e1; border-radius: 4px; padding: 10px 12px; font-size: 15px; color: #111827; background: #fff; box-sizing: border-box;"
            >
            @error('data.password')
                <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 26px;">
            <input id="remember-me" type="checkbox" wire:model="data.remember" style="width: 16px; height: 16px;">
            <label for="remember-me" style="font-size: 14px; color: #111827;">Remember Me</label>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="#" style="color: #6366f1; font-size: 14px; text-decoration: none;">Forgot Password?</a>
            <button
                type="submit"
                style="background: #6366f1; color: #ffffff; border: none; border-radius: 4px; padding: 10px 28px; font-size: 15px; cursor: pointer;"
            >
                Login
            </button>
        </div>
    </form>

    <div style="text-align: center; margin-top: 48px; color: #9ca3af; font-size: 12px;">
        Copyright &copy; Melindastore.
    </div>
</div>
