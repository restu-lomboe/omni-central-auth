<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

uses(TestCase::class);

it('records an event to audit log', function () {
    $user = $this->regularUser();
    $this->actingAs($user);

    AuditLog::record('login', ['via' => 'sso']);

    expect(AuditLog::count())->toBe(1);

    $log = AuditLog::first();
    expect($log->event)->toBe('login');
    expect($log->user_id)->toBe($user->id);
    expect($log->metadata)->toBe(['via' => 'sso']);
});

it('records event without authenticated user (guest)', function () {
    AuditLog::record('login_failed');

    $log = AuditLog::first();
    expect($log->user_id)->toBeNull();
    expect($log->event)->toBe('login_failed');
});

it('prunes old logs based on retention policy', function () {
    config(['omni-central-auth.audit.retention_days' => 30]);

    // Log lama (> 30 hari)
    AuditLog::create([
        'event'       => 'login',
        'occurred_at' => now()->subDays(60),
    ]);

    // Log baru (< 30 hari)
    AuditLog::create([
        'event'       => 'login',
        'occurred_at' => now()->subDays(10),
    ]);

    $deleted = AuditLog::pruneOldLogs();

    expect($deleted)->toBe(1);
    expect(AuditLog::count())->toBe(1);
});

it('does not prune when retention is null', function () {
    config(['omni-central-auth.audit.retention_days' => null]);

    AuditLog::create(['event' => 'login', 'occurred_at' => now()->subDays(999)]);

    $deleted = AuditLog::pruneOldLogs();

    expect($deleted)->toBe(0);
    expect(AuditLog::count())->toBe(1);
});

it('can filter audit log by event in dashboard', function () {
    $admin = $this->adminUser();

    AuditLog::create(['event' => 'login', 'occurred_at' => now()]);
    AuditLog::create(['event' => 'logout', 'occurred_at' => now()]);
    AuditLog::create(['event' => 'login_failed', 'occurred_at' => now()]);

    $this->actingAs($admin)
        ->get(route('omni.dashboard.audit-log.index', ['event' => 'login']))
        ->assertOk()
        ->assertSee('login');
});
