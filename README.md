Развертывание системы:

1. Запустить `make up`
2. POST http://localhost:8083/user/register
3. POST http://localhost:8083/login
4. GET http://localhost:8083/user/{id}
5. GET http://localhost:8083/user/search?last_name={last_name}&first_name={first_name}
   `docker cp var/temp.csv social_db:/tmp`
