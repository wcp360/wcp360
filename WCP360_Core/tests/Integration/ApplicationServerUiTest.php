<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

final class ApplicationServerUiTest extends WebTestCase
{
    protected function tearDown():void
    {
        parent::tearDown();
        $_ENV['WCP_CONTEXT']=$_SERVER['WCP_CONTEXT']='user';
        $_ENV['WCP_PUBLIC_HOST']=$_SERVER['WCP_PUBLIC_HOST']='user.localhost';
    }
    public function testActualAdminFormUpdatesDefaultWithCsrfAndRejectsStaleRevision():void
    {
        $_ENV['WCP_CONTEXT']=$_SERVER['WCP_CONTEXT']='admin';
        $_ENV['WCP_PUBLIC_HOST']=$_SERVER['WCP_PUBLIC_HOST']='admin.localhost';
        $browser=self::createClient([],['HTTP_HOST'=>'admin.localhost','HTTP_ORIGIN'=>'http://admin.localhost']);$browser->disableReboot();
        $store=self::getContainer()->get(WorkbenchStore::class);WorkbenchFixture::reset($store->db);
        $crawler=$browser->request('GET','http://admin.localhost/login');
        $browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>'admin@example.test','_password'=>WorkbenchFixture::PASSWORD]));
        for($i=0;$i<3 && $browser->getResponse()->isRedirect();$i++)$crawler=$browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSame('/mfa',$browser->getRequest()->getPathInfo());
        $crawler=$browser->submit($crawler->selectButton('Configurer la MFA')->form());
        self::assertResponseIsSuccessful();
        $secret=trim($crawler->filter('#mfa-secret')->text());
        $browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>TOTP::createFromSecret($secret)->now()]));
        self::assertResponseIsSuccessful();self::assertSelectorCount(10,'#recovery-codes code');
        $crawler=$browser->request('GET','http://admin.localhost/');self::assertResponseIsSuccessful();
        self::assertSelectorExists('#default-application-server option[value="php_fpm"][selected]');
        self::assertSelectorExists('#default-application-server option[value="frankenphp_worker"][disabled]');
        $form=$crawler->selectButton('Enregistrer le défaut')->form();
        $token=$form->getValues()['_token'];
        $browser->submit($form);self::assertResponseStatusCodeSame(303);
        self::assertSame(2,(int)$store->db->fetchOne('SELECT revision FROM wcp_candidate_runtime_settings'));
        $browser->request('POST','http://admin.localhost/workbench/settings/application-server',['_token'=>$token,'application_server'=>'php_fpm','revision'=>'1']);
        self::assertResponseStatusCodeSame(409);
        $browser->request('POST','http://admin.localhost/workbench/settings/application-server',['_token'=>'forged','application_server'=>'php_fpm','revision'=>'2']);
        self::assertResponseStatusCodeSame(403);
    }
}
