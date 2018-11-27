<?php

namespace Acquia\Orca;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Manages an environment made of bundles.
 */
class Kernel extends BaseKernel {

  const CONFIG_EXTS = '.{php,xml,yaml,yml}';

  /**
   * {@inheritdoc}
   */
  public function getCacheDir() {
    return $this->getProjectDir() . '/var/cache';
  }

  /**
   * {@inheritdoc}
   */
  public function getLogDir() {
    return $this->getProjectDir() . '/var/log';
  }

  /**
   * {@inheritdoc}
   */
  public function registerBundles() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function registerContainerConfiguration(LoaderInterface $loader): void {
    $loader->load($this->getProjectDir() . '/config/services.yml');
  }

  /**
   * {@inheritdoc}
   */
  protected function build(ContainerBuilder $container_builder): void {
    $container_builder->addCompilerPass($this->createCollectingCompilerPass());
    $container_builder->setParameter('app.fixture_dir', $this->getFixtureDir());
    $container_builder->setParameter('app.projects_config', $this->getProjectsConfig());
  }

  /**
   * Creates a collecting compiler pass.
   */
  private function createCollectingCompilerPass(): CompilerPassInterface {
    return new class implements CompilerPassInterface {

      /**
       * {@inheritdoc}
       */
      public function process(ContainerBuilder $container_builder) {
        $app_definition = $container_builder->findDefinition(Application::class);

        foreach ($container_builder->getDefinitions() as $definition) {
          if (!is_a($definition->getClass(), Command::class, TRUE)) {
            continue;
          }

          $app_definition->addMethodCall('add', [
            new Reference($definition->getClass()),
          ]);
        }
      }

    };
  }

  /**
   * Gets the fixture directory.
   *
   * @return string
   */
  protected function getFixtureDir(): string {
    return dirname($this->getProjectDir()) . '/orca-build';
  }

  /**
   * Gets the path to the projects configuration.
   *
   * @return string
   */
  private function getProjectsConfig(): string {
    $path = $this->getProjectDir();
    if (getenv('ORCA_SELF_TEST')) {
      $path .= '/tests/fixtures';
    }
    $path .= '/config/projects.yml';
    return $path;
  }

}
