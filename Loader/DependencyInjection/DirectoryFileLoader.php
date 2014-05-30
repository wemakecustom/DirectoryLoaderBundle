<?php

namespace WMC\DirectoryLoaderBundle\Loader\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\FileLoader;

class DirectoryFileLoader extends FileLoader
{
    /**
     * @param mixed  $file The resource
     * @param string $type The resource type
     */
    public function load($file, $type = null)
    {
        $file = rtrim($file, '/');
        $path = $this->locator->locate($file);

        foreach (scandir($path) as $dir) {
            if ($dir[0] !== '.') {
                if (is_dir("$path/$dir")) {
                    $dir .= '/'; // append / to allow recursion
                }

                $this->setCurrentDir($path);

                $this->import($dir, null, false, $path);
            }
        }
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return bool    true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'directory' === $type || (!$type && preg_match('/\/$/', $resource) === 1); // ends with a slash
    }
}
