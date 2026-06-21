from fastapi import FastAPI
from datetime import datetime
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
 
@app.get('/api/status')
async def status():
        return {'status': 'ok', 'time': str(datetime.now())}
   