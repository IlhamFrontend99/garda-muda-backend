namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pengalihan storage untuk Lingkungan Vercel Serverless
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            $storagePath = '/tmp/storage';
            if (!file_exists($storagePath)) {
                mkdir($storagePath . '/framework/views', 0755, true);
                mkdir($storagePath . '/framework/cache', 0755, true);
                mkdir($storagePath . '/framework/sessions', 0755, true);
            }
            $this->app->useStoragePath($storagePath);
            config(['view.compiled' => '/tmp/storage/framework/views']);
        }
    }
}