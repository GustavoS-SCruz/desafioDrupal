<?php
/**
  * @file
  * Functions to support theming for My Sub Theme.
  */
use \Drupal\Core\Asset\AttachedAssetsInterface;
/**
  * Implements hook_css_alter().
  *
  * Replace all css from Olivero by this theme css.
  */
function olivero_sub_theme_css_alter(&$css, AttachedAssetsInterface $assets) {
  $oliveroThemePath = drupal_get_path('theme', 'olivero');
  $mySubThemePath = drupal_get_path('theme', 'olivero_sub_theme');
  foreach ($css as $cssFile => $value) {
    if (strpos($cssFile, $oliveroThemePath) !== FALSE) {
      $css[$cssFile]['data'] = str_replace($oliveroThemePath, $mySubThemePath, $css[$cssFile]['data']);
    }
  }
}
