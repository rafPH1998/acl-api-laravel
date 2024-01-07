<?php

use function Pest\Laravel\getJson;

it('should return status code 200', function() {
  getJson('/', [
    'Content-Type' => 'Application-json'
  ])->assertOk();
  
});
