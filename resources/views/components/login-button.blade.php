<a href="{{ route('omni.login') }}"
   class="inline-flex items-center gap-2 px-5 py-2.5 bg-omni-500 hover:bg-omni-600
          text-white text-sm font-medium rounded-lg transition-colors
          focus:outline-none focus:ring-2 focus:ring-omni-500 focus:ring-offset-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    {{ config('omni-central-auth.client.button_label', 'Login dengan Akun Pusat') }}
</a>
