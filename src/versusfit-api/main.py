from fastapi import FastAPI, Request
from datetime import datetime
from routers import ws
import aiomysql
from routers import leaderboard
from fastapi.middleware.cors import CORSMiddleware
 
app = FastAPI(title='VersusFit API', version='0.2.0')

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(leaderboard.router)
app.include_router(ws.router)

@app.post('/internal/broadcast') 
async def internal_broadcast(request: Request):
    data = await request.json()
    await ws.manager.broadcast({'type': 'new_record', 'post': data})
    return {'ok': True}
 
@app.get('/api/status')
async def status():
        return {'status': 'ok', 'time': str(datetime.now())}
   