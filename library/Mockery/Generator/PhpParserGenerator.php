<?php

declare(strict_types=1);

namespace Mockery\Generator;

use InvalidArgumentException;
use Mockery\Parser\NodeVisitor\AbstractNodeVisitor;
use Mockery\Parser\NodeVisitor\AvoidMethodClashVisitor;
use Mockery\Parser\NodeVisitor\CallTypeHintVisitor;
use Mockery\Parser\NodeVisitor\ClassAttributesVisitor;
use Mockery\Parser\NodeVisitor\ClassNameVisitor;
use Mockery\Parser\NodeVisitor\ClassVisitor;
use Mockery\Parser\NodeVisitor\ConstantsVisitor;
use Mockery\Parser\NodeVisitor\InstanceMockVisitor;
use Mockery\Parser\NodeVisitor\InterfaceVisitor;
use Mockery\Parser\NodeVisitor\MagicMethodTypeHintsVisitor;
use Mockery\Parser\NodeVisitor\MethodDefinitionVisitor;
use Mockery\Parser\NodeVisitor\RemoveBuiltinMethodsThatAreFinalVisitor;
use Mockery\Parser\NodeVisitor\RemoveDestructorVisitor;
use Mockery\Parser\NodeVisitor\RemoveUnserializeForInternalSerializableClassesVisitor;
use Mockery\Parser\NodeVisitor\TraitVisitor;
use Mockery\Parser\Parser;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;
use SplFileInfo;

final class PhpParserGenerator implements Generator
{
    /**
     * @param Parser $parser
     * @param array<array-key,class-string<AbstractNodeVisitor>> $visitors
     */
    public function __construct(
        private readonly Parser $parser,
        private readonly array $visitors = []
    ) {
        foreach ($this->visitors as $visitor) {
            if (!is_a($visitor, AbstractNodeVisitor::class, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid Visitor; "%s" must extend "%s"!',
                    $visitor,
                    AbstractNodeVisitor::class
                ));
            }
        }
    }

    /**
     * Creates a new PhpParserGenerator with the default visitors
     */
    public static function withDefaultVisitors(): self
    {
        return new self(
            new Parser(),
            [
                CallTypeHintVisitor::class,
                MagicMethodTypeHintsVisitor::class,
                ClassVisitor::class,
                TraitVisitor::class,
                ClassNameVisitor::class,
                InstanceMockVisitor::class,
                InterfaceVisitor::class,
                AvoidMethodClashVisitor::class,
                MethodDefinitionVisitor::class,
                RemoveUnserializeForInternalSerializableClassesVisitor::class,
                RemoveBuiltinMethodsThatAreFinalVisitor::class,
                RemoveDestructorVisitor::class,
                ConstantsVisitor::class,
                ClassAttributesVisitor::class,
        ]
        );
    }

    public function generate(MockConfiguration $config): MockDefinition
    {
        $filePath = new SplFileInfo(sprintf('%s/Mock.php', dirname(__DIR__)));

        $fileRealPath = $filePath->getRealPath();
        if ($fileRealPath === false) {
            throw new RuntimeException(sprintf('Unable to find "%s"!', $filePath->getFilename()));
        }

        $fileContents = file_get_contents($fileRealPath);
        if ($fileContents === false) {
            throw new RuntimeException(sprintf('Unable to read "%s"!', $filePath->getFilename()));
        }

        /** @var Stmt[]|Node[]|null $astNodes */
        $astNodes = $this->parser->parse($fileContents);
        if ($astNodes === null) {
            throw new RuntimeException(sprintf('Unable to parse "%s"!', $filePath->getFilename()));
        }

        $nodeTraverser = new NodeTraverser();
        foreach ($this->visitors as $visitor) {
            $nodeTraverser->addVisitor(new $visitor($config));
        }

        $traversedNodes = $nodeTraverser->traverse($astNodes);

        $code = (new Standard())->prettyPrintFile($traversedNodes);

        $config = $config->rename($config->getName() ?: $config->generateName());

        return new MockDefinition($config, $code);
    }
}
