<x-app-layout>
    <div class="max-w-2xl mx-auto py-8 px-4">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">New Partylist</h1>
            <p class="mt-1 text-[10px] text-slate-500 italic uppercase tracking-wider">Add a new political party for elections.</p>
        </div>

        <form action="{{ route('admin.partylists.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white shadow-xl rounded-3xl border border-slate-100 overflow-hidden">
            @csrf

            <div class="p-8 space-y-5">

                {{-- Logo Upload --}}
                <div class="space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Party Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 border-2 border-dashed border-sky-200 rounded-2xl overflow-hidden bg-slate-50 flex items-center justify-center relative">
                            <img id="logo-preview" class="w-full h-full object-cover hidden">
                            <span id="logo-placeholder" class="text-[8px] font-black text-sky-300 uppercase">Logo</span>
                            <input type="file" name="logo" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer"
                                   onchange="previewLogo(event)">
                        </div>
                        <p class="text-[9px] text-slate-400 italic">JPG, PNG, WEBP. Max 2MB.</p>
                    </div>
                    @error('logo') <p class="text-rose-500 text-[9px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Name --}}
                <div class="space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Party Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400"
                           placeholder="e.g., Sulo Party" required>
                    @error('name') <p class="text-rose-500 text-[9px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-1">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400"
                              placeholder="Brief description of the party...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 text-[9px] mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="bg-slate-50 px-8 py-5 border-t flex items-center justify-between">
                <a href="{{ route('admin.partylists.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-sky-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-sky-100 hover:bg-sky-700 transition active:scale-95">
                    Save Partylist
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>