<?php
/**
 * Summary of namespace App\Providers
 */
namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class PassportServiceProvider extends ServiceProvider
{
    // public function boot()
    // {
    //     $this->generateKeyFiles();
    //     $keyPath = storage_path('app/oauth-keys');
    //     \Log::info("OAuth keys path: " . $keyPath);

    //     if (!file_exists($keyPath)) {
    //         \Log::error("OAuth keys directory does not exist");
    //         mkdir($keyPath, 0755, true);
    //     }
    //     \Log::info("Raw private key content: \n" . config('auth.passport.private_key'));
    //     \Log::info("Raw public key content: \n" . config('auth.passport.public_key'));
    //     Passport::loadKeysFrom($keyPath);
    // }

    // private function generateKeyFiles()
    // {
    //     $privateKey = $this->decodeKey(config('auth.passport.private_key'), 'RSA PRIVATE KEY');
    //     $publicKey = $this->decodeKey(config('auth.passport.public_key'), 'PUBLIC KEY');

    //     Storage::put('oauth-keys/oauth-private.key', $privateKey);
    //     Storage::put('oauth-keys/oauth-public.key', $publicKey);

    //     $privateKeyPath = Storage::path('oauth-keys/oauth-private.key');
    //     $publicKeyPath = Storage::path('oauth-keys/oauth-public.key');

    //     chmod($privateKeyPath, 0600);
    //     chmod($publicKeyPath, 0600);

    //     \Log::info("Private key file generated: " . $privateKeyPath);
    //     \Log::info("Public key file content: \n" . Storage::get('oauth-keys/oauth-private.key'));
    //     \Log::info("Public key file generated: " . $publicKeyPath);
    //     \Log::info("Public key file content: \n" . Storage::get('oauth-keys/oauth-public.key'));

    // }

    // private function decodeKey($base64Key, $keyType)
    // {
    //     if (!$base64Key) {
    //         \Log::error("Base64 key is empty for key type: " . $keyType);
    //         return null;
    //     }

    //     $key = base64_decode($base64Key);
    //     if (!$key) {
    //         \Log::error("Failed to decode base64 key for key type: " . $keyType);
    //         return null;
    //     }

    //     $key = trim($key);
    //     $key = chunk_split($key, 64, "\n");
    //     return "-----BEGIN {$keyType}-----\n{$key}-----END {$keyType}-----\n";


    // }
}