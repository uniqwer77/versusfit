<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Все челленджи') }}
            </h2>
            <a href="{{ route('challenges.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-950 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                + Создать челлендж
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($challenges as $challenge)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <a href="{{ route('challenges.show', $challenge) }}" class="hover:underline text-blue-600">
                                    {{ $challenge->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($challenge->description, 150, '...') }}
                            </p>
                        </div>
                        
                        <div class="border-t pt-4 text-xs text-gray-500 space-y-1">
                            <div><strong>Организатор:</strong> {{ $challenge->owner->name }}</div>
                            <div><strong>Старт:</strong> {{ \Carbon\Carbon::parse($challenge->start_date)->format('d.m.Y') }}</div>
                            <div><strong>Финиш:</strong> {{ \Carbon\Carbon::parse($challenge->end_date)->format('d.m.Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 col-span-full text-center text-gray-500">
                        Челленджей пока нет. Будь первым, кто его создаст!
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $challenges->links() }}
            </div>
        </div>
    </div>
</x-app-layout>