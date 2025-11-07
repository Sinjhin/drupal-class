<?php

/**
 * Script to create TODO content type for decoupled Drupal.
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

$autoloader = require_once '/var/www/html/web/autoload.php';
$kernel = DrupalKernel::createFromRequest(Request::createFromGlobals(), $autoloader, 'prod');
$kernel->boot();
$kernel->prepareLegacyRequest(Request::createFromGlobals());

// Create TODO content type
$content_type = NodeType::create([
  'type' => 'todo',
  'name' => 'TODO',
  'description' => 'A TODO item for the task list',
]);
$content_type->save();

echo "Created TODO content type\n";

// Add description field (Body field is added by default)
// Add completed checkbox field
$field_storage = FieldStorageConfig::create([
  'field_name' => 'field_completed',
  'entity_type' => 'node',
  'type' => 'boolean',
]);
$field_storage->save();

$field = FieldConfig::create([
  'field_storage' => $field_storage,
  'bundle' => 'todo',
  'label' => 'Completed',
  'required' => FALSE,
  'default_value' => [['value' => 0]],
]);
$field->save();

echo "Created completed field\n";

// Configure form display
$form_display = \Drupal::entityTypeManager()
  ->getStorage('entity_form_display')
  ->load('node.todo.default');

if (!$form_display) {
  $form_display = \Drupal::entityTypeManager()
    ->getStorage('entity_form_display')
    ->create([
      'targetEntityType' => 'node',
      'bundle' => 'todo',
      'mode' => 'default',
      'status' => TRUE,
    ]);
}

$form_display->setComponent('field_completed', [
  'type' => 'boolean_checkbox',
  'weight' => 10,
])->save();

echo "Configured form display\n";

// Configure view display
$view_display = \Drupal::entityTypeManager()
  ->getStorage('entity_view_display')
  ->load('node.todo.default');

if (!$view_display) {
  $view_display = \Drupal::entityTypeManager()
    ->getStorage('entity_view_display')
    ->create([
      'targetEntityType' => 'node',
      'bundle' => 'todo',
      'mode' => 'default',
      'status' => TRUE,
    ]);
}

$view_display->setComponent('field_completed', [
  'type' => 'boolean',
  'weight' => 10,
  'label' => 'above',
])->save();

echo "Configured view display\n";
echo "TODO content type created successfully!\n";
