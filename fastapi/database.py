import aiomysql 

DB_CONFIG = {
    'host': 'mysql',
    'port': 3306,
    'user': 'student',
    'password': '0192837456',
    'db': 'versusfit_main',
    'charset': 'utf8mb4',
}

async def get_db(): return await aiomysql.connect(**DB_CONFIG)