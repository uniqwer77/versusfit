<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Челлендж: {{ $challenge->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">{{ $challenge->description }}</p>
                <div class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 p-4 rounded-lg font-bold">
                    🔥 Всего набегано участниками: {{ $totalDistance }} км
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                @if(!$isJoined)
                    <form action="{{ route('challenges.join', $challenge) }}" method="POST">
                        @csrf
                        <x-primary-button class="bg-green-600 hover:bg-green-700">
                            🎯 Вступить в челлендж
                        </x-primary-button>
                    </form>
                @else
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Добавить свой результат</h3>
                    <form action="{{ route('records.store') }}" method="POST" class="flex items-center space-x-4">
                        @csrf
                        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                        
                        <div>
                            <x-text-input type="number" step="0.01" name="value" placeholder="Дистанция (км)" required class="w-48"/>
                            <x-input-error :messages="$errors->get('value')" class="mt-2" />
                        </div>

                        <x-primary-button>
                            🚀 Добавить км
                        </x-primary-button>
                    </form>
                @endif
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">🏆 Таблица лидеров</h3>
                
                @if($members->isEmpty())
                    <p class="text-gray-500">В этом челлендже пока нет участников. Будь первым!</p>
                @else
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Место</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Участник</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Результат</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                                @foreach($members as $index => $member)
                                    <tr class="{{ $member->id === auth()->id() ? 'bg-yellow-50 dark:bg-yellow-900/30' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $member->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600 dark:text-blue-400">
                                            {{ $member->records_sum_value ?? 0 }} км
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>