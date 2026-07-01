<?php

use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;

uses(TestCase::class);

it('encrypts and decrypts payload correctly', function () {
    $key = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    $data = [
        'omni_id' => 42,
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'avatar' => null,
        'timestamp' => 1234567890,
    ];

    $encrypted = AuthorizationController::encryptPayload($data, $key);
    expect($encrypted)->toBeString();
    expect($encrypted)->not->toBeEmpty();

    $decrypted = AuthorizationController::decryptPayload($encrypted, $key);
    expect($decrypted)->toBeArray();
    expect($decrypted['omni_id'])->toBe(42);
    expect($decrypted['name'])->toBe('Budi Santoso');
    expect($decrypted['email'])->toBe('budi@example.com');
    expect($decrypted['timestamp'])->toBe(1234567890);
});

it('returns null for invalid base64 payload', function () {
    $result = AuthorizationController::decryptPayload('!!!invalid-base64!!!', 'some-key');
    expect($result)->toBeNull();
});

it('returns null for payload too short', function () {
    $result = AuthorizationController::decryptPayload('c2hvcnQ=', 'some-key');
    expect($result)->toBeNull();
});

it('returns null for tampered payload', function () {
    $key = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    $data = [
        'omni_id' => 1,
        'name' => 'Test',
        'email' => 'test@example.com',
        'timestamp' => 1000,
    ];

    $encrypted = AuthorizationController::encryptPayload($data, $key);

    $tampered = substr($encrypted, 0, -5).'XXXXX';

    $result = AuthorizationController::decryptPayload($tampered, $key);
    expect($result)->toBeNull();
});

it('returns null for payload with missing required fields', function () {
    $key = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    $data = ['name' => 'No ID'];
    $encrypted = AuthorizationController::encryptPayload($data, $key);

    $result = AuthorizationController::decryptPayload($encrypted, $key);
    expect($result)->toBeNull();
});

it('returns null when decryption fails', function () {
    $result = AuthorizationController::decryptPayload(
        base64_encode(random_bytes(32)),
        'some-key'
    );
    expect($result)->toBeNull();
});
