# RECHERCHE GLOBALE

Ci dessous ce trouve tous les détails de fonctionnement du systeme d'autocomplete. Voir la section INIT et FRONTEND plus bas pour l'activation.

## AUTOCOMPLETE

L'autocomplète est généré avec FuseJs. Les titres des pages/post/documents (le choix est a faire dans la page d'administration) proviennent du endpoint qui retourne un index des post séléctionnés. L'index des `tags` provient de relevanssi.  Fuse ressort les 5 premiers resultats les plus pertinent en se basant sur un poids de 50% sur le titre et de 10% sur les mots clés contenu dans la page. Le endpoint remonte egalement des mots clés rajoutés manuellement dans l'admin. Les mots clés manuels comptent pour 40% supplémentaire. 

Concretement, on recupere en BDD l'index de relevanssi pour tous les posts. A cela, nous ne gardons que les `post types` défini en admin. Ensuite, nous y rajoutons le tableau de mot clés manuels défini en admin. Tout y est sauvegardé dans un transient. Lors du call du endpoint, selon les paramètres, nous retournons l'index dans la bonne langue, et comprenant les resultats privés ou pas.

Les résultats du endpoint sont mis a jour à chaque modification de page puis sauvegardés dans un transient pour une semaine. Coté visiteur, l'index est sauvegardé pour 7 jours dans le local storage. Cela améliore les performances de fuse qui reste réactif. 

Un timestamp est sauvegardé en dans le champ acf `autocomplete_search_version` lors d'un refresh du cache coté serveur. Il est remonté au JS lors du chargement de la page et sauvegardé en local storage lorsque l'index est sauvegardé en local storage. Lors du chargement de la page, si le timestamp en local storage est différent de celui du serveur, le navigateur récuperera la derniere version en date puis sauvegardera de nouveau l'index en local storage + son timestamp.

Afin d'optimiser la taille de l'index json, nous récupérons dans l'index de relevanssi les mots présent plus d'une fois dans le contenu des pages ET d'au moins 3 charactères. Nous fournissons une liste de `stopwords` à relevanssi pour éviter d'indexer des mots de liaison inutile et non pertinent pour un index.

Avec jQuery, une navigation au clavier a été rajouté. La subtilité est que fuse regénère la liste à chaque changement. La navigation dans les sugestion se fait donc par un ID rajouté dans chaque élément.

Lors de l'afficgage de resultat, nous affichons 6 élements. Le premier élément est defini pour envoyer sur le lien de la recherche, les 5 autres pour générer un clic vers le lien direct des articles via le paramètre `/?page_id=XXXX`

Pour les mots clés manuels, ils ont été fait dans le cas ou le client souhaite rajouter des `resultats` supplémentaire autre que ceux de l'index de base (Un lien rapide vers une question par exemple). 
Ces mots clés s'ajoutent dans la page d'option `Recherche Globale`. Pour faire simple, lors de la création de l'index basé sur relevanssi, on rajoute les resultats manuels en donnant un plus gros poids de mot sur le titre et les mots clés définis. Il est possible de rajouter des resultats qui ne sont pas des pages (donc par ID). Dans ce cas-ci, nous mettont l'url directement dans l'index, mais nous gardons le systeme d'ID pour tout le reste car c'est bien moins gourmand en memoire d'index que des URLs.

## AUTOCOMPLETE V2

Pour la 2nd version de l'autocomplete présente sur ce projet (CNQ), nous rajoutons deux principales fonctionnalitées: Le support de WPML ainsi que l'ajout d'un double index (privé/publique)

### WPML

Coté utilisation tout est géré automatiquement. La gestion de l'index se fait toute seule. Ce qui change c'est que le JS va rajouter dans le endpoint le paramètre `/fr` pour avoir la version française ou autre code de langue choisi. Le endpoint coté backend va recuperer l'index global stoqué en transient et le filtrer pour n'avoir que la bonne langue. Si WPML n'est pas installé, le paramètre sera `/main` pour avoir la langue principale. 

La langue est envoyée directement dans le JS dans la variable JS `search_options.language`.

###  Privé/Publique

Cette notion permet d'appeler un index dédié aux utilisateurs connéctés (et peut etre étendu pour ressortir un nombre infini d'index utilisateurs). Concretement, dans la variable JS `search_options.secret_key`, nous y renvoyons pour les utilisateurs connécté une clé privé. Celle-ci est rajouté au endpoint qui va la verifier et si valide remonter l'index voulu. Celui-ci remplace l'index en local storage des utilisateurs s'ils se `[connectent/déconnectent]`

La notion de Page Privé/Publique se gère dans la fonction `is_this_page_private()`. Par défaut, elle se base sur un champ ACF `private_page`. Le retour de l'index selon l'autorisation se gère dans la fonction `get_autorised_index()`. Toutes deux disposent de filtres permettant de changer la logique.

## FILTRES

De nombreux filtres sont en place dans les fichiers `search.functions.php` et `search.helpers.php` pour permettre d'altérer l'index ou le retour du endpoint.

## ENDPOINT

- Publique: Le endpoint de base est : 

 `/wp-json/search/index/[LANG]`

 Le paramètre `lang` obligatoire donne l'index à remonter. Dans le cas ou WPML est pas installé, la langue à donner est `main`

- Privé : Le endpoint privé est : 

 `/wp-json/search/index/[LANG]/[KEY]`
 
 Le parametre `key` est la clé privé qui débloque l'index réservé aux utilisateurs connécté.

## INIT DE L'INDEX
Pour que l'index fonctionne, ACF et Relevanssi doivent etre installé et actif. Vous devez ensuite definir dans relevanssi les post-types à indexer. Ensuite sauvegardez la page d'option `Recherche Globale` pour une première initialisation. A chaque sauvegarde de cette page d'option ou a l'update d'un post, l'index va se regénérer. 

### STOPWORDS
Pour éviter d'avoir trop de mots clés dans l'index, il est recommandé d'ajouter des stopwords dans les paramètres de relevanssi. Vous pouvez trouver des listes toute faite par langue [ICI](https://www.ranks.nl/stopwords)

 ## FRONTEND : AFFICHER L'AUTOCOMPLETE
 Pour afficher l'autocomplete, il faut wrapper l'input de recherche avec une div ayant pour class `wrapper-search-autocomplete`. Il faudra styliser le div qui affiche les résultats. Le reste est automatique.

 ```html
<div class="wrapper-search-autocomplete">
    <input type="text" name="s" id="search" class="search-field" value="Rechercher" />
</div> 
 ```

Les éléments LI des resultats se trouvent dans la fonction `inject_autocomplete_template()`

**Dépendances de l'autocomplete** : 
- `/assets/scripts/global-search.js`
- `fuse.js` (npm)
- Le dossier `/inc/search/*` 
- Le plugin `relevanssi`
- Le groupe de champs ACF `Recherche Globale`
- Le ul de l'autocomplete sous la recherche: `display_autocomplete();`
- Enqueue le script avec `wp-util` pour avoir la gestion du template `underscore.js` de l'autocomplete
``` php
wp_enqueue_script( 'global-search', Assets\asset_path( 'scripts/global-search.js' ), array( 'jquery', 'wp-util' ), null, false );
```


