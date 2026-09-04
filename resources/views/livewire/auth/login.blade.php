<div class="flex items-center justify-center min-h-[calc(100vh-4rem)]">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-pink-600 mb-2">🌸 Bazar POS</h1>
            <p class="text-gray-500">Silakan login untuk mengakses kasir.</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" wire:model="username" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-3" required autofocus>
                @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" wire:model="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500 bg-gray-50 border p-3" required>
            </div>

            <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-colors mt-2">
                Masuk
            </button>
        </form>
    </div>
</div>
