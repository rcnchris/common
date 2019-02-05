<?php
use Rcnchris\Common\Cmd;
use Tests\Rcnchris\Common\BaseTestCase;

class CmdTest extends BaseTestCase
{
    /**
     * Obtenir une instance de Cmd
     *
     * @param string|null $commands Commande shell à exécuter
     *
     * @return \Rcnchris\Common\Cmd
     */
    public function makeCmd($commands = null)
    {
        return new Cmd($commands);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Shell');
        $this->assertInstanceOf(Cmd::class, new Cmd());
    }

    public function testHelp()
    {
        $o = $this->makeCmd();
        $this->assertHasHelp($o);
    }

    public function testAddOneCommandOnInstance()
    {
        $cmd = $this->makeCmd('pwd');
        $this->assertEquals('pwd', $cmd->getCommands());
    }

    public function testAddOneCommand()
    {
        $cmd = $this->makeCmd()->add('pwd');
        $this->assertEquals('pwd', $cmd->getCommands());
    }

    public function testAddNCommand()
    {
        $cmd = $this->makeCmd()->add('cd .. && ls -lAF');
        $this->assertInternalType('array', $cmd->getCommands());
        $this->assertCount(2, $cmd->getCommands());
    }

    public function testExecOneCommandOneLine()
    {
        $result = $this->makeCmd()->add('pwd')->exec();
        $this->assertInternalType('string', $result);
    }

    public function testExecOneCommandNLines()
    {
        $result = $this->makeCmd()->add('ls -lAF')->exec();
        $this->assertInternalType('array', $result);
    }

    public function testExecNCommands()
    {
        $result = $this->makeCmd()->add('cd .. && pwd && ls -lAF')->exec();
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
    }

    public function testExecOneCommandWithInfosInResult()
    {
        $result = $this
            ->makeCmd()
            ->add('ls -lAF')
            ->exec(true);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKeys(['cmd', 'result', 'time', 'ret'], $result);
    }

    public function testExecNCommandsWithInfosInResult()
    {
        $result = $this
            ->makeCmd()
            ->add('cd .. && pwd && ls -lAF')
            ->exec(true);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        foreach ($result as $r) {
            $this->assertArrayHasKeys(['cmd', 'result', 'time', 'ret'], $r);
        }
    }

    public function testExecWrongCommand()
    {
        $result = $this->makeCmd('lll')->exec();
        $this->assertInternalType('string', $result);
        $this->assertContains('not found', $result);
    }
}
