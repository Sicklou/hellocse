<?php

test('it can be pinged', function () {
    $response = $this->get('/api/ping');
    $response->assertStatus(200);
});
