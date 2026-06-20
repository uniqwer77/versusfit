<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $challenge->title }}
            </h2>
            <a href="{{ route('challenges.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Назад к списку</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-blue-600 bg-blue-200 uppercase last:mr-0 mr-1">
                        Организатор: {{ $challenge->owner->name }}
                    </span>
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-gray-600 bg-gray-200 uppercase last:mr-0 mr-1">
                        Период: {{ \Carbon\Carbon::parse($challenge->start_date)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($challenge->end_date)->format('d.m.Y') }}
                    </span>
                </div>

                <div class="text-gray-700 whitespace-pre-line mb-8">
                    {{ $challenge->description ?? 'Описание отсутствует.' }}
                </div>

                @can('update', $challenge)
                    <div class="border-t pt-4 flex items-center space-x-4 justify-end">
                        <a href="{{ route('challenges.edit', $challenge) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition ease-in-out duration-150">
                            Редактировать
                        </a>

                        <form action="{{ route('challenges.destroy', $challenge) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот челлендж?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                                Удалить
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

            </div>
    </div>
</x-app-layout>