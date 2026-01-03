# SurferAI – Rapport de Projet

## 1. Introduction
- **Contexte et objectifs**
  Le projet "SurferAI" est né de la volonté de dédramatiser l'accès à l'intelligence artificielle. Les interfaces actuelles (ChatGPT, Claude) sont souvent austères et cliniques. L'objectif était de créer une interface "SaaS" complète, fonctionnelle et engageante, qui plonge l'utilisateur dans un univers détendu ("Surf & Océan") pour favoriser la créativité et le "flow", tout en conservant la puissance des modèles LLM modernes.
- **Périmètre du projet**
  L'application permet aux utilisateurs de s'inscrire, de gérer leur profil, de créer des conversations avec différents modèles d'IA (GPT-4, Claude 3), et d'interagir via une interface de chat fluide supportant le texte et les images. Le projet inclut également une landing page marketing complète et une interface d'administration basique via le tableau de bord.
- **Technologies utilisées**
  - **Framework Backend** : Laravel 11 (PHP 8.2+)
  - **Frontend** : Vue.js 3 (Composition API) avec Inertia.js pour le routing unifié.
  - **Styling** : Tailwind CSS avec une configuration de thème personnalisée.
  - **Base de données** : MySQL.
  - **IA & Streaming** : Intégration API OpenRouter avec gestion du Streaming (Server-Sent Events).
  - **Tests** : Laravel Dusk pour les tests End-to-End (E2E).

## 2. Thématisation & Identité
### 2.1 Thème choisi
- **Justification du choix**
  Le thème du surf ("Ride the AI Wave") a été retenu pour son aspect visuel fort et sa métaphore pertinente : l'IA est une vague puissante qu'il faut apprendre à maîtriser. Ce choix permet de se démarquer immédiatement de la concurrence "Tech/Blue" habituelle.
- **Public cible identifié**
  Les "Digital Nomads", créateurs de contenu et freelances tech qui cherchent des outils performants mais avec une âme et une expérience utilisateur (UX) supérieure.
- **Analyse des besoins du public cible**
  Ce public a besoin de rapidité (d'où le choix d'Inertia et du Streaming), de simplicité, mais aussi d'une interface inspirante qui réduit la fatigue visuelle (Dark mode, palettes douces).

### 2.2 Personnalité de l'IA
- **Ton et style de communication définis**
  L'assistant se nomme "Coach Surfer". Il adopte un ton bienveillant, tutoyant et énergique.
- **Instructions système créées**
  Le *System Prompt* injecté dans chaque conversation est : *"Tu es 'Coach Surfer', un assistant AI ultra-cool, expert en surf et en 'good vibes'. Tu parles français avec un ton décontracté, tu utilises le tutoiement et des expressions de surfeur. Ton objectif est d'aider l'utilisateur à naviguer dans ses tâches avec positivisme."*
- **Exemples de réponses typiques**
  - *"Ça farte ! Quelle vague d'idées on attaque aujourd'hui ?"*
  - *"T'inquiète pas pour ce bug, on va le lisser comme une planche neuve."*

### 2.3 Design & Branding
- **Charte graphique**
  - **Couleurs** : Utilisation de variables CSS/Tailwind personnalisées : `surf-teal` (#00B4D8) pour l'action, `surf-ocean` (#0077B6) pour la profondeur, `surf-sunset` (#FF9E00) pour les accents chauds.
  - **Typographie** : *Permanent Marker* pour les titres (côté fun/manuscrit) et *Figtree* pour le corps de texte (lisibilité).
- **Choix d'iconographie**
  Mélange d'emojis natifs (🌊, 🏄, 🌴) pour l'immersion émotionnelle et d'icônes SVG (Heroicons) pour les éléments fonctionnels, assurant un équilibre entre ludique et sérieux.
- **Screenshots de l'interface**
  *[Insérer ici une capture de la page d'accueil]*
  *[Insérer ici une capture de l'interface de chat]*

## 3. Architecture et Conception
### 3.1 Base de données
- **Diagramme UML**
  Les entités principales sont :
  `User (1) ---- (*) Conversation (1) ---- (*) Message`
- **Explication des tables et relations**
  - `users` : Stocke les informations d'authentification et le `preferred_model`.
  - `conversations` : Lie un utilisateur à un thread, stocke le titre et le modèle utilisé.
  - `messages` : Contient le `role` ('user' ou 'assistant') et le `content` (texte JSONifié pour supporter le multimodal).
- **Contraintes et règles d'intégrité**
  Utilisation de clés étrangères avec `ON DELETE CASCADE`. Si un utilisateur supprime son compte, toutes ses conversations et messages sont instantanément purgés de la base de données.

### 3.2 Architecture logicielle
- **Organisation du code Laravel**
  - Les **Controllers** (`ChatController`, `AskController`) gèrent les requêtes HTTP.
  - La logique métier complexe est déportée dans des **Services** (`ChatService`, `ImageService`, `SimpleAskService`) pour garder les contrôleurs légers.
- **Structure des composants Vue.js**
  - Architecture basée sur des composants atomiques (`PrimaryButton`, `TextInput`) réassemblés dans des Pages (`Pages/Chat/Show.vue`).
  - Utilisation des **Layouts** (`AuthenticatedLayout`) pour gérer la structure commune (Sidebar, Navigation).
- **Services et patterns utilisés**
  - Pattern **Service Layer** pour l'interaction avec l'API OpenRouter.
  - Utilisation de Guzzle en mode **Stream** pour recevoir les tokens de l'IA en temps réel.

## 4. Fonctionnalités développées
### 4.1 Fonctionnalités obligatoires
- **Authentification complète** : Login, Register, Reset Password (basé sur Laravel Breeze).
- **Chat en Streaming** : Le cœur de l'app. Les réponses de l'IA s'affichent mot à mot sans rechargement, grâce aux Server-Sent Events (SSE).
- **Gestion des conversations** : Création, listage, et suppression de l'historique de chat.
- **Screenshots annotés** : *[Insérer screenshot du chat avec une flèche montrant le curseur de streaming]*
- **Défis techniques et solutions** :
  - *Défi* : Intégrer le streaming SSE dans l'architecture Inertia.js (qui attend du JSON complet).
  - *Solution* : Création d'une route spécifique retournant une `StreamedResponse` et gestion manuelle de l'objet `EventSource` côté Vue.js.

### 4.2 Fonctionnalités bonus
- **Support Multimodal (Images)** : L'utilisateur peut uploader une image pour que l'IA l'analyse.
- **Choix du Modèle** : Sélecteur dynamique permettant de passer de GPT-4o à Claude 3 Haiku selon les besoins (rapidité vs intelligence).
- **Dark Mode** : Bascule automatique ou manuelle du thème via Tailwind (`darkMode: 'class'`).

## 5. Page Marketing
### 5.1 Stratégie marketing
- **Positionnement choisi** : "The Productivity OS for Chill People".
- **Arguments de vente principaux** :
  1. Zéro stress (UI apaisante).
  2. Vitesse éclair (Turbo Infrastructure).
  3. Collaboration intelligente (Duo IA).
- **Structure de la landing page** : Hero (Hook) > Logos (Social Proof) > Features Grid > Table de Comparaison > Pricing > FAQ > Footer.

### 5.2 Contenu créé
- **Screenshots des différentes sections** : *[Insérer captures des sections]*
- **Pricing fictif** :
  - *Grommet ($0)* : Pour découvrir.
  - *Pro Surfer ($19)* : L'offre phare (illimitée).
  - *Big Wave ($99)* : Pour les équipes.
- **Témoignages créés** : Création de personas crédibles (ex: "Elena Aris, Product Designer") validant le concept du "Chill Mode".

## 6. SEO & Performance
### 6.1 Stratégie SEO
- **Balises meta implémentées** : Tags dynamiques `<Head title="..." />` sur chaque page Vue via Inertia.
- **Structure sémantique HTML** : Respect strict de la hiérarchie (`h1` unique, `section`, `nav`, `footer`).
- **Sitemap et robots.txt** : Configurés pour permettre l'indexation des pages publiques (Welcome, Login) et bloquer les pages privées (Chat/*).

### 6.2 Performance
- **Score Lighthouse Performance** : *[Insérer screenshot, idéalement >90]*
- **Optimisations réalisées** :
  - **Lazy Loading** des routes Vue.js.
  - **Minification** des assets via Vite.
  - Chargement optimisé des polices (Bunny Fonts).

## 7. Accessibilité
### 7.1 Standards suivis
- **Niveau WCAG visé** : AA.
- **Outils de test** : Navigation clavier manuelle, Lighthouse Accessibility Audit.

### 7.2 Implémentations concrètes
- **Navigation clavier** : Tous les éléments interactifs ont un état `:focus-visible` (anneau bleu ou `surf-teal`) clairement visible.
- **ARIA labels** : Ajoutés aux boutons iconographiques (ex: le bouton "Nouvelle conversation" a un `aria-label="New Chat"`).
- **Gestion des contrastes** : Le texte gris sur fond blanc a été assombri (`text-slate-600` au min) pour garantir la lisibilité.
- **Score Lighthouse Accessibility** : *[Insérer screenshot]*

## 8. Conformité Légale
### 8.1 RGPD
- **Données collectées** : Nom, Email (pour le service), IP (sécurité).
- **Tableau des finalités** :
  - *Email* : Identification et récupération de compte.
  - *Conversations* : Historique accessible à l'utilisateur uniquement.
- **Droits des utilisateurs** : Droit à l'effacement total via le bouton "Supprimer mon compte" dans le profil.
- **Mesures techniques** : Chiffrement des mots de passe (Bcrypt), HTTPS forcé.

### 8.2 AI Act
- **Classification** : Système d'IA générative à usage général (risque limité/modéré).
- **Transparence** : L'utilisateur est informé dès l'accueil qu'il interagit avec une IA. Un disclaimer est présent : "L'IA peut faire des erreurs."

### 8.3 Implémentation technique
- **Cookie consent** : Un bandeau simple informe de l'utilisation de cookies strictement nécessaires au fonctionnement (session).
- **Pages légales** : Liens "Privacy" et "Terms" présents dans le footer.

## 9. Tests et Qualité
- **Stratégie de tests** : Priorité aux tests d'intégration et E2E pour valider les parcours critiques.
- **Tests Dusk implémentés** :
  - `ExampleTest.php` : Vérifie le rendu de la home.
  - `SurferTest.php` : Vérifie la présence de la marque et l'accès à la page.
  - *Auth tests* : Vérification de l'inscription et du login.
- **Résultats** : Les tests automatisés permettent de déployer sereinement sans casser l'authentification.

## 10. Difficultés et Solutions
- **Problème rencontré** : La latence des réponses de l'IA (parfois 3-4 secondes avant le premier mot) frustrait les utilisateurs.
- **Solution apportée** : Passage d'un appel API classique à une réponse en **Streaming**. L'utilisateur voit le texte s'écrire en temps réel, ce qui masque la latence et rend l'attente active et engageante.

## 11. Utilisation des outils IA
- **Outils utilisés** :
  - **Assistant AI (Gemini/Claude)** : Pour la génération du boilerplate code (Migrations, Modèles) et la rédaction du contenu marketing créatif.
  - **Copilot** : Pour l'autocomplétion rapide des classes Tailwind CSS.
- **Pour quelles tâches ?** : Principalement pour accélérer l'écriture du code répétitif ("boilerplate") et pour générer des idées de textes "fun" pour le thème surf.
- **Validation** : Chaque morceau de code généré a été relu et adapté. Le code de Streaming, particulièrement complexe, a nécessité plusieurs itérations manuelles pour fonctionner correctement avec Inertia.
- **Réflexion critique** : L'IA agit comme un "Pair Programmer" infatigable, permettant à un développeur seul de produire un SaaS complet et poli en un temps record.

## 12. Conclusion
- **Bilan du projet** : SurferAI est bien plus qu'un simple wrapper ChatGPT. C'est une application avec une identité forte, qui prouve qu'un outil technique peut être fun et accessible.
- **Apprentissages clés** : Maîtrise de la stack VILT (Vue, Inertia, Laravel, Tailwind), gestion des flux de données temps réel (SSE), et importance cruciale du Branding dans un projet SaaS.
- **Perspectives d'amélioration** : Ajout d'une fonctionnalité de "Text-to-Speech" pour que le Coach parle réellement, et développement d'une application mobile native.
