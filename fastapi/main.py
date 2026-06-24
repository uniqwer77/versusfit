import json, asyncio
from datetime import datetime
from contextlib import asynccontextmanager
from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
import redis.asyncio as aioredis
from routers import leaderboard, ws
from routers.ws import manager
import logging
from fastapi import WebSocket

logger = logging.getLogger("uvicorn")

async def redis_subscriber():
    while True:
        pubsub = None
        try:
            redis = await aioredis.from_url("redis://redis:6379", decode_responses=True)
            pubsub = redis.pubsub()
            
            channels = [
                'laravel-database-challenge_updates',
            ]
            await pubsub.subscribe(*channels)
            logger.info(f"Redis subscriber started, listening to: {channels}")

            async for message in pubsub.listen():
                try:
                    if message['type'] != 'message':
                        continue

                    channel = message['channel']
                    data = json.loads(message['data'])

                    if channel == 'laravel-database-challenge_updates':
                        await manager.broadcast({
                            'type': 'leaderboard_update',
                            'challenge_id': data.get('challenge_id'),
                            'user_id': data.get('user_id'),
                            'name': data.get('name'),
                            'challenge_value': data.get('challenge_value')
                        })

                except json.JSONDecodeError as e:
                    logger.error(f"Invalid JSON from Redis channel {channel}: {e}")
                except Exception as e:
                    logger.error(f"Error processing message from channel {channel}: {e}")

        except aioredis.ConnectionError as e:
            logger.error(f"Redis connection error: {e}. Reconnecting in 2 seconds...")
        except Exception as e:
            logger.error(f"Unexpected error in redis_subscriber: {e}. Reconnecting in 2 seconds...")
        finally:
            if pubsub is not None:
                try:
                    await pubsub.unsubscribe(*channels)
                except Exception:
                    pass
                try:
                    await pubsub.close()
                except Exception:
                    pass
            await asyncio.sleep(2)

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