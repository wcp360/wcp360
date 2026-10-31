<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Domain;
interface ProvisioningGateway
{
    /** @param array{schema:string,operation:string,job_id:string,tenant_id:string,site_id:string,hostname:string,expected_revision:int} $request
     * @return 'ACTIVE'|'REJECTED'|'UNKNOWN_OUTCOME'
     */
    public function send(array $request):string;
}
