# Создание проекта:

## БД: Хранилище для хранения информации о созданных проектах

### Структура БД - Таблицы

#### Users ( Сущность пользователя, который будет заниматься публикацией релизов и добавлением тугриков в к нему. )

| NAME       | TYPE      | INFO                           |
|------------|-----------|--------------------------------|
| id         | int       | id                             |
| email      | string    | email адрес. \ unique          |
| is_admin   | boolean   | админ или обычный пользователь |
| password   | string    | Пароль                         |
| created_at | timestamp | дата создания                  |
| updated_at | timestamp | дата последнего изменения      |
| deleted_at | timestamp | дата мягкого удаления.         |

---

#### Ghosts ( Сущность пользователя, который скачал релиз. )

| NAME       | TYPE   | INFO                   |
|------------|--------|------------------------|
| id         | int    | id                     |
| session_id | int    | id сессии пользователя |
| ip         | string | ip адрес пользователя  |

---

#### Releases ( Сущность релиза. Релиз приложения. )

| NAME                     | TYPE      | INFO                                               |
|--------------------------|-----------|----------------------------------------------------|
| id                       | int       | id                                                 |
| project_id               | int       | id проекта                                         |
| platform_id              | int       | id платформы                                       |
| changes                  | text      | изменения, если они есть                           |
| description              | text      | доп описание                                       |
| release_type_id          | int       | стадия релиза                                      |
| is_ready                 | boolean   | готов ли релиз к отображению на сайте к скачиванию |
| is_public                | boolean   | опубликован ли релиз                               |
| technical_requirement_id | int       | технические требования                             |
| user_id                  | int       | id пользователя собравшего релиз                   | 
| download_url             | string    | url для скачивания                                 | 
| version                  | string    | версия релиза                                      | 
| created_at               | timestamp | дата создания                                      | 
| updated_at               | timestamp | дата последнего изменения                          | 
| deleted_at               | timestamp | дата мягкого удаления.                             | 

---

#### ReleasesDownloads ( Сущность лога скачивания релиза )

| NAME        | TYPE      | INFO                            |
|-------------|-----------|---------------------------------|
| id          | int       | id                              |
| release_id  | int       | id релиза                       |
| ip          | string    | ip адрес пользователя           |
| useragent   | string    | Агент пользователя              |
| utm         | string    | спец метки                      |
| created_at  | timestamp | дата создания / дата скачивания |
| updated_at  | timestamp | дата последнего изменения       |
| deleted_at  | timestamp | дата мягкого удаления.          |

---

#### ReleasesTypes ( Сущность типа релиза. Стадия, этап релиза. )

| NAME        | TYPE      | INFO                         |
|-------------|-----------|------------------------------|
| id          | int       | id                           |
| name        | string    | тип релиза                   |
| description | timestamp | краткое описание типа релиза |
| created_at  | timestamp | дата создания                |
| updated_at  | timestamp | дата последнего изменения    |
| deleted_at  | timestamp | дата мягкого удаления.       |

---

#### Projects ( Сущность проекта, в который будет публиковаться релиз. )

| NAME        | TYPE      | INFO                        |
|-------------|-----------|-----------------------------|
| id          | int       | id                          |
| name        | string    | название проекта            |
| description | text      | описание проекта            |
| user_id     | int       | ссылка на создателя проекта |
| created_at  | timestamp | дата создания               |
| updated_at  | timestamp | дата последнего изменения   |
| deleted_at  | timestamp | дата мягкого удаления.      |

---

#### Platforms ( Сущность платформы )

| NAME       | TYPE      | INFO                      |
|------------|-----------|---------------------------|
| id         | int       | id                        |
| name       | string    | название платформы        |
| created_at | timestamp | дата создания             |
| updated_at | timestamp | дата последнего изменения |
| deleted_at | timestamp | дата мягкого удаления.    |

---

#### Technical_requirements ( технические характеристики )

| NAME           | TYPE      | INFO                      |
|----------------|-----------|---------------------------|
| id             | int       | id                        |
| OS_type        | string    | операционная система      |
| Specifications | text      | прочие характеристики     |
| created_at     | timestamp | дата создания             |
| updated_at     | timestamp | дата последнего изменения |
| deleted_at     | timestamp | дата мягкого удаления.    |

---

#### Projects_platforms ( связующая таблица projects и platforms )

| NAME        | TYPE | INFO         |
|-------------|------|--------------|
| id          | int  | id           |
| project_id  | int  | id проекта   |
| platform_id | int  | id платформы |

---

#### Projects_users ( связующая таблица user и projects )

| NAME       | TYPE | INFO         |
|------------|------|--------------|
| id         | int  | id           |
| user_id    | int  | id проекта   |
| project_id | int  | id платформы |

---
