<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">System Settings</h2>
                    <p class="text-sm text-gray-500">Configure system preferences and security</p>
                </div>
                {{-- Global Save Button --}}
                <button type="submit" form="settings-form" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Changes
                </button>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form id="settings-form" action="{{ route('dashboard.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- CARD 1: ELECTION SETTINGS --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Election Settings</h3>
                            <p class="text-sm text-gray-500">Default election configurations</p>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            {{-- Setting: Allow Vote Changes --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="allow_vote_changes" class="font-medium text-gray-900 cursor-pointer">Allow Vote Changes</label>
                                    <p class="text-sm text-gray-500">Let voters change their vote before deadline</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="allow_vote_changes" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="allow_vote_changes" id="allow_vote_changes" value="1" class="sr-only peer" 
                                            {{ $settings['allow_vote_changes']->value == '1' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- Setting: Real-time Results --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="real_time_results" class="font-medium text-gray-900 cursor-pointer">Real-time Results</label>
                                    <p class="text-sm text-gray-500">Show live vote counts to public</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="real_time_results" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="real_time_results" id="real_time_results" value="1" class="sr-only peer"
                                            {{ $settings['real_time_results']->value == '1' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: SECURITY SETTINGS --}}
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                            <p class="text-sm text-gray-500">System security configurations</p>
                        </div>

                        <div class="p-6 space-y-6">
                            {{-- Setting: 2FA --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="require_2fa" class="font-medium text-gray-900 cursor-pointer">Two-Factor Authentication</label>
                                    <p class="text-sm text-gray-500">Require 2FA for all admin accounts</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="require_2fa" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="require_2fa" id="require_2fa" value="1" class="sr-only peer"
                                            {{ $settings['require_2fa']->value == '1' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- Setting: Session Timeout --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="session_timeout" class="font-medium text-gray-900">Session Timeout (Minutes)</label>
                                    <p class="text-sm text-gray-500">Auto logout after inactivity</p>
                                </div>
                                <div class="w-32">
                                    <select name="session_timeout" id="session_timeout" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="15" {{ $settings['session_timeout']->value == '15' ? 'selected' : '' }}>15 Mins</option>
                                        <option value="30" {{ $settings['session_timeout']->value == '30' ? 'selected' : '' }}>30 Mins</option>
                                        <option value="60" {{ $settings['session_timeout']->value == '60' ? 'selected' : '' }}>1 Hour</option>
                                        <option value="120" {{ $settings['session_timeout']->value == '120' ? 'selected' : '' }}>2 Hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>