Развертывание системы:

1. Запустить `make up`
2. POST http://localhost:8083/user/register
3. POST http://localhost:8083/login
4. GET http://localhost:8083/user/{id}
5. GET http://localhost:8083/user/search?last_name={last_name}&first_name={first_name}
6. PUT http://localhost:8083/friend/set/{user_id}
7. PUT http://localhost:8083/friend/delete/{user_id}
8. GET http://localhost:8083/post/feed
9. POST http://localhost:8083/dialog/{user_id}/send
10. GET http://localhost:8083/dialog/{userId}/list
11. POST http://localhost:8083/post/create