<?php

require __DIR__ . '/vendor/autoload.php';

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

// You can start from a default standard
$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());
$ruleset->overrideRule(new \TwigCsFixer\Rules\Variable\VariableNameRule(
    case: \TwigCsFixer\Rules\Variable\VariableNameRule::CAMEL_CASE,
));
$ruleset->addRule(new \TwigCsFixer\ForIfRule());

$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);

$config->allowNonFixableRules();

return $config;
