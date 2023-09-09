<?php

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\ContainerSetFunctionChecker;

use App\Manifest\Generator\Exception\SyntaxTreeException;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use SprykerSdk\Evaluator\Checker\AbstractChecker;
use SprykerSdk\Evaluator\Dto\CheckerInputDataDto;
use SprykerSdk\Evaluator\Dto\CheckerResponseDto;
use SprykerSdk\Evaluator\Dto\ViolationDto;
use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Parser\NodeFinderInterface;
use SprykerSdk\Evaluator\Parser\PhpParserInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ContainerSetFunctionChecker extends AbstractChecker
{
    /**
     * @var string
     */
    public const NAME = 'CONTAINER_SET_FUNCTION_CHECKER';

    /**
     * @var string
     */
    protected const DEPENDENCY_PROVIDER_PATTERN = '*DependencyProvider.php';

    /**
     * @var array<string>
     */
    protected const EXCLUDE_PATH_LIST = ['vendor'];

    /**
     * @var string
     */
    protected const VIOLATION_MESSAGE = 'The callback function inside `container->set()` should not return an array directly but instead call another method. Please review your code and make the necessary changes.';

    /**
     * @var \SprykerSdk\Evaluator\Finder\SourceFinderInterface
     */
    protected SourceFinderInterface $sourceFinder;

    /**
     * @var \SprykerSdk\Evaluator\Parser\PhpParserInterface
     */
    protected PhpParserInterface $phpParser;

    /**
     * @var \SprykerSdk\Evaluator\Parser\NodeFinderInterface
     */
    protected NodeFinderInterface $nodeFinder;

    /**
     * @var string
     */
    protected string $checkerDocUrl;

    /**
     * @param SourceFinderInterface $sourceFinder
     * @param PhpParserInterface $phpParser
     * @param NodeFinderInterface $nodeFinder
     * @param string $checkerDocUrl
     */
    public function __construct(
        SourceFinderInterface $sourceFinder,
        PhpParserInterface $phpParser,
        NodeFinderInterface $nodeFinder,
        string $checkerDocUrl
    )
    {
        $this->sourceFinder = $sourceFinder;
        $this->phpParser = $phpParser;
        $this->nodeFinder = $nodeFinder;
        $this->checkerDocUrl = $checkerDocUrl;
    }


    /**
     * @param \SprykerSdk\Evaluator\Dto\CheckerInputDataDto $inputData
     *
     * @return \SprykerSdk\Evaluator\Dto\CheckerResponseDto
     */
    public function check(CheckerInputDataDto $inputData): CheckerResponseDto
    {
        $violations = [];
        $dependencyProviderList = $this->findDependencyProviders($inputData->getPath());
        foreach ($dependencyProviderList as $dependencyProvider) {
            $violations = [...$violations, ...$this->getViolationFromFile($dependencyProvider)];
        }

        return new CheckerResponseDto($violations, $this->checkerDocUrl);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return array<ViolationDto>
     */
    protected function getViolationFromFile(SplFileInfo $fileInfo): array
    {
        $violations = [];
        $fileStm = $this->phpParser->parse($fileInfo->getPathname());
        $containerSetStmList = $this->findContainerSetStm($fileStm);
        $returnStmList = $this->findReturnStm($containerSetStmList);

        foreach ($returnStmList as $returnStm) {
            if (!$this->isContainArray($returnStm)){
                continue;
            }

            $violations[] = new ViolationDto(
                self::VIOLATION_MESSAGE,
                sprintf('%s:%s', $fileInfo->getPathname(), $returnStm->getLine()),
            );
        }

        return $violations;
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     * @return array<\PhpParser\Node>
     */
    protected function findContainerSetStm(array $syntaxTree): array
    {
        return $this->nodeFinder->find($syntaxTree, function ($statement) {
            return $statement instanceof Expression
                && $statement->expr instanceof MethodCall
                && $statement->expr->var instanceof Variable
                && $statement->expr->var->name === 'container'
                && $statement->expr->name->name === 'set';
        });
    }

    /**
     * @param array<\PhpParser\Node> $syntaxTree
     * @return array<Stmt\Return_>
     */
    protected function findReturnStm(array $syntaxTree): array
    {
        return $this->nodeFinder->findInstanceOf($syntaxTree, Stmt\Return_::class);
    }

    /**
     * @param Stmt\Return_ $returnStmt
     * @return bool
     */
    protected function isContainArray(Stmt\Return_ $returnStmt): bool
    {
        return $returnStmt->expr instanceof Node\Expr\Array_;
    }

    /**
     * @param string $path
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function findDependencyProviders(string $path): Finder
    {
        return $this->sourceFinder->find([static::DEPENDENCY_PROVIDER_PATTERN], [$path], static::EXCLUDE_PATH_LIST);
    }
}
