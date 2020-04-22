<?php

namespace JPNut\Tests;

use JPNut\CodeGen\Generator;
use Illuminate\Support\Facades\File;

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
            'export interface JPNutTestsDTOsFooIndexDTO {',
            '  array_data: JPNutTestsDTOsFooDTO[];',
            '  iterable_data: Array<JPNutTestsDTOsFooDTO>;',
            '}',
            '',
            'export interface JPNutTestsDTOsFooDTO {',
            '  string: string;',
            '  integer: number;',
            '  boolean: boolean;',
            '  float: number;',
            '  iterable: any[];',
            '  array: any[];',
            '  object: any;',
            '  child: JPNutTestsDTOsFooDTO;',
            '  nullable_child?: JPNutTestsDTOsFooDTO | null;',
            '  mixed?: any | null;',
            '  nullable_mixed?: any | null;',
            '  mixed_array: any[];',
            '  soft_string: string;',
            '  soft_integer: number;',
            '  soft_boolean: boolean;',
            '  soft_float: number;',
            '  soft_iterable: any[];',
            '  soft_array: any[];',
            '  soft_object: any;',
            '  soft_child: JPNutTestsDTOsFooDTO;',
            '  soft_nullable_child?: JPNutTestsDTOsFooDTO | null;',
            '  number: number;',
            '  serializable_object: string | number;',
            '  unknown_property?: any;',
            '}',
            '',
            'export interface JPNutTestsDTOsUpdateFooDTO {',
            '  string: string;',
            '  integer: number;',
            '  boolean: boolean;',
            '  float: number;',
            '  iterable: any[];',
            '  array: any[];',
            '  object: any;',
            '  child: JPNutTestsDTOsFooDTO;',
            '  nullable_child?: JPNutTestsDTOsFooDTO | null;',
            '  mixed?: any | null;',
            '  nullable_mixed?: any | null;',
            '  mixed_array: any[];',
            '  soft_string: string;',
            '  soft_integer: number;',
            '  soft_boolean: boolean;',
            '  soft_float: number;',
            '  soft_iterable: any[];',
            '  soft_array: any[];',
            '  soft_object: any;',
            '  soft_child: JPNutTestsDTOsFooDTO;',
            '  soft_nullable_child?: JPNutTestsDTOsFooDTO | null;',
            '  number: number;',
            '  serializable_object: string | number;',
            '  unknown_property?: any;',
            '}',
            '',
            'export interface JPNutTestsRequestsUpdateFooRequest {',
            '  body: JPNutTestsDTOsUpdateFooDTO;',
            '}',
            '',
            'export interface JPNutTestsDTOsBarRequestParamsDTO {',
            '  count: number;',
            '  filter: string;',
            '  sort: string[];',
            '}',
            '',
            'export interface JPNutTestsRequestsQueryParamsRequest {',
            '  queryParams: JPNutTestsDTOsBarRequestParamsDTO;',
            '}',
            '',
            "export const fooIndexRequest = (): Promise<JPNutTestsDTOsFooIndexDTO> => request({ uri: `http://localhost/foo`, method: 'GET' });",
            '',
            "export const fooFetchRequest = (foo: string): Promise<JPNutTestsDTOsFooDTO> => request({ uri: `http://localhost/foo/\${foo}`, method: 'GET' });",
            '',
            "export const fooUpdateRequest = ({ body }: JPNutTestsRequestsUpdateFooRequest, foo: string): Promise<JPNutTestsDTOsFooDTO> => request({ uri: `http://localhost/foo/\${foo}`, method: 'PUT', body: JSON.stringify(body) });",
            '',
            "export const fooDeleteRequest = (foo: string): Promise<any> => request({ uri: `http://localhost/foo/\${foo}`, method: 'DELETE' });",
            '',
            "export const barScalarRequest = (): Promise<string> => request({ uri: `http://localhost/bar/scalar-return-type`, method: 'GET' });",
            '',
            "export const barOptionalScalarRequest = (): Promise<string | null> => request({ uri: `http://localhost/bar/optional-scalar-return-type`, method: 'GET' });",
            '',
            "export const barOptionalParameterRequest = (id?: string): Promise<number | null> => request({ uri: `http://localhost/bar/\${!isNil(id)?id:\"\"}`, method: 'GET' });",
            '',
            "export const barQueryParamsRequest = ({ queryParams }: JPNutTestsRequestsQueryParamsRequest): Promise<any> => request({ uri: `http://localhost/bar/query-params`, method: 'GET', queryParams });",
            '',
            "export const barHttpOnlyRequest = (): Promise<string | null> => request({ uri: `http://localhost/bar/http-only`, method: 'GET' });",
            '',
            "export const barHttpsOnlyRequest = (): Promise<string | null> => request({ uri: `https://localhost/bar/https-only`, method: 'GET' });",
            '',
            "export const barDomainRequest = (): Promise<string | null> => request({ uri: `http://localhost.dev/bar/domain`, method: 'GET' });",
        ]);
    }

    private function loadTestRoutes()
    {
        require __DIR__.'/routes/routes.php';
    }
}
