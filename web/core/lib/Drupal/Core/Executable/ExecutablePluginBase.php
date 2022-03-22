<?php

namespace Drupal\Core\Executable;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginTrait;
use Drupal\Core\Plugin\PluginBase;

/**
 * Provides the basic architecture for executable plugins.
 */
abstract class ExecutablePluginBase extends PluginBase implements ExecutableInterface, CacheableDependencyInterface, ContextAwarePluginInterface
{

  use ContextAwarePluginTrait;

  /**
   * Gets an array of definitions of available configuration options.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   An array of typed data definitions describing available configuration
   *   options, keyed by option name.
   * @todo: This needs to go into an interface.
   *
   */
  public function getConfigDefinitions()
  {
    $definition = $this->getPluginDefinition();
    if (!empty($definition['configuration'])) {
      return $definition['configuration'];
    }
    return [];
  }

  /**
   * Gets the definition of a configuration option.
   *
   * @param string $key
   *   The key of the configuration option to get.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface|false
   *   The typed data definition describing the configuration option, or FALSE
   *   if the option does not exist.
   * @todo: This needs to go into an interface.
   *
   */
  public function getConfigDefinition($key)
  {
    $definition = $this->getPluginDefinition();
    if (!empty($definition['configuration'][$key])) {
      return $definition['configuration'][$key];
    }
    return FALSE;
  }

  /**
   * Gets all configuration values.
   *
   * @return array
   *   The array of all configuration values, keyed by configuration option
   *   name.
   * @todo: This needs to go into an interface.
   *
   */
  public function getConfig()
  {
    return $this->configuration;
  }

  /**
   * Sets the value of a particular configuration option.
   *
   * @param string $key
   *   The key of the configuration option to set.
   * @param mixed $value
   *   The value to set.
   *
   * @return $this
   *   The executable object for chaining.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If the provided configuration value does not pass validation.
   * @todo This doesn't belong here. Move this into a new base class in
   *   https://www.drupal.org/node/1764380.
   * @todo This does not set a value in \Drupal::config(), so the name is confusing.
   *
   */
  public function setConfig($key, $value)
  {
    if ($definition = $this->getConfigDefinition($key)) {
      $typed_data = \Drupal::typedDataManager()->create($definition, $value);

      if ($typed_data->validate()->count() > 0) {
        throw new PluginException("The provided configuration value does not pass validation.");
      }
    }
    $this->configuration[$key] = $value;
    return $this;
  }

}
