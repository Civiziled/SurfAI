<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\RouteListCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'route:list', description: 'List all registered routes (compatibilité --columns)')]
class RouteListCompatCommand extends RouteListCommand
{
    /**
     * Override the base command to accept the legacy --columns option
     * that existed before Laravel 12.
     *
     * @var string
     */
    protected $name = 'route:list';

    /**
     * Add the legacy option and keep all existing ones.
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['columns', null, InputOption::VALUE_OPTIONAL, 'Deprecated: previously allowed selecting columns; ignored here.'],
        ]);
    }

    /**
     * Use the legacy --columns option if provided; otherwise fall back to the
     * default column handling from the parent command.
     */
    protected function getColumns()
    {
        $columns = $this->option('columns');

        if ($columns !== null) {
            $parsed = $this->parseColumns((array) $columns);

            return $parsed ?: parent::getColumns();
        }

        return parent::getColumns();
    }
}
