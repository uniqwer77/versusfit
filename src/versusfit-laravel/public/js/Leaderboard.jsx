function Leaderboard({ challengeId }) {
    const [records, setRecords] = React.useState([]);
    const [notifications, setNotifications] = React.useState([]);

    const totalDistance = records.reduce((sum, item) => sum + (item.challenge_value || 0), 0);

    React.useEffect(() => {
        fetch(`https://api.versusfit.ru/api/challenges/${challengeId}/leaderboard`)
            .then(res => {
                if (!res.ok) throw new Error('Ошибка сети');
                return res.json();
            })
            .then(data => {
                setRecords(data);
            })
            .catch(err => {
                console.error("Не удалось загрузить лидерборд:", err);
            });

        const ws = new WebSocket('wss://api.versusfit.ru/ws');
        
        ws.onmessage = (event) => {
            const message = JSON.parse(event.data);
            console.log("Пришло сообщение:", message);
            
            if (message.type === 'leaderboard_update' && message.challenge_id === challengeId) {
                setRecords(prev => {
                    const updated = prev.filter(s => s.user_id !== message.user_id);
                    updated.push(message);
                    return updated.sort((a, b) => b.challenge_value - a.challenge_value).slice(0, 10);
                });
                setNotifications(prev => [
                    `${message.name} теперь имеет ${message.challenge_value} км`,
                    ...prev
                ].slice(0, 5));
            }
            
            if (message.type === 'user_renamed') {
                setRecords(prev => prev.map(s => 
                    s.user_id === message.user_id 
                        ? { ...s, username: message.new_name } 
                        : s
                ));
            }

            if (message.type === 'user_joined' && message.challenge_id === challengeId) {
                setNotifications(prev => [
                    `${message.name} присоединился к челленджу!`,
                    ...prev
                ].slice(0, 5));
            }
        };

        return () => ws.close();
    }, [challengeId]); 

    return (
        <div className="leaderboard-box p-4 bg-white rounded shadow">
            <h3 className="text-xl font-bold mb-4 flex items-center">
                Живой Лидерборд
            </h3>
            <div className="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 p-4 rounded-lg font-bold mb-4">
                Всего набегано участниками: {totalDistance} км
            </div>

            {notifications.length > 0 && (
                <div className="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg mb-4">
                    <h4 className="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Последние события</h4>
                    <ul className="space-y-1">
                        {notifications.map((note, i) => (
                            <li key={i} className="text-sm text-gray-600 dark:text-gray-400">
                                {note}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {records.length === 0 ? (
                <p className="text-gray-500 text-sm">Рекордов пока нет. Стань первым!</p>
            ) : (
                <ol className="space-y-2">
                    {records.map((item, idx) => (
                        <li key={idx} className="flex justify-between items-center p-2 rounded bg-gray-50">
                            <span className="font-medium text-gray-700">
                                {idx + 1}. {item.name}
                            </span>
                            <span className="font-bold text-indigo-600">
                                {item.challenge_value} очков
                            </span>
                        </li>
                    ))}
                </ol>
            )}
        </div>
    );
}

const container = document.getElementById('react-leaderboard');
const challengeId = parseInt(container.dataset.challengeId, 10);

const root = ReactDOM.createRoot(container);
root.render(<Leaderboard challengeId={challengeId} />);