<?php

namespace Drupal\Tests\node\Functional;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Tests making node base fields' displays configurable.
 *
 * @group node
 */
class NodeDisplayConfigurableTest extends NodeTestBase
{

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['rdf', 'block'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void
  {
    parent::setUp();
    $data = $this->getProvidedData();
    $theme = reset($data);
    \Drupal::service('theme_installer')->install([$theme]);
    $this->config('system.theme')->set('default', $theme)->save();
    $settings = [
      'theme' => $theme,
      'region' => 'content',
      'weight' => -100,
    ];
    $this->drupalPlaceBlock('page_title_block', $settings);
  }

  /**
   * Sets base fields to configurable display and check settings are respected.
   *
   * @param string $theme
   *   The name of the theme being tested.
   * @param string $metadata_region
   *   The region of the node html content where meta data is expected.
   * @param bool $field_classes
   *   If TRUE, check for field--name-XXX classes.
   *
   * @dataProvider provideThemes
   */
  public function testDisplayConfigurable(string $theme, string $metadata_region, bool $field_classes)
  {
    // Change the node type setting to show submitted by information.
    $node_type = \Drupal::entityTypeManager()->getStorage('node_type')->load('page');
    $node_type->setDisplaySubmitted(TRUE);
    $node_type->save();

    $user = $this->drupalCreateUser([
      'administer nodes',
    ], $this->randomMachineName(14));
    $this->drupalLogin($user);
    $node = $this->drupalCreateNode(['uid' => $user->id()]);
    $assert = $this->assertSession();

    // Check the node with Drupal default non-configurable display.
    $this->drupalGet($node->toUrl());
    $this->assertNodeHtml($node, $user, TRUE, $metadata_region, $field_classes);

    // Enable module to make base fields' displays configurable.
    \Drupal::service('module_installer')->install(['node_display_configurable_test']);

    // Configure display.
    $display = EntityViewDisplay::load('node.page.default');
    $display->setComponent('uid',
      [
        'type' => 'entity_reference_label',
        'label' => 'above',
        'settings' => ['link' => FALSE],
      ])
      ->save();

    // Recheck the node with configurable display.
    $this->drupalGet($node->toUrl());

    $this->assertNodeHtml($node, $user, FALSE, $metadata_region, $field_classes);

    $assert->elementExists('css', 'div[rel="schema:author"]');

    // Remove from display.
    $display->removeComponent('uid')
      ->removeComponent('created')
      ->save();

    $this->drupalGet($node->toUrl());
    $assert->elementNotExists('css', 'div[rel="schema:author"]');
    $assert->elementTextNotContains('css', 'article', $user->getAccountName());
  }

  /**
   * Asserts that the node HTML is as expected.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node being tested.
   * @param \Drupal\user\UserInterface $user
   *   The logged in user.
   * @param bool $is_inline
   *   Whether the fields are rendered inline or not.
   * @param string $metadata_region
   *   The region of the node html content where meta data is expected.
   * @param bool $field_classes
   *   If TRUE, check for field--name-XXX classes.
   *
   * @internal
   */
  protected function assertNodeHtml(NodeInterface $node, UserInterface $user, bool $is_inline, string $metadata_region, bool $field_classes): void
  {
    $assert = $this->assertSession();

    $html_element = $is_inline ? 'span' : 'div';
    $title_selector = 'h1 span' . ($field_classes ? '.field--name-title' : '');
    $assert->elementTextContains('css', $title_selector, $node->getTitle());

    // With field classes, the selector can be very specific.
    if ($field_classes) {
      $created_selector = 'article ' . $html_element . '.field--name-created';
      $assert->elementTextContains('css', $created_selector, \Drupal::service('date.formatter')->format($node->getCreatedTime()));
    } else {
      // When field classes aren't available, use HTML elements for testing.
      $formatted_time = \Drupal::service('date.formatter')->format($node->getCreatedTime());
      if ($is_inline) {
        $created_selector = sprintf('//article//%s//%s[text()="%s"]', $metadata_region, $html_element, $formatted_time);
      } else {
        $created_selector = sprintf('//article//%s[text()="%s"]', $html_element, $formatted_time);
      }
      $assert->elementExists('xpath', $created_selector);
    }

    $uid_selector = 'article ' . $html_element . ($field_classes ? '.field--name-uid' : '');
    if (!$is_inline) {
      $field_classes_selector = $field_classes ? "[contains(concat(' ', normalize-space(@class), ' '), ' field--name-uid ')]" : '';
      $assert->elementExists('xpath', sprintf('//article//%s//*%s//%s[text()="Authored by"]', $html_element, $field_classes_selector, $html_element));
      $assert->elementTextContains('css', "$uid_selector $html_element" . '[rel="schema:author"]', $user->getAccountName());
      $assert->elementNotExists('css', "$uid_selector a");
      $assert->elementExists('css', 'span[property="schema:dateCreated"]');
    } else {
      $assert->elementTextContains('css', $uid_selector . ' a[property="schema:name"]', $user->getAccountName());
      $assert->elementTextContains('css', 'article ' . $metadata_region, 'Submitted by');
    }
  }

  /**
   * Data provider for ::testDisplayConfigurable().
   *
   * @return array
   */
  public function provideThemes()
  {
    return [
      ['bartik', 'header', TRUE],
      ['claro', 'footer', TRUE],
      ['classy', 'footer', TRUE],
      // @todo Add coverage for olivero after fixing
      // https://www.drupal.org/project/drupal/issues/3215220.
      // ['olivero', 'footer', TRUE],
      ['seven', 'footer', TRUE],
      ['stable', 'footer', FALSE],
      ['stable9', 'footer', FALSE],
    ];
  }

}
