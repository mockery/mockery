<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP73;

use Generator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 7.3
 *
 * @see https://github.com/mockery/mockery/issues/1404
 */
final class TestCase1404Test extends MockeryTestCase
{
    /**
     * @return Generator<string,list<string>>
     */
    public static function provideResult(): iterable
    {
        yield from [
            'empty' => [[]],
            'non-empty' => [['Black', 'Lives', 'Matter']],
        ];
    }

    /**
     * @dataProvider provideResult
     *
     * @throws Throwable
     */
    #[DataProvider('provideResult')]
    public function testDemeterChainsAlternativeSyntax(array $result): void
    {
        $dbConnection = Mockery::mock(PDO::class);

        $dbConnection->shouldReceive('query->fetchAll')
            ->andReturn($result);

        self::assertSame($result, $dbConnection->query('sql')->fetchAll());
    }

    /**
     * @dataProvider provideResult
     *
     * @throws Throwable
     */
    #[DataProvider('provideResult')]
    public function testDemeterChainsExpects(array $result): void
    {
        $dbConnection = Mockery::mock(PDO::class);

        $dbConnection->expects('query->fetchAll')
            ->andReturn($result);

        self::assertSame($result, $dbConnection->query('sql')->fetchAll());
    }

    /**
     * @dataProvider provideResult
     *
     * @throws Throwable
     */
    #[DataProvider('provideResult')]
    public function testDetestDemeterChainsAllowsmeterChainsAllows(array $result): void
    {
        $dbConnection = Mockery::mock(PDO::class);

        $dbConnection->allows('query->fetchAll')
            ->andReturn($result);

        self::assertSame($result, $dbConnection->query('sql')->fetchAll());
    }

    /**
     * @dataProvider provideResult
     *
     * @throws Throwable
     */
    #[DataProvider('provideResult')]
    public function testNonDemeterChainsSyntax(array $result): void
    {
        $dbStatement = Mockery::mock(PDOStatement::class);
        $dbStatement->expects('fetchAll')
            ->andReturn($result);

        $dbConnection = Mockery::mock(PDO::class);
        $dbConnection->expects('query')
            ->with('sql')
            ->andReturn($dbStatement);

        self::assertSame($result, $dbConnection->query('sql')->fetchAll());
    }
}
