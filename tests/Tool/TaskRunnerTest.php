<?php

namespace Acquia\Orca\Tests\Tool;

use Acquia\Orca\Console\Helper\StatusCode;
use Acquia\Orca\Helper\Task\TaskRunner;
use Acquia\Orca\Tool\PhpLint\PhpLintTask;
use Acquia\Orca\Tool\TaskInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @property \Prophecy\Prophecy\ObjectProphecy|\Acquia\Orca\Tool\ComposerValidate\ComposerValidateTask $composerValidateTask
 * @property \Prophecy\Prophecy\ObjectProphecy|\Acquia\Orca\Tool\PhpLint\PhpLintTask $phplintTask
 */
class TaskRunnerTest extends TestCase {

  private const PATH = 'var/www/example';

  private const STATUS_MESSAGE = 'Printing status message';

  public function testTaskRunner(): void {
    $output = $this->prophesize(SymfonyStyle::class);
    $output->section(self::STATUS_MESSAGE)
      ->shouldBeCalledTimes(1);
    $output = $output->reveal();
    $phplint = $this->setTaskExpectations(PhpLintTask::class);

    $runner = new TaskRunner($output);
    $runner->setPath('foobar')
      ->addTask($phplint)
      ->setPath(self::PATH);
    $status_code = $runner->run();
    // Make sure tasks are reset on clone.
    (clone($runner))->run();

    self::assertInstanceOf(TaskRunner::class, $runner, 'Instantiated class.');
    self::assertEquals(StatusCode::OK, $status_code, 'Returned a "success" status code.');
  }

  protected function setTaskExpectations($class): TaskInterface {
    $task = $this->prophesize($class);
    $task->statusMessage()
      ->shouldBeCalledTimes(1)
      ->willReturn(self::STATUS_MESSAGE);
    $task->setPath(self::PATH)
      ->shouldBeCalledTimes(1)
      ->willReturn($task);
    $task->execute()->shouldBeCalledTimes(1);
    $task = $task->reveal();
    return $task;
  }

}