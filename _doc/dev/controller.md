### Créer un contrôleur de partiel

Créer un contrôleur permet de déplacer la logique relié à l'initialisation des datas pour un partiel.

À placer dans /inc/controller dans un fichier controller.[nom_du_partiel].php
Chaîne à remplacer dans l'exemple de code ci-bas :
* {PARTIAL_NAME}

```
add_filter( 'get_partial_args/templates/{PARTIAL_NAME}', 'control_template_data_{PARTIAL_NAME}' );
function control_template_data_{PARTIAL_NAME}( $template_data ) {
  $template_data = array_merge(
    array(

    ),
    $template_data
  );

  return $template_data;
}
