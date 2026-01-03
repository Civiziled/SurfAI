# Mini ChatGPT - Guide d'utilisation

## 🚀 Mise en place

### 1. Configuration de l'API OpenRouter

Assurez-vous que votre clé API OpenRouter est configurée dans votre fichier `.env` :

```env
OPENROUTER_API_KEY=votre-cle-api-openrouter
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

**Note:** Vous devez obtenir une clé API sur [https://openrouter.ai](https://openrouter.ai)

### 2. Fichiers créés

#### Backend
- `app/Services/SimpleAskService.php` - Service de communication avec l'API OpenRouter
- `app/Http/Controllers/AskController.php` - Contrôleur pour gérer les requêtes
- `config/services.php` - Configuration des services (mise à jour)
- `routes/web.php` - Routes pour la feature (mise à jour)
- `resources/views/prompts/system.blade.php` - Prompt système de l'IA

#### Frontend
- `resources/js/Pages/Ask/Index.vue` - Interface utilisateur VueJS

### 3. Dépendances NPM installées

```bash
npm install markdown-it highlight.js @tailwindcss/typography
```

Ces packages permettent:
- **markdown-it**: Rendu du Markdown
- **highlight.js**: Coloration syntaxique du code
- **@tailwindcss/typography**: Styles pour le contenu rédactionnel

## 📖 Utilisation

### Accès à la page

La page est accessible via le route `/ask` (après authentification):

```
http://votre-domaine.test/ask
```

### Fonctionnalités

1. **Sélection du modèle** - Choisissez parmi les modèles disponibles sur OpenRouter
2. **Saisie de la question** - Tapez votre question dans le textarea
3. **Envoi** - Cliquez sur "Envoyer la question"
4. **Affichage de la réponse** - La réponse s'affiche en Markdown formaté avec syntaxe highlighting

## 🔐 Sécurité

⚠️ **Important:** Les routes sont protégées par le middleware `auth` pour éviter l'accès non autorisé à l'API.

Si vous voulez permettre l'accès sans authentification, modifiez les routes dans `routes/web.php`:

```php
// Actuellement (protégé)
Route::middleware('auth')->group(function () {
    Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
    Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');
});
```

## 🎨 Personnalisation

### Prompt système

Modifiez le prompt système dans `resources/views/prompts/system.blade.php` pour changer le comportement de l'IA.

### Styles

Le composant Vue utilise:
- Tailwind CSS pour la mise en page
- Dark mode support automatique
- Classes `prose` pour la typographie

## ⚙️ Variables et constantes

### SimpleAskService

```php
public const DEFAULT_MODEL = 'openai/gpt-4o-mini';
```

Modifiez le modèle par défaut si nécessaire.

### Timeout

Le timeout pour les requêtes API est de **120 secondes** (configurable dans `SimpleAskService`).

## 🐛 Dépannage

### Erreur: "Manifeste Vite non trouvé"
```bash
npm run build
```

### Erreur: "Clé API invalide"
Vérifiez votre `OPENROUTER_API_KEY` dans `.env`

### Erreur: "Modèle non trouvé"
Assurez-vous que le modèle spécifié est disponible sur OpenRouter

### Performance lente
- Réduisez le timeout
- Choisissez un modèle plus rapide (mini vs ultra)
- Vérifiez votre connexion réseau

## 📝 Notes

- Une seule question peut être posée à la fois (pas d'historique)
- Les réponses ne sont pas stockées en base de données
- Le cache des modèles disponibles expire après 1 heure

## 🚀 Prochaines améliorations possibles

- Ajouter le streaming en temps réel
- Implémenter un historique de conversation
- Ajouter des images dans les messages
- Créer un client API séparé avec DTOs
- Implémenter la pagination pour les modèles
