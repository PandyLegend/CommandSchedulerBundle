<?php

namespace Dukecity\CommandSchedulerBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AddCommandTest.
 */
abstract class AbstractCommandTest extends WebTestCase
{
    use FixturesTrait;

    /** @var EntityManager */
    protected EntityManager $em;

    /** @var CommandTester|null */
    protected CommandTester | null $commandTester;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * This helper method abstracts the boilerplate code needed to test thetes
     * execution of a command.
     * @link https://symfony.com/doc/current/console.html#testing-commands
     *
     * @param string $commandClass
     * @param array $arguments All the arguments passed when executing the command
     * @param array $inputs The (optional) answers given to the command
     * @param int $expectedExitCode
     * @return CommandTester
     */
    protected function executeCommand(string $commandClass, array $arguments = [], array $inputs = [], int $expectedExitCode=0): CommandTester
    {
        // this uses a special testing container that allows you to fetch private services

        if(!is_subclass_of($commandClass, Command::class))
        {throw new InvalidArgumentException("Not a command class");}
        #$this->assertInstanceOf(Command::class, $commandClass);

        /* https://symfony.com/doc/current/console.html#testing-commands
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        */

        $cmd = self::$container->get($commandClass);
        $cmd->setApplication(new Application('Test'));

        #var_dump($cmd->getDefinition()); die();
        # scheduler:add <name> <cmd> <arguments> <cronExpression> [<logFile> [<priority> [<executeImmediately> [<disabled>]]]]
        #var_dump($cmd->getSynopsis()()); die();

        $commandTester = new CommandTester($cmd);
        $commandTester->setInputs($inputs);
        $result = $commandTester->execute($arguments, ["capture_stderr_separately"]);

        $this->assertSame($expectedExitCode, $result);

        if($result !== $expectedExitCode)
        {var_dump($commandTester->getErrorOutput());}

        return $commandTester;
    }
}
