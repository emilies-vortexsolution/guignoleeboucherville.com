# Traduction avec le fichier de configuration wpml

Comme pour ACF et FacetWP, les traductions doivent etre définies dans un fichier de configuration plutot qu'en base de donnée afin de faciliter les migrations. 

Si le fichier de configuration `wpml-config.xml` est déja présent à la racine du theme, veuillez modifier les parametres de wpml directement dans le fichier (vous verrez un cadenas à coté des champs que vous souhaitez configurer dans les options de wpml si le parametre est défini dans le fichier de configuration). Détails [ici](https://wpml.org/documentation/support/language-configuration-files/) pour la structure de la configuration

Si le fichier wpml-config n'existe pas encore, veuillez suivre la procedure pour le générer:
1. Une fois que WPML est installé, veuillez le configurer comme souhaité
2. Installez le plugin [Multilingual Tools](https://github.com/OnTheGoSystems/multilingual-tools) en le téléchargeant depuis son repo github
3. Dans  `Multilingual Tools` --> `Configuration Generator`, cochez les paramètres voulu puis cliquez sur generate.

Le fichier sera crée à la racine. 

Les détails de paramètres du fichier peuvent etre trouvés [ici](https://wpml.org/documentation/support/language-configuration-files/)