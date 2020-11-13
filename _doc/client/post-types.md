# Types de contenu

Les types de contenu, ou *post types*, sont les différents contenus qui peuvent se retrouver dans le CMS et s'afficher sur le site web. Chaque type fonctionne de façon différente, s'affiche de façon différente sur le site web et comprennent des options qui leur sont spécifiques.

WordPress comprend deux types de contenu principaux par défaut : les **pages** et les **articles**. Les autres types de contenu ont été créés spécialement pour votre site web. Ce sont des types de contenu personnalisés (ou *custom post types*).

Vous retrouvez tous les types de contenu dans le menu de gauche du CMS. Pour aller ajouter ou modifier un contenu faisant partie d'un certain type, on doit donc se rendre dans le menu du type correspondant dans le menu de gauche. (Cliquer **Ajouter** pour créer un nouvel élément, ou cliquer sur le **titre** d'un des éléments existants pour le modifier).

## Fonctionnalités globales des types de contenu

Certaines fonctionnalités sont globales à la plupart des types de contenu sur le site. Ce sont donc des options qui sont toujours modifiables de la même façon, que vous soyez dans un type de contenu ou dans un autre. Il se peut par contre qu'elles soient désactivées pour certains types de contenu selon leurs spécificités et besoins.

### Image mise de l'avant et extrait

Lorsque vous modifiez un élément, certaines options sont disponibles dans la colonne de droite de l'éditeur. Deux éléments importants sont l'**image mise en avant** et l'**extrait**.

![](../img/post-types/featured-image_excerpt.jpg?raw=true)

L'**image mise en avant**, ou *featured image*, permet d'ajouter une vignette à un élément qui sera alors utilisée lors de l'affichage de cet élément à divers endroits, que ce soit sur une page d'archives* ou sur la page d'accueil. Elle est aussi utilisée au niveau du **référencement** de l'élément.

L'**extrait** permet d'ajouter une courte description à un élément qui sera alors utilisé lors de l'affichage de celui-ci à divers endroits, que ce soit sur une page d'archives* ou sur la page d'accueil. Il est aussi utilisé au niveau du **référencement** de l'élément.

*On apelle **page d'archives** une page qui liste la totalité des éléments d'un certain type de contenu. Par exemple, une page "Actualités" pourrait présenter la liste de tous les articles de nouvelle d'un site web. La page "Actualités" serait donc considérée comme la page d'archives des articles.

### Taxonomies

Pour tous les types de contenu, il est possible de créer des catégories qui leur sont propres pour pouvoir ainsi regrouper certains éléments sous un même sujet ou trait particulier. C'est ce qu'on appelle des *taxonomies*. Ces taxonomies sont souvent utiles pour filtrer les éléments d'un type de contenu par rapport à un certain sujet. D'un type de contenu à l'autre, ces catégories peuvent porter un nom différent et avoir différents éléments, mais le principe reste toujours le même pour pouvoir les modifier. Voici, par exemple, les thématiques d'un type de contenu appelé **Magazine**. 

![](../img/post-types/taxonomies_exemple.jpg?raw=true)

Cette section se retrouve dans la colonne de droite de l'éditeur lorsque vous modifiez un élément. Dans l'exemple ci-dessus, il vous est possible d'associer un magazine à plusieurs thématiques en **cochant celles existantes**. Il vous est aussi possible de créer une nouvelle thématique pour l'associer à votre magazine en cliquant sur **Ajouter un Thématique**.

Dans d'autres types de contenu, vous aurez non pas des thématiques, mais des catégories, ou encore des sujets. Le nom peut changer d'un type de contenu à l'autre, mais l'option pour les modifier sera cependant toujours sous cette même forme.


## Pages

Les pages sont le type de contenu le plus commun du site web. Elles servent principalement à déterminer les différentes sections du site web et à afficher du contenu informationnel.

Pour alimenter le contenu d'une page, on utilise l'éditeur de WordPress et les différents [blocs de contenu](blocks.md) disponibles.

Les pages sont **hiérarchiques**, c'est-à-dire qu'elles peuvent s'emboiter en plusieurs niveaux. Vous pouvez donc avoir une page, puis une sous-page de celle-ci, qu'on appelle aussi "page enfant". À l'inverse, on appelle "page parente" la page au-dessus. C'est donc grâce aux pages qu'on peut mettre en place l'**arborescence** du site.

Pour modifier la hiérarchie d'une page, aller modifier la page souhaitée et, dans la colonne de droite, sous la section **Attributs de page**, sélectionner la **page parente** souhaitée.

## Articles

Sur WordPress, les articles sont souvent utilisés pour créer des articles de nouvelle, et peuvent donc se retrouver dans la section **Actualités** (ou **Blogue**) d'un site. 

Contrairement aux pages, et comme la plupart des types de contenu, les articles sont **non-hiérarchiques**, c'est-à-dire que tous les articles sont au même niveau d'arborescence. Leur ordre est souvent déterminé par leur **date de publication**.


### Articles épinglés

Pour mettre de l'avant certains articles plus importants, il est possible de les **épingler**. Ils apparaitront alors au-dessus de tous les autres articles et ressortiront visuellement du lot.

Pour activer cette option, cocher la case **Épingler en haut du blog** dans la colonne de droite lorsque vous modifiez ou publiez un article. N'oubliez pas ensuite de mettre à jour l'article. 

![](../img/post-types/articles-epingles_option.jpg?raw=true)

**Note :** Pour un rendu optimal, ne garder idéalement qu'un seul article épinglé à la fois, ou du moins un nombre limité. Un nombre trop grand d'articles épinglés peut vite rendre le tout imposant sur la page d'archives des articles, et les articles épinglés ne seront plus autant mis à l'avant comme souhaité.