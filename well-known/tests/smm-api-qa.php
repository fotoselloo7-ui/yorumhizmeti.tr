<?php
/**
 * Offline smoke QA: no real provider key, DB or external API request is used.
 * php well-known/tests/smm-api-qa.php
 */
require_once dirname(__DIR__).'/app/Services/SmmApiClient.php';
use App\Services\SmmApiClient;

$passed=0;
$check=static function(bool $ok,string $message) use (&$passed): void {
    if (!$ok) throw new \RuntimeException('FAIL: '.$message);
    $passed++;
};
$_ENV['SMM_ENCRYPTION_KEY']='base64:'.base64_encode(random_bytes(32));
$secret='qa_provider_secret_'.bin2hex(random_bytes(10));
$a=SmmApiClient::encrypt($secret);
$b=SmmApiClient::encrypt($secret);
$check($a!==$b,'AES-GCM IV uniqueness');
$check(SmmApiClient::decrypt($a)===$secret,'API key roundtrip');
$check(!str_contains($a,$secret),'no plaintext key');
$corrupted=$a;
$corrupted[strlen($corrupted)-2]=$corrupted[strlen($corrupted)-2]==='A'?'B':'A';
try { SmmApiClient::decrypt($corrupted); $bad=false; } catch (\RuntimeException $e) { $bad=true; }
$check($bad,'tampered ciphertext rejected');
$check(SmmApiClient::validateEndpoint('https://provider.example/api/v2')==='https://provider.example/api/v2','valid endpoint');
foreach (['http://provider.example/api/v2','https://127.0.0.1/api/v2',
          'https://localhost/api/v2','https://user:pass@provider.example/api/v2',
          'https://provider.example/api/v2?key=secret',
          'https://provider.example:8443/api/v2'] as $badUrl) {
    try { SmmApiClient::validateEndpoint($badUrl); $rejected=false; }
    catch (\InvalidArgumentException $e) { $rejected=true; }
    $check($rejected,'unsafe URL rejected: '.$badUrl);
}
$client=new SmmApiClient('https://provider.example/api/v2',$secret);
try { $client->call('random_action'); $bad=false; }
catch (\InvalidArgumentException $e) { $bad=true; }
$check($bad,'unknown API operation denied');
echo "PASS: {$passed} offline SMM security smoke checks\n";
