<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationPlanSyntax;
use Wcp360\Core\Module\Hosting\Application\PreviewApplicationPlan;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ApplicationSyntaxWorkflowTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function service(ApplicationPlanSyntax $syntax): PreviewApplicationPlan
    {
        return new PreviewApplicationPlan(new CandidateAuthorization($this->store->db),
            self::getContainer()->get(GoPlanRenderer::class), $syntax);
    }

    private function state(): array
    {
        $state = [];
        foreach (['sites','jobs','activations','audit','runtime_settings'] as $table) {
            $state[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        }
        return $state;
    }

    public function testCheckIsExplicitlyOptedInAndBoundToExactGeneratedBytesWithoutBusinessWrites(): void
    {
        $syntax = new class implements ApplicationPlanSyntax {
            public int $calls = 0;
            public function check(ApplicationPlanDocument $document): array
            {
                ++$this->calls;
                return array_fill_keys(array_keys($document->files()), 'passed');
            }
        };
        $before = $this->state();
        $service = $this->service($syntax);
        foreach ([false, true] as $requested) {
            $report = $service->preview($this->fixture['users']['admin'], 'syntax.test', 'nginx_frankenphp', 'wordpress', false, $requested);
            self::assertSame($requested, $report['syntax_checks']['requested']);
            foreach (['coverage_complete','activation_authorized','runtime_validated'] as $flag) self::assertFalse($report['syntax_checks'][$flag]);
            foreach ($report['syntax_checks']['checks'] as $check) {
                self::assertSame($requested ? 'passed' : 'not_run', $check['status']);
                self::assertSame(hash('sha256', $report['files'][$check['artifact']]['content']), $check['sha256']);
            }
        }
        self::assertSame(1, $syntax->calls);
        self::assertSame($before, $this->state());
    }

    public function testUnknownParserResultsAreRedactedAndNeverPublishedAsSuccessfulChecks(): void
    {
        $before = $this->state();
        foreach ([[], ['nginx'=>'private-parser-diagnostic','php_fpm'=>'passed']] as $results) {
            $syntax = new class($results) implements ApplicationPlanSyntax {
                public function __construct(private array $results) {}
                public function check(ApplicationPlanDocument $document): array { return $this->results; }
            };
            try {
                $this->service($syntax)->preview($this->fixture['users']['admin'], 'syntax.test', 'nginx_php_fpm', 'php', false, true);
                self::fail('Invalid parser evidence published.');
            } catch (ServiceUnavailableHttpException $error) {
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-parser-diagnostic', (string) $error);
            }
        }
        $syntax = new class implements ApplicationPlanSyntax {
            public function check(ApplicationPlanDocument $document): array { throw new \RuntimeException('private-parser-cause'); }
        };
        try {
            $this->service($syntax)->preview($this->fixture['users']['admin'], 'syntax.test', 'nginx_php_fpm', 'php', false, true);
            self::fail('Parser failure published.');
        } catch (ServiceUnavailableHttpException $error) {
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('private-parser-cause', (string) $error);
        }
        self::assertSame($before, $this->state());
    }

    public function testAuthorityRevokedDuringNativeCheckingPreventsPublication(): void
    {
        $actor = $this->fixture['users']['admin'];
        $syntax = new class($this->store, $actor) implements ApplicationPlanSyntax {
            public function __construct(private WorkbenchStore $store, private string $actor) {}
            public function check(ApplicationPlanDocument $document): array
            {
                $this->store->db->update('wcp_candidate_users', ['active'=>0], ['id'=>$this->actor]);
                return array_fill_keys(array_keys($document->files()), 'passed');
            }
        };
        $this->expectException(AccessDeniedHttpException::class);
        $this->service($syntax)->preview($actor, 'syntax.test', 'nginx_php_fpm', 'php', false, true);
    }
}
