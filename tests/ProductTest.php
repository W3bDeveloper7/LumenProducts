<?php

use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{

    /**
     * Login as default API user and get token back.
     *
     * @return void
     */
    public function testLogin()
    {
        $parameters = [
            'email' => 'ahmed@domain.com',
            'password' => '123456',
        ];

        $this->post("/api/login", $parameters, []);
        $this->seeStatusCode(200);
        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token', 'token_type', 'expires_in'
            ]);
    }

    /**
     * /products [GET]
     */
    public function testShouldReturnAllProducts(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        //dd($token);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $this->get("/api/products", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(['data'=>[
            'current_page',
            'data' => [ '*'=>
                [
                'id',
                'name',
                'cost',
                'price',
                'profit',
                'discount',
                'is_active',
                'd_type',
                'created_at',
                'updated_at',
                'final_price'
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',

        ], 'message']);
//        $this->seeJsonStructure(['data'=>[]]);
    }

    /**
     * /products/profitable [GET]
     */
    public function testShouldReturnTopProfitProducts(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        //dd($token);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $this->get("/api/products/profitable", $header);
        $this->seeStatusCode(200);

        $this->seeJsonStructure(
            ['data' =>
                ['*'=>
                    [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                    ]
                ], 'message'
            ]
        );

    }


    /**
     * /products/expensive [GET]
     */
    public function testShouldReturnTopExpensiveProducts(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        //dd($token);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $this->get("/api/products/expensive", $header);
        $this->seeStatusCode(200);

        $this->seeJsonStructure(
            ['data' =>
                [ '*'=>
                    [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                    ]
                ], 'message'
            ]
        );

    }

    /**
     * /products/id [GET]
     */
    public function testShouldReturnProduct(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $this->get("/api/products/2", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                ], 'message'
            ]
        );

    }

    /**
     * /products [POST]
     */
    public function testShouldCreateProduct(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $parameters = [
            'name' => 'P533',
            'cost' => 74,
            'price' => 124,
            'discount' => 8,
            'd_type' => 1,
        ];

        $this->post("/api/admin/products/add", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                ], 'message'
            ]
        );

    }

    /**
     * /products/id [PUT]
     */
    public function testShouldUpdateProduct(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $parameters = [
            'name' => 'Infinix Hot Note',
            'cost' => 174,
            'price' => 224,
            'discount' => 6,
            'd_type' => 1,
        ];

        $this->patch("/api/admin/products/4/update", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                ], 'message'
            ]
        );
    }

    /**
     * /products/id [DELETE]
     */
    public function testShouldDeleteProduct(){
        $user = User::where('email', 'ahmed@domain.com')->first();
        $token = JWTAuth::fromUser($user);
        $header = [ 'HTTP_Authorization' => 'Bearer '.$token];
        $this->delete("api/admin/products/52/delete", [], $header);
        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'data' =>
                [
                    'id',
                    'name',
                    'cost',
                    'price',
                    'profit',
                    'discount',
                    'is_active',
                    'd_type',
                    'created_at',
                    'updated_at',
                    'final_price'
                ], 'message'
        ]);
    }

}
