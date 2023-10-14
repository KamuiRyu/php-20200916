<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsApiTest extends TestCase
{
    use RefreshDatabase;

    public function testHealthCheck()
    {
        $response = $this->get('api/');
        $response->assertStatus(200);
    }

    public function testUpdateProduct()
    { 
        $product = Product::factory()->make();
        $response = $this->putJson("api/products/{$product->code}", [
            'product_name' => 'Novo Nome',
            'quantity' => '99g',
        ]);
        $response->assertStatus(200);
    }

    public function testDeleteProduct()
    {
        $product = Product::factory()->create();
        $response = $this->delete("api/products/{$product->code}");
        $response->assertStatus(204);
    }

    public function testGetProduct()
    {
        $product = Product::factory()->create();
        $response = $this->get("api/products/{$product->code}");
        $response->assertStatus(200);
    }

    public function testGetAllProducts()
    {
        $products = Product::factory(10)->create();
        $productsArray = $products->map(function ($product) {
            return [
                'campo' => 'code',
                'valor' => $product->code,
            ];
        });
        $jsonData = $productsArray->toJson();
        $response = $this->getJson('api/products', [$jsonData]);
        $response->assertStatus(200);
    }
}
