<?php

declare(strict_types=1);

namespace Mockery\Parser;

use FilesystemIterator;
use Generator;
use PhpParser\Node\Stmt;
use PhpParser\Parser as NikicParser;
use PhpParser\ParserFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use SplFileInfo;

final class Parser implements ParserInterface
{
    private readonly NikicParser $parser;
    public function __construct(
        private readonly ParserFactory $parserFactory = new ParserFactory()
    ) {
        $this->parser = ($parserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @param string $path
     * @return @return null|Stmt[]
     */
    public function parseFile(string $path): array|null
    {
        return $this->parseSource(new SplFileInfo($path));
    }

    /**
     * @param SplFileInfo $source
     * @return null|Stmt[]
     */
    public function parseSource(SplFileInfo $source): array|null
    {
        return $this->parse(file_get_contents($source->getRealPath()));
    }

    /**
     * @param string $code
     * @return null|Stmt[]
     */
    public function parse(string $code): array|null
    {
        return $this->parser->parse($code);
    }

    /**
     * @param string $path
     * @param string $fileExtension
     * @return Generator<SplFileInfo,Stmt[]|null>
     */
    public function parseDirectory(string $path, string $fileExtension = 'php'): Generator
    {
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator(
            $path,
            FilesystemIterator::FOLLOW_SYMLINKS |
            FilesystemIterator::KEY_AS_PATHNAME |
            FilesystemIterator::SKIP_DOTS
        );

        $recursiveIteratorIterator = new RecursiveIteratorIterator(
            $recursiveDirectoryIterator,
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $regexIterator = new RegexIterator(
            $recursiveIteratorIterator,
            '(\.' . preg_quote($fileExtension) . '$)',
            RegexIterator::MATCH
        );

        /** @var SplFileInfo $splFileInfo */
        foreach ($regexIterator as $path => $splFileInfo) {
            yield $splFileInfo => $this->parseSource($splFileInfo);
        }
    }

    public function getParserFactory(): ParserFactory
    {
        return $this->parserFactory;
    }
}
