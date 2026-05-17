<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <p class="pb-8 font-bold text-base">パスワードをお忘れの方</p>
        <p class="pb-6">ご登録内容を確認いたしますので、メールアドレスをご入力ください。<br>
        新しいパスワードを設定するための再発行リンクをメールでお送りします。</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('パスワード再設定用のメールを送信する') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
