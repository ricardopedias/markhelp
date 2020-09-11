<?php
declare(strict_types=1);

namespace Tests;

use Exception;

class ConsoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    private function call(string $command): string
    {
        $command = "cd {$this->pathRoot}; $command";

        $output = [];
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception("Command '{$command}' failed (exit-code {$returnCode}).", $returnCode);
        }

        return implode("\n", $output);
    } 

    /**
     * Este é um teste falso, apenas para atualizar a versão do arquivo version.app 
     * @test 
     */
    public function generateVersion()
    {
        $release = trim($this->call("git describe --tags $(git rev-list --tags --max-count=1)"));
        $release = preg_replace("/v(.*)/", "$1", $release);

        $versionFile = $this->normalizePath("{$this->pathRoot}/version.app");
        file_put_contents($versionFile, $release, LOCK_EX);
        
        $this->assertTrue(true);
    }

    /** @test */
    public function showHelp()
    {
        $result = $this->call("./markhelp");
        $this->assertStringContainsString('Usage:', $result);

        $result = $this->call("./markhelp --help");
        $this->assertStringContainsString('Usage:', $result);
    }

    /** @test */
    public function renderInvalidSource()
    {
        // Este teste dispara a mensagem de erro liberada pelo comando
        $pathProject = "{$this->pathReleases}/v9.9.9";
        $result = $this->call("./markhelp -i {$pathProject} -o {$this->pathDestination}");
        $this->assertStringContainsString('The specified source is not a valid path', $result);
    }

    /** @test */
    public function renderInvalidOutput()
    {
        // Este teste dispara a mensagem de erro liberada pelo comando
        $pathProject = "{$this->pathReleases}/v1.0.0";
        $result = $this->call("./markhelp -i {$pathProject} -o {$this->pathDestination}/invalid");
        $this->assertStringContainsString('The specified destination is not a valid path', $result);
    }

    /** @test */
    public function renderSourceEqualOutput()
    {
        // Este teste dispara a mensagem de erro liberada pelas bibliotecas
        $pathProject = "{$this->pathReleases}/v1.0.0";
        $result = $this->call("./markhelp -i {$pathProject} -o {$pathProject}");
        $this->assertStringContainsString('Source directory cannot be the same as the destination directory', $result);
    }

    /** @test */
    public function renderInputAndOutput()
    {
        $pathProject = "{$this->pathReleases}/v1.0.0";

        $result = $this->call("./markhelp -i {$pathProject} -o {$this->pathDestination}");
        $this->assertStringContainsString('Documentation site successfully generated', $result);

        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertFileExists("{$this->pathDestination}/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");

        $this->assertDirectoryExists("{$this->pathDestination}/images");
        $this->assertFileExists("{$this->pathDestination}/images/example.png");
        $this->assertFileExists("{$this->pathDestination}/images/logo.png");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page.html");
    }

    /** @test */
    public function renderOnlyOutput()
    {
        $command = [
            "cd tests/test-files/skeleton-releases/v1.0.0;",
            "../../../../markhelp -o {$this->pathDestination}"
        ];
        $result = $this->call(implode("", $command));
        $this->assertStringContainsString('Documentation site successfully generated', $result);

        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertFileExists("{$this->pathDestination}/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");

        $this->assertDirectoryExists("{$this->pathDestination}/images");
        $this->assertFileExists("{$this->pathDestination}/images/example.png");
        $this->assertFileExists("{$this->pathDestination}/images/logo.png");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page.html");
    }

    /** @test */
    public function renderInputAndOutputConfiguration()
    {
        $pathProject = "{$this->pathReleases}/v1.0.0";
        $configFile = $this->normalizePath("{$this->pathConfigurations}/config.json");

        $result = $this->call("./markhelp -i {$pathProject} -o {$this->pathDestination} -c {$configFile}");
        $this->assertStringContainsString('Documentation site successfully generated', $result);

        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertFileExists("{$this->pathDestination}/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");

        $this->assertDirectoryExists("{$this->pathDestination}/images");
        $this->assertFileExists("{$this->pathDestination}/images/example.png");
        $this->assertFileExists("{$this->pathDestination}/images/logo.png");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page.html");
    }

    /** @test */
    public function renderInvalidConfiguration()
    {
        $pathProject = "{$this->pathReleases}/v1.0.0";
        $configFile = $this->normalizePath("{$this->pathConfigurations}/invalid.json");

        $result = $this->call("./markhelp -i {$pathProject} -o {$this->pathDestination} -c {$configFile}");
        $this->assertStringContainsString('The specified configuration file not exists', $result);
    }
}