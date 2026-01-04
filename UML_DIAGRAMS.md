# Diagrammes UML - SurferAI

Ce document présente les 5 diagrammes UML principaux pour le projet SurferAI.

## 1. Diagramme de Classe (Class Diagram)
Ce diagramme détaille la structure des modèles de données (Entités), avec leurs attributs (issus de la base de données) et leurs méthodes (relations et logique métier), selon le formalisme standard.

```mermaid
classDiagram
    %% Relations & Cardinalités
    User "1" --> "*" Conversation : possède
    Conversation "1" --> "*" Message : contient

    class User {
        -id: int
        -name: string
        -email: string
        -email_verified_at: timestamp
        -password: string
        -remember_token: string
        -preferred_model: string
        -instructions: json
        -created_at: timestamp
        -updated_at: timestamp
        +conversations(): HasMany
        +casts(): array
    }

    class Conversation {
        -id: int
        -user_id: int
        -title: string
        -model: string
        -context: longText
        -created_at: timestamp
        -updated_at: timestamp
        +user(): BelongsTo
        +messages(): HasMany
        +getFormattedMessages(): array
        +addMessage(role: string, content: mixed): Message
    }

    class Message {
        -id: int
        -conversation_id: int
        -role: enum
        -content: longText
        -created_at: timestamp
        -updated_at: timestamp
        +conversation(): BelongsTo
        +getContentAttribute(value: mixed): mixed
        +setContentAttribute(value: mixed): void
        +toApiFormat(): array
    }
```

## 2. Diagramme de Séquence (Sequence Diagram)
Représente les interactions temporelles lors de l'envoi d'un message par l'utilisateur et le traitement par le système (Streaming).

```mermaid
sequenceDiagram
    actor Client as Utilisateur (Navigateur)
    participant Route as Route Laravel
    participant Controller as ChatController
    participant Model as Message (DB)
    participant API as OpenRouter AI

    Client->>Route: POST /chat/{id}/message
    Route->>Controller: sendMessageStream(request)
    activate Controller
    
    Note right of Controller: Validation des entrées

    Controller->>Model: Message::create(user input)
    activate Model
    Model-->>Controller: Saved Instance
    deactivate Model

    Controller->>API: POST /chat/completions (Stream=true)
    activate API
    
    loop Server-Sent Events (SSE)
        API-->>Controller: JSON Chunk
        Controller-->>Client: "data: {content} \n\n"
    end
    
    deactivate API

    Controller->>Model: Message::create(assistant output)
    activate Model
    Model-->>Controller: Saved Instance
    deactivate Model

    deactivate Controller
```

## 3. Diagramme d'État (State Diagram)
Illustre les différents états possibles d'une **Conversation** et les transitions entre eux.

```mermaid
stateDiagram-v2
    [*] --> Nouvelle : Création
    Nouvelle --> Active : Premier Message
    
    state Active {
        [*] --> AttenteInput
        AttenteInput --> TraitementIA : Envoi Message
        TraitementIA --> Streaming : Connexion API
        Streaming --> AttenteInput : Fin Réponse
    }

    Active --> Archivée : Suppression Logique (SoftDelete)
    Active --> [*] : Suppression Définitive
```

## 4. Diagramme d'Activité (Activity Diagram)
Détaille l'algorithme de traitement d'un message utilisateur, incluant la logique conditionnelle (Texte vs Image) et la gestion d'erreurs.

```mermaid
stateDiagram-v2
    [*] --> RéceptionRequête
    
    state "Validation" as Val {
        RéceptionRequête --> VérifierAuth : User connecté ?
        VérifierAuth --> Erreur403 : Non
        VérifierAuth --> VérifierContenu : Oui
        VérifierContenu --> Erreur422 : Vide
        VérifierContenu --> AnalyserType : OK
    }

    Erreur403 --> [*]
    Erreur422 --> [*]

    state "Préparation" as Prep {
        AnalyserType --> FormatTexte : Seulement Texte
        AnalyserType --> OptimiserImage : Contient Image
        OptimiserImage --> FormatMultimodal
        FormatTexte --> ConstructionPayload
        FormatMultimodal --> ConstructionPayload
    }

    state "Exécution" as Exec {
        ConstructionPayload --> AppelOpenRouter
        AppelOpenRouter --> StreamResponse : Succès
        AppelOpenRouter --> LogErreur : Échec API
    }

    StreamResponse --> SauvegardeDB : Fin du flux
    LogErreur --> RetourMessageErreur
    
    SauvegardeDB --> [*]
    RetourMessageErreur --> [*]
```

## 5. Diagramme de Cas d'Utilisation (Use Case Diagram)
Vue d'ensemble des fonctionnalités offertes par le système aux acteurs.

```mermaid
usecaseDiagram
    actor "Visiteur" as Guest
    actor "Inscrit" as User
    actor "Admin" as Admin

    package "SurferAI System" {
        usecase "S'inscrire / Connexion" as UC_Auth
        usecase "Consulter Landing Page" as UC_View
        
        usecase "Gérer Conversations" as UC_Manage
        usecase "Discuter avec IA" as UC_Chat
        usecase "Changer de Modèle (GPT/Claude)" as UC_Config
        usecase "Uploader Fichiers" as UC_Upload
        
        usecase "Voir Dashboard" as UC_Dash
    }

    Guest --> UC_View
    Guest --> UC_Auth

    User --> UC_Auth
    User --> UC_Dash
    User --> UC_Manage
    User --> UC_Chat
    User --> UC_Config
    User --> UC_Upload

    %% Relations d'inclusion/extension
    UC_Chat ..> UC_Config : <<include>>
    UC_Upload ..> UC_Chat : <<extend>>
```
