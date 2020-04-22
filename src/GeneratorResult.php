<?php

namespace JPNut\CodeGen;

use Illuminate\Support\Facades\File;

final class GeneratorResult
{
    /**
     * @var string
     */
    private string $content;

    /**
     * @param  string  $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function result(): string
    {
        return $this->content;
    }

    /**
     * @param  string  $file
     * @return bool
     */
    public function writeToFile(string $file): bool
    {
        return File::put($file, $this->getFileContents());
    }

    /**
     * @return string
     */
    private function getFileContents(): string
    {
        return str_replace('{{ Schema }}', $this->content, $this->stub());
    }

    /**
     * @return string
     */
    private function stub(): string
    {
        return File::get(config('typescript-codegen.stub'));
    }
}
