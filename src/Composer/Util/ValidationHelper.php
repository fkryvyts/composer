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

        if (!$errors && !$publishErrors && !$warnings) {
            $io->write('<info>' . $file . ' is valid</info>');
        } elseif (!$errors && !$publishErrors) {
            $io->writeError('<info>' . $file . ' is valid, but with a few warnings</info>');
            $io->writeError('<warning>See https://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
        }  elseif (!$errors) {
            $io->writeError('<info>' . $file . ' is valid for simple usage with composer but has</info>');
            $io->writeError('<info>strict errors that make it unable to be published as a package:</info>');
            $io->writeError('<warning>See https://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
        } else {
            $io->writeError('<error>' . $file . ' is invalid, the following errors/warnings were found:</error>');
        }

        $messages = array(
            'error' => array_merge($errors, $publishErrors),
            'warning' => $warnings,
        );

        foreach ($messages as $style => $msgs) {
            foreach ($msgs as $msg) {
                $io->writeError('<' . $style . '>' . $msg . '</' . $style . '>');
            }
        }

        return !$errors;
    }
}