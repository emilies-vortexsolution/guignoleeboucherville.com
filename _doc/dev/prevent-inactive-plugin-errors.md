### Ajouter une prévention d'erreurs d'extensions inactifs

Pour éviter que le prochain dévelopeur se retrouve avec des erreurs fatales en installant le thème pour la première fois, il est possible d'ajouter des fonctions/classes qui prendront leur place le temps de les activer.

À placer dans /inc/plugins-errors-prevention dans le fichier prevention-instances.php

#### Pour ajouter une fonction
Ajouter chaque fonction utilisé sous le même `function_exists()`.
```
/////////////////////////////////////////
// Plugin name
/////////////////////////////////////////
if ( ! function_exists( 'some_function ) ) {
  vtx_add_inactive_plugin( 'plugin_name' );
  
  // @codingStandardsIgnoreLine
  function get_field( ...$args ) {
    vtx_notice_use_of_inactive_plugin( __FUNCTION__ );
  }
}
```

#### Pour ajouter une classe
Si la classe est seulement utilisé comme `new Plugin_Class()`, la version simple suffira.
```
/////////////////////////////////////////
// Plugin name simple
/////////////////////////////////////////
if ( ! class_exists( 'Plugin_Class_Simple' ) ) {
  vtx_add_inactive_plugin( 'plugin_name_simple' );
  class Plugin_Class extends VTX_Class_Placeholder {

    // @codingStandardsIgnoreLine
    public function __construct( ...$args ) {
      vtx_notice_use_of_inactive_plugin( __CLASS__ );
    }
  }
}
```

Si on doit prévenir l'accès une méthode ou propriété statique, il faudra la préciser en plus.
```
/////////////////////////////////////////
// Plugin name complex
/////////////////////////////////////////
if ( ! class_exists( 'Plugin_Class_Complex' ) ) {
  vtx_add_inactive_plugin( 'plugin_name_complex' );
  class Plugin_Class extends VTX_Class_Placeholder {
    public static $plugin_property = 'Plugin name not active';
    
    // @codingStandardsIgnoreLine
    public function __construct( ...$args ) {
      vtx_notice_use_of_inactive_plugin( __CLASS__ );
    }
    
    // @codingStandardsIgnoreLine
    public static function plugin_method( ...$args ) {
      vtx_notice_use_of_inactive_plugin( __METHOD__ );
    }
  }
}
```

Dans le cas ou il serait nécessaire de déterminer si un plugin est actif ou non malgré les placeholders (plug moyen d'utiliser `function_exists` voyez-vous), il sera possible d'utiliser `vtx_is_plugin_inactive( 'plugin_name' )`. Tous les placeholders ayant utilisé `vtx_add_inactive_plugin( 'plugin_name )` vont retourner `TRUE`.