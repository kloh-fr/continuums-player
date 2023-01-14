<?php
/*
 * Plugin Name: Continuum(s) Player
 * Description: Lecteur audio et vidéo pour le site Continuum(s)
 * Version: 2014.05.05
 * Text Domain: continuums-player
 * @author Luc Poupard
 * @link http://www.kloh.ch
*/

/* ----------------------------- */
/* Sommaire */
/* ----------------------------- */
/*
  == Traduction
  == Custom Post Types
    -- Vidéos
    -- Sons
  == Shortcodes
    -- Vidéos
    -- Sons
  == Injection CSS et JS
  == Admin
    -- Création d'un bloc pour afficher le shortcode prêt à l'emploi
    -- Ajout du bloc Référencement naturel du thème ffeeeedd sur les contenus audio/vidéo
*/


/* == @section Traduction ==================== */
/**
 * @author Luc Poupard
 * @note I18n : déclare le domaine et l’emplacement des fichiers de traduction
 * @link https://codex.wordpress.org/I18n_for_WordPress_Developers
*/
function continnums_player_init() {
  $plugin_dir = basename( dirname( __FILE__ ) );
  load_plugin_textdomain( 'continuums-player', false, $plugin_dir );
}

add_action( 'plugins_loaded', 'continnums_player_init' );


/* == @section Custom Post Types ==================== */
/**
*  @author Luc Poupard
*  @see http://www.kevinleary.net/wordpress-dashicons-custom-post-types/
*/

add_action( 'init', 'continuums_player_post_type' );

function continuums_player_post_type() {  
  /* -- @subsection Vidéos -------------------- */
  register_post_type( 'continuums_video',
    array(
      'labels' => array(
        'name' => __( 'Vidéos', 'continuums-plugins' ),
        'singular_name' => __( 'Vidéo', 'continuums-plugins' ),
        'add_new_item' => __( 'Ajouter une nouvelle vidéo', 'continuums-plugins' ),
        'new_item' => __( 'Nouvelle vidéo', 'continuums-plugins' ),
        'edit_item' => __( 'Éditer une vidéo', 'continuums-plugins' ),
        'view_item' => __( 'Voir la vidéo', 'continuums-plugins' ),
        'all_items' => __( 'Toutes les vidéos', 'continuums-plugins' ),
        'search_items' => __( 'Chercher des vidéos', 'continuums-plugins' ),
        'not_found' => __( 'Pas de vidéo trouvée.', 'continuums-plugins' ),
        'not_found_in_trash' => __( 'Pas de vidéo trouvée dans la corbeille.', 'continuums-plugins' )
      ),
      'public' => true,
      'exclude_from_search' => true,
      'show_in_nav_menus' => false,
      'menu_position' => 10,
      'menu_icon' => 'dashicons-video-alt3',
      'supports' =>  array(
        'title',
        'editor'
      ),
      'rewrite' => array(
        'slug' => 'video'
      )
    )
  );

  /* -- @subsection Sons -------------------- */
  register_post_type( 'continuums_audio',
    array(
      'labels' => array(
        'name' => __( 'Sons', 'continuums-plugins' ),
        'singular_name' => __( 'Son', 'continuums-plugins' ),
        'add_new_item' => __( 'Ajouter un nouveau son', 'continuums-plugins' ),
        'new_item' => __( 'Nouveau son', 'continuums-plugins' ),
        'edit_item' => __( 'Éditer un son', 'continuums-plugins' ),
        'view_item' => __( 'Voir le son', 'continuums-plugins' ),
        'all_items' => __( 'Tous les sons', 'continuums-plugins' ),
        'search_items' => __( 'Chercher des sons', 'continuums-plugins' ),
        'not_found' => __( 'Pas de son trouvé.', 'continuums-plugins' ),
        'not_found_in_trash' => __( 'Pas de son trouvé dans la corbeille.', 'continuums-plugins' )
      ),
      'public' => true,
      'exclude_from_search' => true,
      'show_in_nav_menus' => false,
      'menu_position' => 10,
      'menu_icon' => 'dashicons-format-audio',
      'supports' =>  array(
        'title',
        'editor'
      ),
      'rewrite' => array(
        'slug' => 'audio'
      ),
      'register_meta_box_cb' => 'continuums_player_metabox'
    )
  );
}


/* == @section Shortcodes ==================== */
/**
*  @author Luc Poupard
*  @see http://wp-themes-pro.com/shortcode-wordpress/
*/

/* -- @subsection Vidéos -------------------- */
/* Récupération valeur custom post types
 * @see http://wordpress.stackexchange.com/questions/36337/echo-custom-field-value-in-shortcode-function
 * @see http://www.advancedcustomfields.com/resources/functions/get_field/
 */
function continnums_player_video() {
  $posts = get_posts( array( 'post_type' => 'continuums_video' ) );
  if( isset( $posts ) && !empty( $posts ) ) {
    foreach( $posts as $post ) {
      
      // On récupère les valeurs des champs du Custom Post Type Vidéo

      // Méthode 1 : fonction get_field() spécifique à l'extention Advanced Custom Fields
      //$vid_preview = get_field( 'preview', $post->ID );
      //$vid_mp4 = get_field( 'fichier_mp4', $post->ID );
      //$vid_webm = get_field( 'fichier_webm', $post->ID );
      //$vid_swf = get_field( 'fichier_swf', $post->ID );
      //$vid_webvtt = get_field( 'fichier_webvtt', $post->ID );

      // Méthode 2 : fonction get_post_meta() plus performante et plus évolutive (non dépendant de l'extention ACF)
      $video_id = $post->ID;
      $video_link = get_permalink( $post->ID );
      /* Poster */
      $preview_file = get_post_meta( $post->ID, 'preview', true );
      $preview_video = wp_get_attachment_url( $preview_file );
      /* MP4 */
      $mp4_file = get_post_meta( $post->ID, 'fichier_mp4', true );
      $mp4_video = wp_get_attachment_url( $mp4_file );
      /* WebM */
      $webm_file = get_post_meta( $post->ID, 'fichier_webm', true );
      $webm_video = wp_get_attachment_url( $webm_file );
      /* WebVTT */
      $webvtt_file = get_post_meta( $post->ID, 'fichier_webvtt', true );
      $webvtt_video = wp_get_attachment_url( $webvtt_file );
      /* Récupération des dimensions de la vidéo */ 
      $video_metadata = wp_get_attachment_metadata( $mp4_file );
      $video_height = $video_metadata['height'];
      $video_width = $video_metadata['width'];

      // Ouverture du lecteur
      if ( $preview_video ) {
        // Si on a une image de preview, on l'insère dans l'attribut poster
        $continuums_video = '<video id="video-' . $video_id . '" width="' . $video_width . '" height="' . $video_height . '" controls="controls" poster="' . esc_url( $preview_video ) . '" preload="none">';
      } else {        
        // Si on n'a pas d'image de preview, on n'insère pas d'attribut poster
        $continuums_video = '<video id="video-' . $video_id . '" width="' . $video_width . '" height="' . $video_height . '" controls="controls" preload="none">';
      }

      // Si on a un fichier vidéo MP4, on l'insère
      if ( $mp4_video ) {
        $continuums_video .= '<source type="video/mp4" src="' . esc_url( $mp4_video ) . '" />';
      }

      // Si on a un fichier vidéo WebM, on l'insère
      if ( $webm_video ) {
        $continuums_video .= '<source type="video/webm" src="' . esc_url( $webm_video ) . '" />';
      }

      // Si on a un fichier de sous-titres WebVTT, on l'affiche
      if ( $webvtt_video ) {
        $continuums_video .= '<track kind="subtitles" src="' . esc_url( $webvtt_video ) . '" srclang="fr" label="' . __( 'Français', 'continuums-player' ) . '" />';
      }

      // Fallback avec la balise object
      if ( $mp4_video) {
        $continuums_video .= '<object width="' . $video_width . '" height="' . $video_height . '" type="application/x-shockwave-flash" data="' . plugins_url( '/js/flashmediaelement.swf', __FILE__ ) . '">
        <param name="movie" value="' . plugins_url( '/js/flashmediaelement.swf', __FILE__ ) . '" />';
        $continuums_video .= '<param name="flashvars" value="controls=true&file=' . esc_url( $mp4_video ) . '" />';
      }

      // Message d'erreur si impossibilité d'afficher la vidéo
      $continuums_video .= '<p>' . __( 'Votre navigateur ne peut pas afficher de vidéo.', 'continuums-player' ) . '</p>';

      // Fermeture du lecteur
      $continuums_video .= '</object>
      </video>';

      // Si et seulement si on a au moins une source vidéo, on affiche le lecteur
      if ( $mp4_video or $webm_video or $swf_video ) {
        return $continuums_video;
      }
    }
  }
}

add_shortcode('continuums-video', 'continnums_player_video');

/* -- @subsection Sons -------------------- */
/* Récupération valeur custom post types
 * @see http://wordpress.stackexchange.com/questions/36337/echo-custom-field-value-in-shortcode-function
 * @see http://www.advancedcustomfields.com/resources/functions/get_field/
 */
function continnums_player_audio() {
  $posts = get_posts( array( 'post_type' => 'continuums_audio' ) );
  if( isset( $posts ) && !empty( $posts ) ) {
    foreach( $posts as $post ) {
      
      // On récupère les valeurs des champs du Custom Post Type Audio

      // Méthode 1 : fonction get_field() spécifique à l'extention Advanced Custom Fields
      //$aud_mp3 = get_field( 'fichier_mp3', $post->ID );
      //$aud_ogg = get_field( 'fichier_ogg', $post->ID );

      // Méthode 2 : fonction get_post_meta() plus performante et plus évolutive (non dépendant de l'extention ACF)
      /* Poster */
      $audio_id = $post->ID;
      $audio_link = get_permalink( $post->ID );
      /* MP3 */
      $mp3_file = get_post_meta( $post->ID, 'fichier_mp3', true );
      $mp3_audio = wp_get_attachment_url( $mp3_file );
      /* Ogg */
      $ogg_file = get_post_meta( $post->ID, 'fichier_ogg', true );
      $ogg_audio = wp_get_attachment_url( $ogg_file );

      // Ouverture du lecteur
      $continuums_audio = '<audio id="audio-' . $audio_id . '" controls="controls" preload="none">';

      // Si on a un fichier audio MP3, on l'insère
      if ( $mp3_audio ) {
        $continuums_audio .= '<source type="audio/mp3" src="' . esc_url( $mp3_audio ) . '" />';
      }

      // Si on a un fichier audio Ogg, on l'insère
      if ( $ogg_audio ) {
        $continuums_audio .= '<source type="audio/ogg" src="' . esc_url( $ogg_audio ) . '" />';
      }

      // Fermeture du lecteur
      $continuums_audio .= '</audio>';

      // Lien de la transcription (sauf si on est sur la page de la transcription)
      if ( ! is_singular( 'continuums_audio' ) ) {
        $continuums_audio .= '<p><a href="' . esc_url( $audio_link ) . '">' . __( 'Transcription du fichier son', 'continuums-player' ) . '</a> ' . esc_attr( $audio_size ) . '</p>';
      }

      // Si et seulement si on a au moins une source audio, on affiche le lecteur
      if ( $mp3_audio or $ogg_audio ) {
        return $continuums_audio;
      }
    }
  }
}

add_shortcode('continuums-son', 'continnums_player_audio');


/* == @section Afficher les articles où apparaissent le son ou la vidéo
function continuums_player_linked_posts() {
  global $post;
  $post_title = get_the_title( $post->ID );
  // args
  $args = array(
    'numberposts' => -1,
    'post_type' => 'continuums_audio',
    'meta_key' => 'title',
    'meta_value' => $post_title
  );
  $the_query = new WP_Query( $args );

  if( $the_query -> have_posts() ) {
    while ( $the_query -> have_posts() ) {
    echo '<a href="' . $parent_permalink . '">' . $parent_title . '</a>';

    }
  }
} */


/* == @section Injection CSS et JS ==================== */
/* -- @subsection Ajouter les scripts en bas de page -------------------- */

function continuums_player_script() {
  global $post;

  /* On vérifie l'existence du shortcode, même s'il est imbriqué
   * @note has_shortcode ne permet pas vérifier les shortcodes imbriqués dans un autre shortcode
   * @see http://wordpress.stackexchange.com/questions/126563/has-shortcode-how-to-detect-nested-shortcode
   */
  $page_id = get_queried_object_id();
  $page_object = get_page( $page_id );
  if ( strpos($page_object->post_content, '[continuums-son') ) {
    $shortcode_son = true;
  }
  if ( strpos($page_object->post_content, '[continuums-video') ) {
    $shortcode_video = true;
  }

  // On injecte les scripts si la page est un article ou une page et a le shortcode [continuums-son]…
  if ( is_singular() && ( $shortcode_son === true )
  // … si la page est un article ou une page et a le shortcode [continuums-video]…
  or is_singular() && ( $shortcode_video === true )
  // … ou si la page est la page de la transcription du média
  or is_singular( array(
    'continuums_audio',
    'continuums_video'
  ) ) ) {
    wp_enqueue_script(
      'player',
      '/wp-content/plugins/continuums-player/js/mediaelement-and-player.js',
      array( 'jquery' ),
      '20140429',
      true
    );
    wp_enqueue_script(
      'player-init',
      '/wp-content/plugins/continuums-player/js/mediaelement-init.js',
      array( 'jquery', 'player' ),
      '20140429',
      true
    );
  }
}

add_action( 'wp_enqueue_scripts', 'continuums_player_script' );


/* == @section Admin ==================== */
/* -- @subsection Création d'un bloc pour afficher le shortcode prêt à l'emploi -------------------- */
/* Ajout d'une metabox pour récupérer le shortcode audio/vidéo
 * @see http://wptheming.com/2010/08/custom-metabox-for-post-type/
 */
function continuums_player_metabox() {
  add_meta_box( 'continuums_player_meta', __( 'Shortcode', 'continuums-player' ), 'continuums_player_shortcode', 'continuums_audio', 'side', 'default' );
  add_meta_box( 'continuums_player_meta', __( 'Shortcode', 'continuums-player' ), 'continuums_player_shortcode', 'continuums_video', 'side', 'default' );
}

add_action( 'add_meta_boxes', 'continuums_player_metabox' );


function continuums_player_shortcode( $post ) {
  global $post;
  echo '<p>' . __( 'Shortcode à copier/coller dans l’article pour insérer le lecteur&nbsp;:', 'continuums-player' ) . '</p>';
  echo '<p>[continuums-son id="' . $post->ID . '"]</p>';
}

/*
function continuums_player_save( $post_ID ) {
}
add_action( 'save_post', 'continuums_player_save' );
*/

/* -- @subsection Ajout du bloc Référencement naturel du thème ffeeeedd sur les contenus audio/vidéo -------------------- */
if ( ! function_exists( 'ffeeeedd__metabox' ) ) {
  function continuums_ffeeeedd_metabox() {
    add_meta_box( 'ffeeeedd__metabox__seo', __( 'SEO', 'ffeeeedd' ), 'ffeeeedd__metabox__contenu', 'continuums_audio', 'side', 'high' );
    add_meta_box( 'ffeeeedd__metabox__seo', __( 'SEO', 'ffeeeedd' ), 'ffeeeedd__metabox__contenu', 'continuums_video', 'side', 'high' );
  }

  add_action( 'add_meta_boxes', 'continuums_ffeeeedd_metabox' );
}