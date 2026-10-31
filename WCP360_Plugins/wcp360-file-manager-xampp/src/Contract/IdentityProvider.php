<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Contract;
interface IdentityProvider { public function current(): Identity; }
