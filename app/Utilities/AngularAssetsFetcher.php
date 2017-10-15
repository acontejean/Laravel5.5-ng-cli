<?php


namespace App\Utilities;


use App\Exceptions\NoData;
use Illuminate\Support\Collection;

class AngularAssetsFetcher
{
    private $data = [];

    private $filterFiles = [
        'inline',
        'main',
        'polyfills',
        'scripts',
        'styles',
        'vendor',
    ];

    /** @var Collection */
    private $files;

    public function __construct()
    {
        $this->fetchAssets();
    }

    private function fetchAssets()
    {
        $this->files = $this->filesInPath($this->getPathFromAngularCliFile());

        $this->getAssetForData();
    }

    private function getAssetForData()
    {
        foreach ($this->filterFiles as $filterFile) {
            $this->data[$filterFile] = $this->fetchAsset($filterFile);
        }
    }

    /**
     * @return string
     */
    private function getPathFromAngularCliFile(): string
    {
        return base_path($this->angularSettings()['apps'][0]['outDir']);
    }

    /**
     * @return array
     */
    private function angularSettings(): array
    {
        return json_decode(file_get_contents($this->angularCLIPath()), true);
    }

    /**
     * @return string
     */
    private function angularCLIPath(): string
    {
        return base_path('.angular-cli.json');
    }

    /**
     * @param string $path
     *
     * @return Collection
     */
    private function filesInPath(string $path): Collection
    {
        return collect(scandir($path));
    }

    /**
     * @param string $target
     *
     * @return string
     */
    private function fetchAsset(string $target): string
    {
        $filtered = $this->files->filter(function($file) use ($target) {
            return $this->isTheUsableFile($file, $target);
        });

        $this->validateFiltered($filtered, $target);

        return $filtered->first();
    }

    /**
     * @param string $file
     * @param string $prefix
     *
     * @return bool
     */
    private function isTheUsableFile(string $file, string $prefix): bool
    {
        return $this->startWithPrefix($file, $prefix) && $this->isNotMapFile($file);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    private function startWithPrefix(string $file, string $prefix): bool
    {
        return starts_with($file, $prefix);
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function isNotMapFile(string $file): bool
    {
        return !ends_with($file, '.map');
    }

    /**
     * @param $filtered Collection
     * @param $target
     */
    private function validateFiltered(Collection $filtered, string $target)
    {
        throw_if(!$filtered->count(), new NoData("File not found {$target} in {$this->getPathFromAngularCliFile()}."));
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get(string $name): string
    {
        if(!array_key_exists($name, $this->data)) return null;

        return $this->data[$name];
    }
}