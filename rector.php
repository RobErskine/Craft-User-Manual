<?php

use craft\rector\SetList;
use Rector\Config\RectorConfig;

// Craft-aware Rector config. Run a preview with `composer rector`
// (alias for `rector process --dry-run`) and apply with `vendor/bin/rector process`.
//
// SetList::CRAFT_CMS_50 carries the automated upgrade rules for Craft 5. Add
// SetList::CRAFT_CMS_60 here once the Craft 6 rule set ships.
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        SetList::CRAFT_CMS_50,
    ]);
