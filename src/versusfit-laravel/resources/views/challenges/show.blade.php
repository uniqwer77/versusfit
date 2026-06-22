<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Челлендж: {{ $challenge->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Блок с описанием и статистикой --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">{{ $challenge->description }}</p>
                {{-- Кнопки управления для владельца --}}
                @if(auth()->check() && auth()->id() === $challenge->owner_id)
                    <div class="mt-4 flex items-center space-x-4">
                        <a href="{{ route('challenges.edit', $challenge) }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow transition">
                            Редактировать
                        </a>
                        <form action="{{ route('challenges.destroy', $challenge) }}" method="POST"
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот челлендж? Все результаты участников будут потеряны.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                Удалить
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Блок добавления результата --}}
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                @if(!$isJoined)
                    <form action="{{ route('challenges.join', $challenge) }}" method="POST">
                        @csrf
                        <x-primary-button class="bg-green-600 hover:bg-green-700">
                            Вступить в челлендж
                        </x-primary-button>
                    </form>
                @else
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Добавить свой результат</h3>
                    
                    <form id="add-record-form" action="{{ route('records.store') }}" method="POST" onsubmit="handleRecordSubmit(event)" class="flex items-center space-x-4">
                        @csrf
                        <input type="hidden" id="challenge_id" name="challenge_id" value="{{ $challenge->id }}">
                        
                        <div>
                            <x-text-input type="number" step="0.01" id="record-value" name="value" placeholder="Дистанция (км)" required class="w-48"/>
                            <x-input-error :messages="$errors->get('value')" class="mt-2" />
                        </div>

                        <x-primary-button type="submit">
                            Добавить км
                        </x-primary-button>
                    </form>
                @endif
            </div>

            <div id="react-leaderboard" data-challenge-id="{{ $challenge->id }}"></div>

            <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
            <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
            <script src="https://unpkg.com/@babel/standalone@7.13.12/babel.min.js"></script>
            <script type="text/babel" data-type="module" src="{{ asset('js/Leaderboard.jsx') }}"></script>

            <script>
                function handleRecordSubmit(event) {
                    event.preventDefault(); 

                    const form = event.target;
                    const challengeId = document.getElementById('challenge_id').value;
                    const value = document.getElementById('record-value').value;
                    const token = form.querySelector('input[name="_token"]').value;

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            challenge_id: challengeId,
                            value: value
                        })
                    })
                    .then(response => {
                        if (response.ok) {
                            document.getElementById('record-value').value = '';
                            console.log('Результат успешно добавлен!');
                        } else {
                            console.error('Ошибка при отправке результата');
                        }
                    })
                    .catch(error => console.error('Ошибка сети:', error));
                }
                </script>
        </div>
    </div>
</x-app-layout>
