<?php

namespace Engage360d\Bundle\TakedaBundle\Composer;

use Symfony\Component\Yaml\Parser;

class ScriptHandler
{
    public static function generateSSHKeys()
    {
        $parametersFile = 'app/config/parameters.yml';

        $yamlParser = new Parser();
        $expectedValues = $yamlParser->parse(file_get_contents($parametersFile));
        if (!isset($expectedValues['parameters'])) {
            throw new \RuntimeException(sprintf('The file %s seems invalid.', $parametersFile));
        }

        $expectedParams = (array) $expectedValues['parameters'];

        $keysDir = $expectedParams['ssh_keys_dir'];
        $passphrase = $expectedParams['ssh_key.passphrase'];

        if (!$passphrase || strlen($passphrase) < 4) {
            throw new \InvalidArgumentException('Invalid ssh_keys.passphrase parameter value. It should be at least 4 characters long. Enter the valid value and rerun this command.');
        }

        if (!is_dir($keysDir)) {
            echo passthru(sprintf('mkdir -vp %s', $keysDir));
        }

        if (!is_file($keysDir . '/private.pem')) {
            echo passthru(sprintf('openssl genrsa -passout pass:%s -out %s/private.pem -aes256 4096', $passphrase, $keysDir));
        }

        if (is_file($keysDir . '/private.pem') && !is_file($keysDir . '/public.pem')) {
            echo passthru(sprintf('openssl rsa -pubout -in %2$s/private.pem -passin pass:%1$s -out %2$s/public.pem', $passphrase, $keysDir));
        }
    }
}
