<?php
use Rcnchris\Common\Cmd;
use Tests\Rcnchris\Common\BaseTestCase;

class CmdTest extends BaseTestCase
{
    /**
     * @var Cmd
     */
    private $cmd;

    public function setUp()
    {
        $this->cmd = $this->makeStaticCmd();
    }

    /**
     * Obtenir une instance de Cmd
     *
     * @return Cmd
     */
    public function makeStaticCmd()
    {
        return Cmd::getInstance();
    }

    /**
     * @return Cmd
     */
    public function makeCmd()
    {
        return new Cmd();
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Shell');
        $this->assertInstanceOf(Cmd::class, Cmd::getInstance());
        $this->assertInstanceOf(Cmd::class, new Cmd());
    }

    public function testHelp()
    {
        $o = $this->makeStaticCmd();
        $this->assertHasHelp($o);
    }

    public function testExec()
    {
        $path = $this->rootPath();
        $cmd = $this->makeStaticCmd();
        $ret = $cmd->exec("cd $path && ls");
        $this->assertCount(1, $cmd->getCmds());
        $this->assertCount(1, $cmd->getCmds(true));
        $this->assertArrayHasKey('cmd', $cmd->getCmds()[0]);
        $this->assertArrayHasKey('time', $cmd->getCmds()[0]);
        $this->assertArrayHasKey('result', $cmd->getCmds()[0]);
        $this->assertArrayHasKey('ret', $cmd->getCmds()[0]);

        $this->assertContains('composer.json', $cmd->getCmds(true)[0]);
    }

    public function testExecWrongCommand()
    {
        ob_start();
        $c = $this->makeStaticCmd();
        $c->exec('lll');
        $this->assertEquals(1, count($c->getCmds()));
        $this->assertArrayHasKey('cmd', $c->getCmds()[0]);
        $content = ob_get_clean();
    }

    public function testExecMultipleCommands()
    {
        $this->assertInternalType('array', Cmd::exec('ls && pwd'));
    }

    public function testExecMultipleCommandsSeparate()
    {
        $path = dirname(__DIR__);
        $c = $this->makeStaticCmd();
        $ret = $c->exec("cd $path && ls", true);
        $this->assertEquals(2, count($c->getCmds()));
        $this->assertEquals(2, count($c->getCmds(true)));
    }

    public function testGetCommands()
    {
        $this->cmd->exec('ls && pwd');
        $this->assertNotEmpty($this->cmd->getCmds());
    }

    public function testGetGitVersion()
    {
        $this->assertInternalType('string', $this->cmd->git());
    }

    public function testGetSizeWithDir()
    {
        $this->assertEquals('git version', substr($this->makeStaticCmd()->git(), 0, 11));
    }
}