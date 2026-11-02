<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Domain\UserListQuery;

final class UserListQueryTest extends TestCase
{
    private const ID = 'abcdefab-cdef-7000-8000-abcdefabcdef';

    public function testUsernameFragmentsAndStateReuseBoundedCursorTransport(): void
    {
        self::assertSame(25, UserListQuery::fromArray([])->limit);
        $query = UserListQuery::fromArray(['q' => ' ACCT_01 ', 'state' => 'active', 'limit' => '50', 'after' => strtoupper(self::ID)]);
        self::assertSame('acct_01', $query->search);
        self::assertSame(self::ID, $query->after);
        self::assertSame(['q' => 'acct_01', 'state' => 'active', 'limit' => '50'], $query->filters());
    }

    public static function invalid(): iterable
    {
        yield [['email' => 'private-email-filter']];
        yield [['sort' => 'password_hash']];
        yield [['q' => '%']];
        yield [['q' => "x' OR 1=1 --"]];
        yield [['q' => str_repeat('a', 33)]];
        yield [['q' => 'é']];
        yield [['q' => ['acct']]];
        yield [['q' => "private\0name"]];
        yield [['state' => 'suspended']];
        yield [['limit' => 25]];
        yield [['limit' => '1000']];
        yield [['after' => self::ID, 'before' => self::ID]];
        yield [['after' => 'private-id']];
    }

    #[DataProvider('invalid')]
    public function testUnknownSelectorsAndInvalidInputsFailWithoutReflection(array $input): void
    {
        try { UserListQuery::fromArray($input); self::fail('Invalid user selector admitted.'); }
        catch (BadRequestHttpException $error) { self::assertSame('Invalid list filters or cursor.', $error->getMessage()); }
    }
}
