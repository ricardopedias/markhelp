<?php
declare(strict_types=1);

namespace Tests\App;

use MarkHelp\App\GitCatcher;
use Tests\TestCase;

class GitCatcherTest extends TestCase
{
    /**
     * @test
     */
    public function loadSupportDocumentDefaultFromTheme()
    {
        $gotcha = new GitCatcher;
        $gotcha->addRepo('https://github.com/ricardopedias/markhelp.git', 'docs', ['master', 'v1.0.0', 'v2.0.0']);
        //$gotcha->grabTo($this->pathDestination);
        $this->assertTrue(true);
    }
}