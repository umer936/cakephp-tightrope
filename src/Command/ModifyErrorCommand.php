<?php
declare(strict_types=1);

namespace CakePHPTightrope\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Modifies `ErrorController.php` to extend this plugin's `ErrorController` class.
 */
class ModifyErrorCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->info('Modifying error controller...');

        $file = $args->getArgument('file');
        if ($file === null) {
            $file = $this->_getDefaultFilePath();
        }

        if (!$this->_modifyError($file)) {
            $io->error("Could not modify `$file`.");
            $this->abort();
        }

        $io->success("Modified `$file`.");

        return static::CODE_SUCCESS;
    }

    /**
     * Modifies the view file at the given path.
     *
     * @param string $filePath The path of the file to modify.
     * @return bool
     */
    protected function _modifyError(string $filePath): bool
    {
        if (!$this->_isFile($filePath)) {
            return false;
        }

        $content = $this->_readFile($filePath);
        if ($content === false) {
            return false;
        }

//        $content = str_replace(
//            'use Cake\\View\\View',
//            'use CakePHPTightrope\\ErrorController',
//            $content
//        );
        $content = str_replace(
            'class ErrorController extends AppController',
            'class ErrorController extends \CakePHPTightrope\\Controller\\ErrorController',
            $content
        );
        $content = str_replace(
            "    public function beforeFilter(EventInterface \$event)\n    {\n",
            "    public function beforeFilter(EventInterface \$event)\n    {\n        parent::beforeFilter(\$event);\n",
            $content
        );

        return $this->_writeFile($filePath, $content);
    }

    /**
     * Checks whether the given path points to a file.
     *
     * @param string $filePath The file path.
     * @return bool
     */
    protected function _isFile(string $filePath): bool
    {
        return is_file($filePath);
    }

    /**
     * Reads a files contents.
     *
     * @param string $filePath The file path.
     * @return false|string
     */
    protected function _readFile(string $filePath)
    {
        return file_get_contents($filePath);
    }

    /**
     * Writes to a file.
     *
     * @param string $filePath The file path.
     * @param string $content The content to write.
     * @return bool
     */
    protected function _writeFile(string $filePath, string $content): bool
    {
        return file_put_contents($filePath, $content) !== false;
    }

    /**
     * Returns the default `ErrorController.php` file path.
     *
     * @return string
     */
    protected function _getDefaultFilePath(): string
    {
        return APP . 'Controller' . DS . 'ErrorController.php';
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(
                'Modifies `ErrorController.php` to extend this plugin\'s `ErrorController` class.'
            )
            ->addArgument('file', [
                'help' => sprintf(
                    'The path of the `ErrorController` file. Defaults to `%s`.',
                    $this->_getDefaultFilePath()
                ),
                'required' => false,
            ])
            ->setEpilog(
                '<warning>Don\'t run this command if you have a already modified the `ErrorController` class!</warning>'
            );
    }
}
