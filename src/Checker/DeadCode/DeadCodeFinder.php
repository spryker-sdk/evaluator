<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Evaluator\Checker\DeadCode;

use SprykerSdk\Evaluator\Finder\SourceFinderInterface;
use SprykerSdk\Evaluator\Resolver\PathResolverInterface;
use Symfony\Component\Finder\Finder;

class DeadCodeFinder
{
    /**
     * @var string
     */
    protected const SPRYKER_NAMESPACE = 'Spryker';

    /**
     * @var string
     */
    protected const ANNOTATION_SKIP = '@evaluator-skip-dead-code';

    /**
     * @var \SprykerSdk\Evaluator\Finder\SourceFinderInterface
     */
    protected SourceFinderInterface $sourceFinder;

    /**
     * @var \SprykerSdk\Evaluator\Resolver\PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @param \SprykerSdk\Evaluator\Finder\SourceFinderInterface $sourceFinder
     * @param \SprykerSdk\Evaluator\Resolver\PathResolverInterface $pathResolver
     */
    public function __construct(SourceFinderInterface $sourceFinder, PathResolverInterface $pathResolver)
    {
        $this->sourceFinder = $sourceFinder;
        $this->pathResolver = $pathResolver;
    }

    /**
     * @param string $path
     *
     * @return array<string, string>
     */
    public function find(string $path): array
    {
        $extendedCoreClassesInUse = $this->getAllExtendedCoreClasses($path);
        $allClassesInUse = $this->getAllClassesInUse($this->pathResolver->getProjectDir());

        $deadClasses = [];
        foreach ($extendedCoreClassesInUse as $className => $classPath) {
            if (!isset($allClassesInUse[$className]) && !$this->isInCurrentNamespace($className, $classPath)) {
                $deadClasses[$className] = $classPath;
            }
        }

        return $deadClasses;
    }

    /**
     * @param string $className
     * @param string $classPath
     *
     * @return bool
     */
    protected function isInCurrentNamespace(string $className, string $classPath): bool
    {
        foreach ($this->getFinderIterator(dirname($classPath)) as $file) {
            if ($classPath === $file->getRealPath()) {
                continue;
            }
            $shortClassName = substr($className, (strrpos($className, '\\') ?: -1) + 1);

            preg_match(sprintf('/(?<class>extends %s|new %s\(|%s:)/', $shortClassName, $shortClassName, $shortClassName), $file->getContents(), $matchesClass);

            if (!empty($matchesClass['class'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return array<string|int, true>
     */
    protected function getAllClassesInUse(string $path): array
    {
        $allClassesInUse = [];
        foreach ($this->getFinderIterator($path, ['*.php']) as $file) {
            $fileContent = $file->getContents();

            preg_match_all('/use (?<useClasses>\S*)(;| )/m', $fileContent, $matches);
            foreach ($matches['useClasses'] as $useClass) {
                $allClassesInUse[$useClass] = true;
            }
        }

        return $allClassesInUse;
    }

    /**
     * @param string $path
     *
     * @return array<string, string>
     */
    protected function getAllExtendedCoreClasses(string $path): array
    {
        $extendedCoreClassesInUse = [];
        $patterns = [
            '*.php',
            '!*Factory.php',
            '!*Facade.php',
            '!*DependencyProvider.php',
            '!*Trait.php',
            '!*Client.php',
            '!*Config.php',
            '!*Controller.php',
            '!*Bootstrap.php',
            '!*Interface.php',
            '!*QueryContainer.php',
            '!*Repository.php',
            '!*EntityManager.php',
            '!*DependencyInjector.php',
            '!*ConstraintValidator.php',
        ];
        foreach ($this->getFinderIterator($path, $patterns) as $file) {
            $fileContent = $file->getContents();

            if (strpos($fileContent, static::ANNOTATION_SKIP) !== false) {
                continue;
            }

            preg_match('/ extends (?<extendedClass>\S+)\\n/', $fileContent, $matches);

            if (!isset($matches['extendedClass']) || !$this->isSprykerNamespace($fileContent, $matches['extendedClass'])) {
                continue;
            }

            preg_match('/^namespace (?<namespace>\S*);/m', $fileContent, $matchesNamespace);
            preg_match('/^(abstract|final|) ?class (?<class>\S*) /m', $fileContent, $matchesClass);

            if (!isset($matchesNamespace['namespace'], $matchesClass['class'])) {
                continue;
            }

            $extendedCoreClassesInUse[sprintf('%s\%s', $matchesNamespace['namespace'], $matchesClass['class'])] = $file->getRealPath();
        }

        return $extendedCoreClassesInUse;
    }

    /**
     * @param string $fileContent
     * @param string $extendedClass
     *
     * @return bool
     */
    protected function isSprykerNamespace(string $fileContent, string $extendedClass): bool
    {
        if (strpos($extendedClass, '\\') !== false) {
            return strpos($extendedClass, '\\' . static::SPRYKER_NAMESPACE) || strpos($extendedClass, static::SPRYKER_NAMESPACE) !== false;
        }

        preg_match(sprintf('/use (?<useClass>%s\S*)(%s| as %s)/', static::SPRYKER_NAMESPACE, $extendedClass, $extendedClass), $fileContent, $matches);

        return isset($matches['useClass']);
    }

    /**
     * @param string $path
     * @param array<string> $patterns
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getFinderIterator(string $path, array $patterns = []): Finder
    {
        return $this->sourceFinder->find($patterns, [$path], ['Generated', 'Orm', 'Dependency', 'vendor', 'tests']);
    }
}
