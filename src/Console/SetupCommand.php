<?php

namespace Canvas\Console;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class SetupCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'canvas:setup {--data : Specifies that demo data should be seeded}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic blog views and routes';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'layouts/app.stub'     => 'layouts/app.blade.php',
        'partials/navbar.stub' => 'partials/navbar.blade.php',
        'partials/styles.stub' => 'partials/styles.blade.php',
        'index.stub'           => 'index.blade.php',
        'show.stub'            => 'show.blade.php',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();
        $this->exportViews();
        $this->exportController();
        $this->registerRoutes();

        // Optionally seed the database with demo data
        if ($this->option('data')) {
            $this->seed();
        }

        $this->info('Setup complete. Head over to <comment>' . url('/blog') . '</comment> to get started.');
    }

    /**
     * Create the view directories.
     *
     * @return void
     */
    private function createDirectories()
    {
        if (!is_dir($directory = resource_path('views/blog/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/blog/partials'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the default blog views.
     *
     * @return void
     */
    private function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/blog/' . $value))) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                sprintf('%s/stubs/views/blog/%s', dirname(__DIR__, 2), $key),
                $view
            );
        }
    }

    /**
     * Compile the default controller stub.
     *
     * @return string
     */
    private function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(dirname(__DIR__, 2) . '/stubs/controllers/BlogController.stub')
        );
    }

    /**
     * Export the new controller.
     *
     * @return void
     */
    private function exportController()
    {
        file_put_contents(
            app_path('Http/Controllers/BlogController.php'),
            $this->compileControllerStub()
        );
    }

    /**
     * Register the new routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(dirname(__DIR__, 2) . '/stubs/routes.stub'),
            FILE_APPEND
        );
    }

    private function seed()
    {
        // todo: implement the seed() method
    }
}
