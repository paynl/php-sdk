<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PayNL\Sdk\Packages\Symfony\Serializer\Mapping\Loader;

use PayNL\Sdk\Packages\Symfony\Config\Util\XmlUtils;
use PayNL\Sdk\Packages\Symfony\Serializer\Exception\MappingException;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\AttributeMetadata;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\ClassDiscriminatorMapping;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\ClassMetadataInterface;

/**
 * Loads XML mapping files.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class XmlFileLoader extends FileLoader
{
    /**
     * An array of {@class \SimpleXMLElement} instances.
     *
     * @var \SimpleXMLElement[]|null
     */
    private $classes;

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromXml();
        }

        if (!$this->classes) {
            return false;
        }

        $attributesMetadata = $classMetadata->getAttributesMetadata();

        if (isset($this->classes[$classMetadata->getName()])) {
            $xml = $this->classes[$classMetadata->getName()];

            foreach ($xml->attribute as $attribute) {
                $attributeName = (string) $attribute['name'];

                if (isset($attributesMetadata[$attributeName])) {
                    $attributeMetadata = $attributesMetadata[$attributeName];
                } else {
                    $attributeMetadata = new AttributeMetadata($attributeName);
                    $classMetadata->addAttributeMetadata($attributeMetadata);
                }

                foreach ($attribute->group as $group) {
                    $attributeMetadata->addGroup((string) $group);
                }

                if (isset($attribute['max-depth'])) {
                    $attributeMetadata->setMaxDepth((int) $attribute['max-depth']);
                }

                if (isset($attribute['serialized-name'])) {
                    $attributeMetadata->setSerializedName((string) $attribute['serialized-name']);
                }
            }

            if (isset($xml->{'discriminator-map'})) {
                $mapping = [];
                foreach ($xml->{'discriminator-map'}->mapping as $element) {
                    $elementAttributes = $element->attributes();
                    $mapping[(string) $elementAttributes->type] = (string) $elementAttributes->class;
                }

                $classMetadata->setClassDiscriminatorMapping(new ClassDiscriminatorMapping(
                    (string) $xml->{'discriminator-map'}->attributes()->{'type-property'},
                    $mapping
                ));
            }

            return true;
        }

        return false;
    }

    /**
     * Return the names of the classes mapped in this file.
     *
     * @return string[] The classes names
     */
    public function getMappedClasses()
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromXml();
        }

        return array_keys($this->classes);
    }

    /**
     * Parses an XML File.
     *
     * @throws MappingException
     */
    private function parseFile(string $file): \SimpleXMLElement
    {
        try {
            $dom = XmlUtils::loadFile($file, __DIR__.'/schema/dic/serializer-mapping/serializer-mapping-1.0.xsd');
        } catch (\Exception $e) {
            throw new MappingException($e->getMessage(), $e->getCode(), $e);
        }

        return simplexml_import_dom($dom);
    }

    private function getClassesFromXml(): array
    {
        $xml = $this->parseFile($this->file);
        $classes = [];

        foreach ($xml->class as $class) {
            $classes[(string) $class['name']] = $class;
        }

        return $classes;
    }
}
