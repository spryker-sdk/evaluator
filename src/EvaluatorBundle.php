<?php

namespace SprykerSdk\Evaluator;

use SprykerSdk\Evaluator\DependencyInjection\EvaluatorExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvaluatorBundle extends Bundle
{
    public function createContainerExtension(): Extension
    {
        return new EvaluatorExtension();
    }
}
