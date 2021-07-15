<?php

namespace Pantheon\Terminus\Tests\Functional;

use Pantheon\Terminus\Tests\Traits\LoginHelperTrait;
use Pantheon\Terminus\Tests\Traits\SiteBaseSetupTrait;
use Pantheon\Terminus\Tests\Traits\TerminusTestTrait;
use Pantheon\Terminus\Tests\Traits\ValidUuidTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

class SiteCommandsTest extends TestCase
{
    use ValidUuidTrait;
    use TerminusTestTrait;
    use SiteBaseSetupTrait;
    use LoginHelperTrait;

    /**
     * @test
     * @covers \Pantheon\Terminus\Commands\Site\ListCommand
     * @group site
     * @group short
     */
    public function testSiteListCommand()
    {
        $org = getenv("TERMINUS_ORG");
        $siteList = $this->terminusJsonResponse(
            "site:list --org=" . $org
        );
        $this->assertIsArray(
            $siteList,
            "Response from site:info should be an array of values"
        );
        $this->assertGreaterThan(
            0,
            count($siteList),
            "count of sites should be a non-zero number"
        );
        $site = array_shift($siteList);
        $this->assertArrayHasKey(
            'id',
            $site,
            "Response from site should contain an ID property"
        );

        $this->assertArrayHasKey(
            'memberships',
            $site,
            'Site information should have a membership property'
        );
    }

    /**
     * Test Site Org List command
     *
     * @test
     * @covers \Pantheon\Terminus\Commands\Site\Org\ListCommand
     * @throws \JsonException
     * @group site
     * @group short
     */
    public function testSiteOrgListCommand()
    {
        $sitename = getenv('TERMINUS_SITE');
        $list = $this->terminusJsonResponse("site:org:list {$sitename}");
        $this->assertIsArray(
            $list,
            "Response from Site List should be an array"
        );
        $this->assertGreaterThan(
            0,
            count($list),
            "count of sites should be a non-zero number"
        );
    }

    /**
     * Test Site Orgs command
     *
     * @test
     * @covers \Pantheon\Terminus\Commands\Site\Org\ListCommand
     * @throws \JsonException
     * @group site
     * @group short
     */
    public function testSiteOrgsCommand()
    {
        $sitename = getenv('TERMINUS_SITE');
        $list = $this->terminusJsonResponse("site:orgs {$sitename}");
        $this->assertIsArray(
            $list,
            "Response from Site List should be an array"
        );
        $this->assertGreaterThan(
            0,
            count($list),
            "count of sites should be a non-zero number"
        );
    }

    /**
     * Test site:create command.
     *
     * @test
     * @covers \Pantheon\Terminus\Commands\Site\CreateCommand
     * @covers \Pantheon\Terminus\Commands\Site\InfoCommand
     * @group site
     * @group long
     * @throws \JsonException
     */
    public function testSiteCreateInfoDeleteCommand()
    {
        $output = new ConsoleOutput();
        $sitename = \strtolower(\substr(\uniqid('site-create-'), -50));
        $org = getenv('TERMINUS_ORG');
        $output->writeln("Step 1 => Sitename => Creating... {$sitename}");
        $command = vsprintf(
            'site:create %s %s drupal9 --org=%s',
            [ $sitename, $sitename, $org ]
        );
        $output->writeln($command);
        $this->terminus($command, null);
        $output->writeln("Step 2 => get info => {$sitename}");
        $command = vsprintf(
            'site:info %s',
            [$sitename]
        );
        $info = $this->terminusJsonResponse($command, null);
        $this->assertEquals($org, $info['organization']);
        $output->writeln("Step 3 => Delete Site => {$sitename}");
        $command = vsprintf(
            'site:delete %s --yes',
            [$info['id']]
        );
        $output->writeln($command);
        $this->terminus($command, null);
    }
}