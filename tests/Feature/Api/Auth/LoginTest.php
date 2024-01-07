<?php

use App\Models\User;

use function Pest\Laravel\postJson;

test('test should auth user', function () {
  $user = User::factory()->createOne();

  $response = postJson(route('auth.login'), [
    'email' => $user->email,
    'password' => 'password',
    'device_name' => 'teste',
  ]);

  $response->assertStatus(200)->assertJsonStructure(['token']);
});

test('test should return error on invalid credentials', function () {
  $user = User::factory()->createOne();

  $response = postJson(route('auth.login'), [
      'email' => $user->email,
      'password' => 'invalid_password',
      'device_name' => 'teste',
  ]);

  $response->assertStatus(422)
          ->assertJsonStructure(['error'])
          ->assertJson(['error' => 'Credenciais inválidas']);
});


