# Авторизация:
### 1. DELETE: /api/auth/user/delete:
- 1.1 Успешное удаление
- 1.2 Ошибка авторизации Unauthorized

### 2. POST: /api/auth/forgot:
- 2.1 Успешный запрос
- 2.2 Валидация данных по RefreshRequest

### 3. POST: /api/auth/login:
- 3.1 Успешная авторизация
- 3.2 Валидация данных по LoginRequest
- 3.3 Ошибка авторизации Unauthorized

### 4. POST: /api/auth/logout:
- 4.1 Успешная деАвторизация
- 4.2 Ошибка авторизации Unauthorized

### 5. GET: /api/auth/user/:
- 5.1 Успешный возврат записи о пользователе
- 5.2 Ошибка авторизации Unauthorized

### 6. POST: /api/auth/refresh/:
- 6.1 Успешная замена access токена
- 6.2 Ошибка авторизации Unauthorized: Refresh Token не действителен

### 7. POST: /api/auth/registration/:
- 7.1 Успешная регистрация
- 7.2 Валидация данных по RegisterRequest
- 7.3 Ошибка авторизации Unauthorized
- 7.4 Ошибка авторизации Forbidden

### 8. POST: /api/auth/registration/:
- 8.1 Успешная регистрация
- 8.2 Ошибка авторизации Unauthorized: Refresh Token не действителен

### 9. POST: /api/auth/reset/:
- 9.1 Успешный сброс пароля
- 9.2 Валидация данных по ResetPasswordRequest
- 9.3 Ошибка авторизации Unauthorized: Refresh Token не действителен

# ChangeLog, Platform, Release_type, Technical_requirements, Release(Их методы друг от друга отличаются лишь сущностью и полями)
### 1. POST: /api/changes/:
- 1.1 Успешное создание записи
- 1.2 Валидация данных по StoreRequest
- 1.3 Ошибка авторизации Unauthorized

### 2. DELETE: /api/changes/{change_id}/:
- 2.1 Успешное удаление записи
- 2.2 Исключение ModelNotFoundException
- 2.3 Ошибка авторизации Unauthorized

### 3. GET: /api/changes/:
- 3.1 Успешное получение записей (без limit и offset)
- 3.2 Успешное получение записей (с limit, без offset)
- 3.3 Успешное получение записей (с limit и offset)
- 3.4 Ошибка при получении записей (без limit, но с offset)
- 3.5 Валидация Данных IndexRequest
- 3.6 Ошибка авторизации Unauthorized

### 4. GET: /api/changes/{change_id}/:
- 4.1 Успешное получение записи
- 4.2 Исключение ModelNotFoundException
- 4.3 Ошибка авторизации Unauthorized

### 5. UPDATE: /api/changes/{change_id}/:
- 5.1 Успешное получение записи
- 5.2 Исключение ModelNotFoundException
- 5.3 Ошибка авторизации Unauthorized
- 5.4 Валидация Данных UpdateRequest
- 5.5 Исключение SameData

# File:
### 1. POST: /api/files/:
- 1.1 Успешное создание записи
- 1.2 Валидация на уникальный путь до файла. Чтобы случайно не сохранять дважды один и тот же файл, одного и того же релиза.

### 2. DELETE: /api/files/{file_id}/:
- 2.1 Успешное удаление записи
- 2.2 Исключение ModelNotFoundException
- 2.3 Ошибка авторизации Unauthorized

### 3. GET: /api/files/:
- 3.1 Успешное получение записей (без limit и offset)
- 3.2 Успешное получение записей (с limit, без offset)
- 3.3 Успешное получение записей (с limit и offset)
- 3.4 Ошибка при получении записей (без limit, но с offset)
- 3.5 Валидация Данных IndexRequest
- 3.6 Ошибка авторизации Unauthorized

### 4. GET: /api/files/{file_id}/:
- 4.1 Успешное получение записи
- 4.2 Исключение ModelNotFoundException
- 4.3 Ошибка авторизации Unauthorized

### 5. UPDATE: /api/files/{file_id}/:
- 5.1 Успешное получение записи
- 5.2 Исключение ModelNotFoundException
- 5.3 Ошибки при работе с файлами

# Project:
### 1. PATCH: /api/projects/{project_id}/users/{user_id}/:
- 1.1 Успешное добавление пользователя
- 1.2 Ошибка при повторном добавлении пользователя

### 2. DELETE: /api/projects/{project_id}/users/{user_id}/:
- 2.1 Успешное удаление пользователя
- 2.2 Ошибка при повторном удалении пользователя

### 3. PATCH: /api/projects/{project_id}/platforms/{platform_id}/:
- 3.1 Успешное добавление платформы
- 3.2 Ошибка при повторном добавлении платформы

### 4. DELETE: /api/projects/{project_id}/platforms/{platform_id}/:
- 4.1 Успешное удаление платформы
- 4.2 Ошибка при удалении повторном платформы

### 5. POST: /api/projects/:
- 5.1 Успешное создание записи
- 5.2 Валидация данных по StoreRequest
- 5.3 Ошибка авторизации Unauthorized
- 5.4 Ошибка авторизации Forbidden

### 6. DELETE: /api/projects/{project_id}/:
- 6.1 Успешное удаление записи
- 6.2 Исключение ModelNotFoundException
- 6.3 Ошибка авторизации Unauthorized
- 6.4 Ошибка авторизации Forbidden

### 7. GET: /api/projects/:
- 7.1 Успешное получение записей
- 7.2 Валидация ошибок IndexRequest
- 7.3 Ошибка авторизации Unauthorized

### 8. GET: /api/projects/{project_id}:
- 8.1 Успешное получение записи
- 8.2 Ошибка авторизации Unauthorized
- 8.3 Исключение ModelNotFoundException

### 9. PATCH: /api/projects/{project_id}:
- 9.1 Успешное получение записи
- 9.2 Ошибка авторизации Unauthorized
- 9.3 Исключение ModelNotFoundException
- 9.4 Ошибка авторизации Forbidden
- 9.5 Исключение SameData
- 9.6 Валидация Данных UpdateRequest

# Release_downloads:
### 1. POST: /api/rels_dls/:
- 1.1 Успешное создание записи
- 1.2 Валидация данных по StoreRequest
- 1.3 Ошибка авторизации Unauthorized
- 1.4 Ошибка авторизации Forbidden

### 2. DELETE: /api/rels_dls/{rel_dl_id}/:
- 2.1 Успешное удаление записи
- 2.2 Исключение ModelNotFoundException
- 2.3 Ошибка авторизации Unauthorized
- 2.4 Ошибка авторизации Forbidden

### 3. GET: /api/rels_dls/:
- 3.1 Успешное получение записей
- 3.2 Валидация ошибок IndexRequest
- 3.3 Ошибка авторизации Unauthorized

### 4. GET: /api/rels_dls/{rel_dl_id}:
- 4.1 Успешное получение записей
- 4.2 Валидация ошибок IndexRequest
- 4.3 Ошибка авторизации Unauthorized
