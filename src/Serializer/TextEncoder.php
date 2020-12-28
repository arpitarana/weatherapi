<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Yaml\Yaml;

class TextEncoder implements EncoderInterface
{
    public function encode($data, $format, $context = [])
    {
        return implode($data, "\n");
    }

    public function supportsEncoding($format)
    {
        return 'text' === $format;
    }
}
