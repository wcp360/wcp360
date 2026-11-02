<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
interface Transport{public function post(string $url,#[\SensitiveParameter]array $request):array;}
