<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PayNL\Sdk\Packages\Symfony\Serializer\Encoder;

use PayNL\Sdk\Packages\Symfony\Serializer\Exception\RuntimeException;
use PayNL\Sdk\Packages\Symfony\Yaml\Dumper;
use PayNL\Sdk\Packages\Symfony\Yaml\Parser;
use PayNL\Sdk\Packages\Symfony\Yaml\Yaml;

/**
 * Encodes YAML data.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class YamlEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT = 'yaml';
    private const ALTERNATIVE_FORMAT = 'yml';

    public const PRESERVE_EMPTY_OBJECTS = 'preserve_empty_objects';

    private $dumper;
    private $parser;
    private $defaultContext = ['yaml_inline' => 0, 'yaml_indent' => 0, 'yaml_flags' => 0];

    public function __construct(Dumper $dumper = null, Parser $parser = null, array $defaultContext = [])
    {
        if (!class_exists(Dumper::class)) {
            throw new RuntimeException('The YamlEncoder class requires the "Yaml" component. Install "symfony/yaml" to use it.');
        }

        $this->dumper = $dumper ?? new Dumper();
        $this->parser = $parser ?? new Parser();
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = [])
    {
        $context = array_merge($this->defaultContext, $context);

        if (isset($context[self::PRESERVE_EMPTY_OBJECTS])) {
            $context['yaml_flags'] |= Yaml::DUMP_OBJECT_AS_MAP;
        }

        return $this->dumper->dump($data, $context['yaml_inline'], $context['yaml_indent'], $context['yaml_flags']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format)
    {
        return self::FORMAT === $format || self::ALTERNATIVE_FORMAT === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = [])
    {
        $context = array_merge($this->defaultContext, $context);

        return $this->parser->parse($data, $context['yaml_flags']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format)
    {
        return self::FORMAT === $format || self::ALTERNATIVE_FORMAT === $format;
    }
}
