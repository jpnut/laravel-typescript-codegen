<?php

namespace JPNut\CodeGen\Tests;

use JPNut\CodeGen\Generator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_generate_for_routes()
    {
        $generator = $this->app->make(Generator::class);

        $expected = $this->expectedResult();

        $this->assertEquals($expected, $generator->generate()->result());
    }

    /**
     * @test
     */
    public function it_can_write_to_file()
    {
        $generator = $this->app->make(Generator::class);

        $expected = str_replace(
            '{{ Schema }}',
            $this->expectedResult(),
            $stub = file_get_contents($stubFile = config('typescript-codegen.stub'))
        );

        File::shouldReceive('get')
            ->with($stubFile)
            ->andReturn($stub);

        File::shouldReceive('put')
            ->with('result.ts', $expected)
            ->andReturn(true);

        $this->assertEquals(true, $generator->generate()->writeToFile('result.ts'));
    }

    /**
     * @test
     */
    public function it_can_write_to_file_from_command()
    {
        $expected = str_replace(
            '{{ Schema }}',
            $this->expectedResult(),
            $stub = file_get_contents($stubFile = config('typescript-codegen.stub'))
        );

        File::shouldReceive('get')
            ->with($stubFile)
            ->andReturn($stub);

        File::shouldReceive('put')
            ->with('result.ts', $expected)
            ->andReturn(true);

        Artisan::call('code-gen:generate', ['file' => 'result.ts']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadTestRoutes();
    }

    /**
     * @return string
     */
    protected function expectedResult(): string
    {
        return implode("\n", [
            'export interface JPNutCodeGenTestsDTOsFooIndexDTO {',
            '  array_data: JPNutCodeGenTestsDTOsFooDTO[];',
            '  iterable_data: Array<JPNutCodeGenTestsDTOsFooDTO>;',
            '}',
            '',
            'export interface JPNutCodeGenTestsDTOsFooDTO {',
            '  string: string;',
            '  integer: number;',
            '  boolean: boolean;',
            '  float: number;',
            '  iterable: any[];',
            '  array: any[];',
            '  object: any;',
            '  child: JPNutCodeGenTestsDTOsFooDTO;',
            '  nullable_child?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  mixed?: any | null;',
            '  nullable_mixed?: any | null;',
            '  mixed_array: any[];',
            '  soft_string: string;',
            '  soft_integer: number;',
            '  soft_integer_array: number[];',
            '  soft_boolean: boolean;',
            '  soft_boolean_array: boolean[];',
            '  soft_float: number;',
            '  soft_float_array: number[];',
            '  soft_iterable: any[];',
            '  soft_array: any[];',
            '  soft_object: any;',
            '  soft_child: JPNutCodeGenTestsDTOsFooDTO;',
            '  soft_nullable_child?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_child_array?: JPNutCodeGenTestsDTOsFooDTO[] | null;',
            '  number: number;',
            '  serializable_object: string | number;',
            '  iterator_object: string[];',
            '  nullable_self?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_self?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_static?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_self_array?: JPNutCodeGenTestsDTOsFooDTO[] | null;',
            '  soft_nullable_static_array?: JPNutCodeGenTestsDTOsFooDTO[] | null;',
            '  unknown_property?: any;',
            '}',
            '',
            'export interface JPNutCodeGenTestsDTOsUpdateFooDTO {',
            '  string: string;',
            '  integer: number;',
            '  boolean: boolean;',
            '  float: number;',
            '  iterable: any[];',
            '  array: any[];',
            '  object: any;',
            '  child: JPNutCodeGenTestsDTOsFooDTO;',
            '  nullable_child?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  mixed?: any | null;',
            '  nullable_mixed?: any | null;',
            '  mixed_array: any[];',
            '  soft_string: string;',
            '  soft_integer: number;',
            '  soft_integer_array: number[];',
            '  soft_boolean: boolean;',
            '  soft_boolean_array: boolean[];',
            '  soft_float: number;',
            '  soft_float_array: number[];',
            '  soft_iterable: any[];',
            '  soft_array: any[];',
            '  soft_object: any;',
            '  soft_child: JPNutCodeGenTestsDTOsFooDTO;',
            '  soft_nullable_child?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_child_array?: JPNutCodeGenTestsDTOsFooDTO[] | null;',
            '  number: number;',
            '  serializable_object: string | number;',
            '  iterator_object: string[];',
            '  nullable_self?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_self?: JPNutCodeGenTestsDTOsFooDTO | null;',
            '  soft_nullable_static?: JPNutCodeGenTestsDTOsUpdateFooDTO | null;',
            '  soft_nullable_self_array?: JPNutCodeGenTestsDTOsFooDTO[] | null;',
            '  soft_nullable_static_array?: JPNutCodeGenTestsDTOsUpdateFooDTO[] | null;',
            '  unknown_property?: any;',
            '}',
            '',
            'export interface JPNutCodeGenTestsRequestsUpdateFooRequest {',
            '  body: JPNutCodeGenTestsDTOsUpdateFooDTO;',
            '}',
            '',
            "export const fooIndexRequest = (options?: RequestInit) => request<JPNutCodeGenTestsDTOsFooIndexDTO>(`http://localhost/foo`, { method: 'GET', ...options });",
            '',
            "export const fooFetchRequest = (foo: string, options?: RequestInit) => request<JPNutCodeGenTestsDTOsFooDTO>(`http://localhost/foo/\${foo}`, { method: 'GET', ...options });",
            '',
            "export const fooUpdateRequest = ({ body }: JPNutCodeGenTestsRequestsUpdateFooRequest, foo: string, options?: RequestInit) => request<JPNutCodeGenTestsDTOsFooDTO>(`http://localhost/foo/\${foo}`, { method: 'PUT', body: JSON.stringify(body), ...options });",
            '',
            "export const fooDeleteRequest = (foo: string, options?: RequestInit) => request<any>(`http://localhost/foo/\${foo}`, { method: 'DELETE', ...options });",
            '',
            "export const barScalarRequest = (options?: RequestInit) => request<string>(`http://localhost/bar/scalar-return-type`, { method: 'GET', ...options });",
            '',
            "export const barOptionalScalarRequest = (options?: RequestInit) => request<string | null>(`http://localhost/bar/optional-scalar-return-type`, { method: 'GET', ...options });",
            '',
            "export const barOptionalParameterRequest = (id?: string, options?: RequestInit) => request<number | null>(`http://localhost/bar/\${!isNil(id)?id:\"\"}`, { method: 'GET', ...options });",
            '',
            "export const barHttpOnlyRequest = (options?: RequestInit) => request<string | null>(`http://localhost/bar/http-only`, { method: 'GET', ...options });",
            '',
            "export const barHttpsOnlyRequest = (options?: RequestInit) => request<string | null>(`https://localhost/bar/https-only`, { method: 'GET', ...options });",
            '',
            "export const barDomainRequest = (options?: RequestInit) => request<string | null>(`http://localhost.dev/bar/domain`, { method: 'GET', ...options });",
        ]);
    }

    private function loadTestRoutes()
    {
        require __DIR__.'/routes/routes.php';
    }
}
