<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Browser;
use Symfony\Component\Panther\PantherTestCase;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
final class HostingJourneyTest extends PantherTestCase
{
    public function testLoginRuntimeSelectorStimulusAndTurboNavigation():void
    {
        $url='sqlite:///'.str_replace('\\','/',dirname(__DIR__,2).'/var/panther.db');
        $_ENV['DATABASE_URL']=$_SERVER['DATABASE_URL']=$url;putenv('DATABASE_URL='.$url);
        foreach(['APP_ENV'=>'test','APP_DEBUG'=>'1','APP_SECRET'=>'browser-test-only-secret','WCP_CANDIDATE'=>'1','WCP_CONTEXT'=>'user','WCP_PUBLIC_HOST'=>'localhost','WCP_ALLOW_FRANKENPHP_PLANS'=>'0','WCP_ALLOW_ISOLATED_PROVISIONING'=>'0']as $k=>$v){$_ENV[$k]=$_SERVER[$k]=$v;putenv($k.'='.$v);}
        $_SERVER['PANTHER_APP_ENV']='dev'; // Real HTTP server uses native cookies; fixture Kernel stays in test.
        $client=self::createPantherClient(['browser'=>self::CHROME,'hostname'=>'localhost','port'=>9080,'webServerDir'=>dirname(__DIR__,2).'/public'],[],['chromedriver_arguments'=>['--log-level=WARNING','--log-path=/tmp/wcp-browser/chromedriver.log']]);
        $store=self::getContainer()->get(WorkbenchStore::class);WorkbenchFixture::reset($store->db);
        $client->request('GET','/login');
        if($client->getCrawler()->filter('form[action="/login"]')->count()!==1){fwrite(STDERR,'Unexpected browser page: '.$client->getCurrentURL().' '.substr($client->executeScript('return document.body.innerText'),0,3000).PHP_EOL);}
        self::assertSelectorExists('form[action="/login"]');
        $client->submitForm('Se connecter',['_username'=>'alice@example.test','_password'=>WorkbenchFixture::PASSWORD]);
        $client->waitFor('[data-form-guard-ready="true"]');
        self::assertSelectorTextContains('h1','Tableau de bord');
        self::assertSelectorExists('#site-application-server option[value="frankenphp_worker"][disabled]');
        self::assertSelectorCount(1,'select[name="tenant_id"] option');
        self::assertSelectorNotExists('a[href="/account/reauthenticate"]');
        $client->executeScript("document.querySelector('[data-mode=light]').click();");
        $lightBackground=$client->executeScript('return getComputedStyle(document.body).backgroundColor');
        $client->executeScript("document.querySelector('[data-mode=dark]').click();");
        self::assertSelectorExists('html[data-theme="dark"]');
        self::assertNotSame($lightBackground,$client->executeScript('return getComputedStyle(document.body).backgroundColor'));
        $client->executeScript("document.querySelector('[data-navigation-target=opener]').click();");
        $client->waitFor('dialog[open]');
        self::assertSame('wcp-page-search',$client->executeScript('return document.activeElement.id'));
        $client->executeScript("const search=document.querySelector('#wcp-page-search');search.value='sessions';search.dispatchEvent(new Event('input',{bubbles:true}));");
        self::assertSelectorCount(1,'[data-navigation-target="item"]:not([hidden])');
        $client->executeScript("document.querySelector('dialog').close();");

        $client->executeScript("window.wcpBrowserJourney='turbo-preserves-window'; const f=document.querySelector('[data-controller=\"form-guard\"]');f.querySelector('[name=\"hostname\"]').value='browser.test';const b=f.querySelector('button[type=\"submit\"]');b.click();b.click();");
        $client->waitFor('.status');
        self::assertSelectorTextContains('h1','Suivi de tâche');
        self::assertSelectorTextContains('.status','QUEUED');
        self::assertSame(1,(int)$store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
        self::assertSame('turbo-preserves-window',$client->executeScript('return window.wcpBrowserJourney'));
        $client->clickLink('Retour au tableau de bord');
        $client->waitFor('[data-form-guard-ready="true"]');
        self::assertSelectorTextContains('h1','Tableau de bord');
        self::assertSame('turbo-preserves-window',$client->executeScript('return window.wcpBrowserJourney'));
    }
}
