from fastapi import APIRouter, HTTPException
import aiomysql
from database import get_db

router = APIRouter(prefix='/api')

@router.get("/challenges/{challenge_id}/leaderboard")
async def get_leaderboard(challenge_id: int):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute("SELECT id FROM challenges WHERE id = %s", (challenge_id,))
            if not await cur.fetchone():
                raise HTTPException(status_code=404, detail="Challenge not found")

            query = """
                SELECT u.id as user_id, u.name, COALESCE(r.value, 0) as challenge_value
                FROM challenge_members cm
                JOIN users u ON cm.user_id = u.id
                LEFT JOIN records r ON r.user_id = u.id AND r.challenge_id = cm.challenge_id
                WHERE cm.challenge_id = %s
                ORDER BY challenge_value DESC
            """
            await cur.execute(query, (challenge_id,))
            return await cur.fetchall()
    finally:
        conn.close()