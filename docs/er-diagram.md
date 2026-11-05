
# QaytaTap — ER-диаграмма

Бұл диаграмма жобаның дерекқорындағы негізгі кестелер мен олардың байланысын көрсетеді.

```mermaid
erDiagram
    USERS ||--o{ REPORTS : creates
    CATEGORIES ||--o{ REPORTS : contains

    USERS {
        int id PK
        varchar name
        varchar email UK
        varchar password
        timestamp created_at
    }

    CATEGORIES {
        int id PK
        varchar name
    }

    REPORTS {
        int id PK
        int user_id FK
        int category_id FK
        varchar title
        text description
        varchar type
        varchar location
        date event_date
        varchar status
        timestamp created_at
    }
```

## Кестелердің байланысы

- **USERS → REPORTS:** бір пайдаланушы бірнеше хабарландыру жариялай алады.
- **CATEGORIES → REPORTS:** бір санатта бірнеше хабарландыру болуы мүмкін.
- `user_id` — USERS кестесіне сілтеме жасайтын сыртқы кілт.
- `category_id` — CATEGORIES кестесіне сілтеме жасайтын сыртқы кілт.

PK — негізгі кілт, FK — сыртқы кілт, UK — қайталанбайтын мән.
