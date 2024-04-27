<?php

test('api can be pinged', function () {
    $response = $this->get('/api/ping');
    $response->assertStatus(200);
});
