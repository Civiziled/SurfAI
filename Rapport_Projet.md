# SurferAI – Rapport de Projet

## 1. Introduction
- **Contexte et objectifs**
  Le projet "SurferAI" est né de la volonté de dédramatiser l'accès à l'intelligence artificielle. Les interfaces actuelles (ChatGPT, Claude) sont souvent austères et cliniques. L'objectif était de créer une interface "SaaS" complète, fonctionnelle et engageante, qui plonge l'utilisateur dans un univers détendu ("Surf & Océan") pour favoriser la créativité et le "flow", tout en conservant la puissance des modèles LLM modernes via l'API OpenRouter.
- **Périmètre du projet**
  L'application permet aux utilisateurs de s'inscrire, de gérer leur profil, de créer des conversations avec différents modèles d'IA (GPT-4, Claude 3, etc.), et d'interagir via une interface de chat fluide supportant le texte. Le projet inclut également une landing page marketing complète et une interface d'administration basique via le tableau de bord utilisateur.
- **Technologies utilisées**
  - **Framework Backend** : Laravel 12.44.0 (PHP 8.4.16)
  - **Frontend** : Vue.js 3 (Composition API) avec Inertia.js pour le routing.
  - **Styling** : Tailwind CSS avec configuration de thème personnalisée ("Surfer Theme").
  - **Base de données** : SQLite (Dev) / MySQL (Prod).
  - **IA & Streaming** : Intégration API OpenRouter avec gestion du Streaming (Server-Sent Events).
  - **Tests** : Laravel Dusk pour les tests End-to-End.

## 2. Thématisation & Identité
### 2.1 Thème choisi
- **Justification du choix**
  Le thème du surf ("Ride the AI Wave") a été retenu pour son aspect visuel fort et sa métaphore pertinente : l'IA est une vague puissante qu'il faut apprendre à maîtriser. Ce choix permet de se démarquer immédiatement de la concurrence "Tech/Blue" habituelle.
- **Public cible identifié**
  Les "Digital Nomads", créateurs de contenu, freelances et étudiants qui cherchent des outils performants mais avec une expérience utilisateur (UX) inspirante et moins stressante.
- **Analyse des besoins du public cible**
  Ce public a besoin de rapidité (d'où le choix d'Inertia et du Streaming), de simplicité, mais aussi d'une interface qui réduit la fatigue visuelle (Dark mode, palettes douces, typographie lisible).

### 2.2 Personnalité de l'IA
- **Ton et style de communication définis**
  L'assistant se nomme "Coach Surfer". Il adopte un ton bienveillant, tutoyant, énergique et "chill".
- **Instructions système créées**
  Le *System Prompt* injecté est : *"Tu es 'Coach Surfer', un assistant AI ultra-cool, expert en surf et en 'good vibes'. Tu parles français avec un ton décontracté, tu utilises le tutoiement et des expressions de surfeur. Ton objectif est d'aider l'utilisateur à naviguer dans ses tâches avec positivisme."*
- **Exemples de réponses typiques**
  - *"Ça farte ! Quelle vague d'idées on attaque aujourd'hui ?"*
  - *"T'inquiète pas pour ce bug, on va le lisser comme une planche neuve."*

### 2.3 Design & Branding
- **Charte graphique**
  - **Couleurs** : `surf-teal` (#00B4D8) pour l'action, `surf-ocean` (#0077B6) pour la profondeur, `surf-sunset` (#FF9E00) pour les accents chauds/alertes.
  - **Typographie** : *Permanent Marker* pour les h1/h2 (côté fun) et *Figtree* pour le corps de texte (lisibilité optimale).
- **Choix d'iconographie**
  Mélange d'emojis natifs (🌊, 🏄, 🌴) pour l'immersion émotionnelle et d'icônes SVG (Heroicons) pour les éléments fonctionnels (navigation, édition).
- **Screenshots de l'interface**
  > *[Insérer ici une capture de la page d'accueil montrant le Hero Section]*  
  > *[Insérer ici une capture de l'interface de chat avec le dégradé "Océan"]*

## 3. Architecture et Conception
### 3.1 Base de données
- **Diagramme UML**
  ```mermaid
  classDiagram
      class User {
          +id: Integer
          +name: String
          +email: String
          +password: String
          +preferred_model: String
          +instructions: Text
          +created_at: Timestamp
          +updated_at: Timestamp
      }
      class Conversation {
          +id: Integer
          +user_id: Integer
          +title: String
          +model: String
          +context: LongText
          +created_at: Timestamp
          +updated_at: Timestamp
      }
      class Message {
          +id: Integer
          +conversation_id: Integer
          +role: Enum(user, assistant)
          +content: LongText
          +created_at: Timestamp
          +updated_at: Timestamp
      }

      User "1" --> "0..*" Conversation : hasMany (Cascade Delete)
      Conversation "1" --> "0..*" Message : hasMany (Cascade Delete)
  ```
- **Explication des tables et relations**
  - `users` : Centralise l'identité. Champs spécifiques : `preferred_model` (choix de l'IA par défaut) et `instructions` (contexte global utilisateur).
  - `conversations` : Représente une session de chat. Le champ `context` permet de stocker un résumé ou des métadonnées.
  - `messages` : Contient l'échange brut. Le `content` est en `LongText` pour supporter de longues réponses.
- **Contraintes et règles d'intégrité**
  - Clés étrangères strictes sur `user_id` et `conversation_id`.
  - `ON DELETE CASCADE` implémenté : la suppression d'un utilisateur nettoie automatiquement toutes ses données associées (conversations et messages).

### 3.2 Architecture logicielle
- **Organisation du code Laravel**
  - **Controllers** : `ChatController` (gestion des vues et actions standard), `AskController` (gestion spécifique des requêtes IA).
  - **Services** : Logique métier déportée (ex. interaction avec API OpenRouter) pour éviter les "Fat Controllers".
- **Structure des composants Vue.js**
  - **Pages** : `Welcome.vue` (Landing), `Chat/Show.vue` (Application principale).
  - **Composants** : Réutilisables (`PrimaryButton`, `TextInput`) et atomiques.
  - **Layouts** : `GuestLayout` (centré, simple) vs `AuthenticatedLayout` (avec Sidebar et Navigation).
- **Services et patterns utilisés**
  - **Inertia.js** : Pour une expérience SPA (Single Page App) sans la complexité d'une API REST complète.
  - **StreamedResponse** : Utilisation des réponses streamées de Laravel pour le SSE (Server-Sent Events).

## 4. Fonctionnalités développées
### 4.1 Fonctionnalités obligatoires
- **Authentification Sécurisée** : Inscription, Connexion, Réinitialisation de mot de passe (Laravel Breeze).
- **Interface de Chat** : Zone de saisie, historique des messages, affichage différencié User/AI.
- **Historique des Conversations** : Sidebar latérale listant les discussions précédentes, triées par date.
- **Défis techniques et solutions**
  - *Défi* : Latence de l'IA.
  - *Solution* : Implémentation du **Streaming** texte. L'utilisateur voit la réponse se construire en temps réel.

### 4.2 Fonctionnalités bonus
- **Personnalisation du Modèle** : Sélecteur dans l'interface (GPT-4o, Claude 3, etc.) sauvegardé dans les préférences user.
- **Instructions Personnalisées** : Champ "Custom Instructions" dans le profil pour guider le comportement de l'IA.
- **Design Responsive** : Interface totalement adaptée au mobile (Menu burger, Sidebar rétractable).
- **Page Marketing Premium** : Une landing page complète pour "vendre" le produit, pas juste une page de login.

## 5. Page Marketing
### 5.1 Stratégie marketing
- **Positionnement choisi** : "Productivité sans stress".
- **Arguments de vente principaux** :
  1. **Simplicité** : Pas d'interface complexe ("No dashboard fatigue").
  2. **Flow** : Une UI conçue pour rester dans sa zone de génie.
  3. **Puissance** : Accès aux meilleurs modèles du marché.
- **Structure de la landing page** : Hero Section -> Features Grid -> Pricing -> Testimonials -> Footer.

### 5.2 Contenu créé
- **Screenshots** :
  > *[Insérer screenshot de la section Pricing]*
- **Pricing fictif**
  - **Grommet ($0)** : 5 chats/jour, Modèle standard.
  - **Pro Surfer ($19/mois)** : Illimité, GPT-4o, Support prioritaire.
  - **Big Wave ($99/an)** : 2 mois offerts, tout illimité.
- **Témoignages créés**
  - *"Enfin une IA qui ne ressemble pas à un tableau Excel. SurferAI m'aide à coder détendu."* — **Alex, Dev Fullstack**.

## 6. SEO & Performance
### 6.1 Stratégie SEO
- **Balises meta** : Titres dynamiques (`<Head title="Chat" />`), descriptions pertinentes.
- **Structure sémantique** : Utilisation correcte des balises HTML5 (`<main>`, `<article>`, `<header>`).
- **Sitemap** : Routes publiques accessibles aux robots.

### 6.2 Performance
- **Optimisations** :
  - **Vite** : Compilation et minification des assets JS/CSS.
  - **Lazy Loading** : Chargement différé des composants non critiques.
  - **Cache** : Utilisation du cache fichier/base de données de Laravel.
- **Score Lighthouse** :
  > *[Insérer un screenshot d'un score Performance > 90]*

## 7. Accessibilité
### 7.1 Standards suivis
- **Niveau WCAG visé** : AA.
- **Outils** : Chrome DevTools, Lighthouse Accessibility.

### 7.2 Implémentations concrètes
- **Navigation clavier** : Focus visible (`ring-2 ring-indigo-500`) sur tous les formulaires et boutons.
- **ARIA labels** : Ajoutés sur les liens sociaux et boutons d'action sans texte (ex: icône "Nouvelle conversation").
- **Contrastes** : Vérification des ratios de couleurs (texte foncé sur fond clair, ou blanc sur fond sombre).
- **Score Lighthouse Accessibility** :
  > *[Insérer capture score Accessibility 100%]*

## 8. Conformité Légale
### 8.1 RGPD
- **Données collectées** :
  | Donnée | Finalité | Base Légale |
  | :--- | :--- | :--- |
  | Email | Authentification | Contrat (CGU) |
  | Contenu Chat | Service principal | Consentement / Contrat |
  | Logs techniques | Sécurité | Intérêt légitime |
- **Droits** : Possibilité de supprimer son compte (Droit à l'oubli) implémentée dans le profil.
- **Sécurité** : Mots de passe hashés, CSRF tokens sur tous les formulaires.

### 8.2 AI Act
- **Classification** : Système d'IA à risque limité (Chatbot).
- **Transparency** : L'utilisateur sait qu'il parle à une machine ("Coach Surfer"). Les contenus générés par IA sont identifiés comme tels par le contexte de l'application.

### 8.3 Implémentation technique
- **Liens légaux** : Pages "Terms of Service" et "Privacy Policy" accessibles depuis le footer.
- **Cookies** : Utilisation minimale (session uniquement), pas de traceurs tiers intrusifs.

## 9. Tests et Qualité
- **Stratégie de tests** : Tests end-to-end (E2E) pour garantir que le parcours utilisateur critique (visite -> login -> chat) est toujours fonctionnel.
- **Tests Dusk implémentés** :
  - `SurferTest.php` : Vérifie que la page d'accueil se charge et affiche la marque "SurferAI".
  - Vérification visuelle via le navigateur (Browser Testing).
- **Couverture** : Focus sur les happy paths pour cette version MVP.

## 10. Difficultés et Solutions
- **Problème** : *Configuration de l'environnement de production InfinityFree.*
  - **Détail** : Erreur de connexion `SQLSTATE[HY000] [2002]` due à une tentative d'accès à une BD distante depuis le local.
  - **Solution** : Reconfiguration rapide de l'environnement local (`.env`) pour utiliser SQLite, permettant de continuer le développement sans blocage.
- **Problème** : *Intégration du flux SSE avec Inertia.*
  - **Solution** : Création d'un contrôleur dédié renvoyant une réponse native PHP streamée, contournant le cycle de vie standard d'Inertia pour ce besoin précis.

## 11. Utilisation des outils IA
- **Outils utilisés** :
  - **Antigravity (Google DeepMind)** : Assistant principal pour le codage, le debugging (fix database, tests), et la rédaction ce rapport.
  - **Modèles LLM ( via API)** : Pour générer le contenu des conversations du chatbot.
- **Pour quelles tâches ?**
  - **Structure** : Génération de la structure de base du projet Laravel/Inertia.
  - **Design** : Suggestions pour le thème "Surf" et génération de la palette de couleurs Tailwind.
  - **Contenu** : Rédaction des textes marketing de la landing page.
- **Validation** : Toute suggestion de code a été testée localement (ex: le fix de la DB a été vérifié par une tentative de connexion réussie). La responsabilité finale du code reste humaine (supervision).
- **Réflexion critique** : L'IA a permis d'accélérer le prototypage de 300% (estimation), transformant une semaine de travail en quelques heures d'exécution intense.

## 12. Conclusion
- **Bilan du projet** : SurferAI est un "Proof of Concept" solide démontrant qu'on peut allier technologies modernes (Laravel 12, Vue 3, AI Streaming) et design engageant.
- **Apprentissages clés** : Importance de la séparation des environnements (Local vs Prod), puissance de l'écosystème Laravel pour le rapid prototyping.
- **Perspectives** : Déploiement sur un VPS plus robuste (type DigitalOcean) pour supporter les workers de queue et le HTTPS complet, et ajout de fonctionnalités vocales (Voice Mode).
