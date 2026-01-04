# Diagramme UML de la Base de Données - SurferAI

Ce document détaille la structure de la base de données relationnelle de l'application.

```mermaid
erDiagram
    %% Entités Principales
    users ||--o{ conversations : "possède"
    conversations ||--o{ messages : "contient"

    %% Définition des Tables
    users {
        bigint id PK
        string name
        string email UK "Unique"
        timestamp email_verified_at "Nullable"
        string password
        string remember_token "Nullable"
        string preferred_model "Default: openai/gpt-4o-mini"
        text instructions "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    conversations {
        bigint id PK
        bigint user_id FK "OnDelete: Cascade"
        string title "Nullable"
        string model "Default: openai/gpt-4o-mini"
        longtext context "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    messages {
        bigint id PK
        bigint conversation_id FK "OnDelete: Cascade"
        enum role "Values: user, assistant"
        longtext content
        timestamp created_at
        timestamp updated_at
    }

    %% Tables Techniques (Laravel Standard)
    cache {
        string key PK
        mediumtext value
        integer expiration
    }

    cache_locks {
        string key PK
        string owner
        integer expiration
    }

    jobs {
        bigint id PK
        string queue
        longtext payload
        tinyint attempts
        integer reserved_at "Nullable"
        integer available_at
        integer created_at
    }

    job_batches {
        string id PK
        string name
        integer total_jobs
        integer pending_jobs
        integer failed_jobs
        longtext failed_job_ids
        mediumtext options "Nullable"
        integer cancelled_at "Nullable"
        integer created_at
        integer finished_at "Nullable"
    }

    failed_jobs {
        bigint id PK
        string uuid UK
        text connection
        text queue
        longtext payload
        longtext exception
        timestamp failed_at
    }

    sessions {
        string id PK
        bigint user_id FK "Nullable"
        string ip_address "Nullable"
        text user_agent "Nullable"
        longtext payload
        integer last_activity
    }
```

## Légende
- **PK** : Primary Key (Clé Primaire)
- **FK** : Foreign Key (Clé Étrangère)
- **UK** : Unique Key (Clé Unique)
- **||--o{** : Relation "Un à Plusieurs" (One-to-Many)
