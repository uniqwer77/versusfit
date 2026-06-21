import aiomysql 

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'student',
    'password': '0192837456',
    'db': 'versusfit_main',
    'charset': 'utf8mb4',
}

async def get_db(): return await aiomysql.connect(**DB_CONFIG)