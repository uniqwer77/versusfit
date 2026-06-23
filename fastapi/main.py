import json, asyncio
from datetime import datetime
from contextlib import asynccontextmanager
from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
import redis.asyncio as aioredis
from routers import leaderboard, ws
from routers.ws import manager

async def redis_subscriber():
    redis = await aioredis.from_url("redis://redis:6379", decode_responses=True)

    pubsub = redis.pubsub()
    await pubsub.subscribe('laravel-database-challenge_updates', 'laravel-database-user.renamed')

    while True:
        message = await pubsub.get_message(timeout=1)

        if message is None:
            await asyncio.sleep(0.01)
            continue

        if message['type'] != 'message':
            continue

        channel = message['channel']
        data = json.loads(message['data'])

        if channel == 'laravel-database-challenge_updates':
            required_fields = ['challenge_id', 'user_id', 'name', 'challenge_value']
            await manager.broadcast({
                'type': 'leaderboard_update',
                'challenge_id': data['challenge_id'],
                'user_id': data['user_id'],
                'name': data['name'],
                'challenge_value': data['challenge_value']
            })

        elif channel == 'laravel-database-user.renamed':
            required_fields = ['id', 'new_name']
            await manager.broadcast({
                'type': 'user_renamed',
                'user_id': data['id'],
                'new_name': data['new_name']
            })
        
        elif channel == 'laravel-database-challenge_update':
            required_fields = ['challenge_id', 'user_id', 'name']
            await manager.broadcast({
                'type': 'user_joined',
                'challenge_id': data['challenge_id'],
                'user_id': data['user_id'],
                'name': data['name']
            })

@asynccontextmanager
async def lifespan(app: FastAPI):
    async def safe_subscriber():
        await redis_subscriber()

    task = asyncio.create_task(safe_subscriber())
    yield
    task.cancel()


app = FastAPI(title='VersusFit API', version='1.0.0', lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "https://versusfit.ru",
        "http://versusfit.ru",
        "http://localhost:8000",
        "http://127.0.0.1:8000"
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(leaderboard.router)
app.include_router(ws.router)


@app.get('/api/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}

@app.post('/internal/broadcast')
async def test_broadcast(request: Request):
    data = await request.json()
    await manager.broadcast(data)
    return {"status": "ok", "message": "Отправлено в WebSocket"}