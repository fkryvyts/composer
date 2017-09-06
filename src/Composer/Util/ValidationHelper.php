<?php

namespace Composer\Util;

use Composer\IO\IOInterface;
use Composer\Package\Loader\ValidatingArrayLoader;

class ValidationHelper
{
    /**
     * @param string $file
     * @param IOInterface $io
     * @return bool
     */
    public function validateComposerFile($file, IOInterface $io)
    {
        $validator = new ConfigValidator($io);
        $checkAll = ValidatingArrayLoader::CHECK_ALL;
        list($errors, $publishErrors, $warnings) = $validator->validate($file, $checkAll);

        if ($errors){
            $io->writeError('<error>' . $file . ' is invalid, the following errors/warnings were found:</error>');
        } elseif ($publishErrors) {
            $io->writeError('<warning>Few strict errors were found during validation of ' . $file . '</warning>');
            $io->writeError('<warning>These errors may result in unexpected behavior of installation process</warning>');
            $io->writeError('<warning>See https://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
        } elseif ($warnings) {
            $io->writeError('<warning>Few warnings were found during validation of ' . $file . '</warning>');
            $io->writeError('<warning>These errors may result in unexpected behavior</warning>');
            $io->writeError('<warning>of installation process</warning>');
            $io->writeError('<warning>See https://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
        }

        foreach (array_merge($errors, $publishErrors) as $msg) {
            $io->writeError('<error>' . $msg . '</error>');
        }

        foreach ($warnings as $msg) {
            $io->writeError('<warning>' . $msg . '</warning>');
        }

        return !$errors;
    }
}