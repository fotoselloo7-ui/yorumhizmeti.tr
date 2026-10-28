<?php
declare(strict_types=1);
require dirname(__DIR__).'/app/Core/Controller.php';
require dirname(__DIR__).'/app/Controllers/ScriptCheckoutController.php';

$check = (new ReflectionClass(\App\Controllers\ScriptCheckoutController::class))->newInstanceWithoutConstructor();
$method = new ReflectionMethod(\App\Controllers\ScriptCheckoutController::class, 'installationFields');
function assertFields(bool $cond, string $message): void {
    if (!$cond) throw new RuntimeException($message);
}
function expectInvalid(ReflectionMethod $method, object $object, array $input, string $label): void {
    try {
        $method->invoke($object, $input);
    } catch (InvalidArgumentException $e) {
        return;
    }
    throw new RuntimeException('Rejected input unexpectedly accepted: '.$label);
}
$valid = [
    'install_domain_mode' => 'existing',
    'install_domain' => 'ornekfirma.com',
    'install_hosting_mode' => 'existing',
    'install_hosting_provider' => 'Turhost',
    'install_hosting_panel' => 'https://panel.example.com',
    'install_hosting_username' => 'panel_user',
    'install_notes' => 'Alt domain altına kurulsun.',
];
$fields = $method->invoke($check, $valid);
$map = array_column($fields, 2, 0);
assertFields(count($fields) === 7, 'Seven installation fields are required for full input');
assertFields($map['nv_install_domain'] === 'ornekfirma.com', 'Domain not persisted');
assertFields($map['nv_install_hosting_provider'] === 'Turhost', 'Hosting not persisted');
assertFields(!array_key_exists('password', $map), 'Password may never be part of installation fields');

$needHelp = [
    'install_domain_mode' => 'need_domain',
    'install_hosting_mode' => 'need_hosting',
    'install_domain' => 'should-be-discarded.com',
    'install_hosting_provider' => 'Discarded',
    'install_hosting_username' => 'Discarded',
    'install_hosting_panel' => 'https://unwanted.example',
    'install_notes' => 'Yeni alan adı ve hosting ihtiyacım var.',
];
$requested = $method->invoke($check, $needHelp);
assertFields(count($requested) === 3, 'Unused fields must be discarded for assistance requests');
expectInvalid($method, $check, array_merge($valid, ['install_domain' => 'http://example.com']), 'Domain with scheme');
expectInvalid($method, $check, array_merge($valid, ['install_domain' => 'localhost']), 'Localhost domain');
expectInvalid($method, $check, array_merge($valid, ['install_hosting_provider' => '']), 'Missing provider');
expectInvalid($method, $check, array_merge($valid, ['install_hosting_panel' => 'http://panel.example.com']), 'Insecure http panel');
expectInvalid($method, $check, array_merge($valid, ['install_hosting_panel' => 'https://user:pass@panel.example.com']), 'URL embedded password');
expectInvalid($method, $check, array_merge($valid, ['install_notes' => 'Şifre: 123456']), 'Shared hosting password');
expectInvalid($method, $check, array_merge($valid, ['install_notes' => str_repeat('x', 501)]), 'Oversized note');
echo "NetVera script setup form validations: OK\n";
