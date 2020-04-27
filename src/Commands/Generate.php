<?php

namespace JPNut\CodeGen\Commands;

use Illuminate\Console\Command;
use JPNut\CodeGen\Generator;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code-gen:generate {file : The output file}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Generate a schema and save it to a file';

    /**
     * @var \JPNut\CodeGen\Generator
     */
    protected Generator $generator;

    public function __construct(Generator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    public function handle(): void
    {
        $this->generator->generate()->writeToFile($this->argument('file'));
    }
}
