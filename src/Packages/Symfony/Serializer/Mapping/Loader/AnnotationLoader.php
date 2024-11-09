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

use Doctrine\Common\Annotations\Reader;
use PayNL\Sdk\Packages\Symfony\Serializer\Annotation\DiscriminatorMap;
use PayNL\Sdk\Packages\Symfony\Serializer\Annotation\Groups;
use PayNL\Sdk\Packages\Symfony\Serializer\Annotation\MaxDepth;
use PayNL\Sdk\Packages\Symfony\Serializer\Annotation\SerializedName;
use PayNL\Sdk\Packages\Symfony\Serializer\Exception\MappingException;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\AttributeMetadata;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\ClassDiscriminatorMapping;
use PayNL\Sdk\Packages\Symfony\Serializer\Mapping\ClassMetadataInterface;

/**
 * Annotation loader.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AnnotationLoader implements LoaderInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        $reflectionClass = $classMetadata->getReflectionClass();
        $className = $reflectionClass->name;
        $loaded = false;

        $attributesMetadata = $classMetadata->getAttributesMetadata();

        foreach ($this->reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof DiscriminatorMap) {
                $classMetadata->setClassDiscriminatorMapping(new ClassDiscriminatorMapping(
                    $annotation->getTypeProperty(),
                    $annotation->getMapping()
                ));
            }
        }

        foreach ($reflectionClass->getProperties() as $property) {
            if (!isset($attributesMetadata[$property->name])) {
                $attributesMetadata[$property->name] = new AttributeMetadata($property->name);
                $classMetadata->addAttributeMetadata($attributesMetadata[$property->name]);
            }

            if ($property->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Groups) {
                        foreach ($annotation->getGroups() as $group) {
                            $attributesMetadata[$property->name]->addGroup($group);
                        }
                    } elseif ($annotation instanceof MaxDepth) {
                        $attributesMetadata[$property->name]->setMaxDepth($annotation->getMaxDepth());
                    } elseif ($annotation instanceof SerializedName) {
                        $attributesMetadata[$property->name]->setSerializedName($annotation->getSerializedName());
                    }

                    $loaded = true;
                }
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->name !== $className) {
                continue;
            }

            $accessorOrMutator = preg_match('/^(get|is|has|set)(.+)$/i', $method->name, $matches);
            if ($accessorOrMutator) {
                $attributeName = lcfirst($matches[2]);

                if (isset($attributesMetadata[$attributeName])) {
                    $attributeMetadata = $attributesMetadata[$attributeName];
                } else {
                    $attributesMetadata[$attributeName] = $attributeMetadata = new AttributeMetadata($attributeName);
                    $classMetadata->addAttributeMetadata($attributeMetadata);
                }
            }

            foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
                if ($annotation instanceof Groups) {
                    if (!$accessorOrMutator) {
                        throw new MappingException(sprintf('Groups on "%s::%s()" cannot be added. Groups can only be added on methods beginning with "get", "is", "has" or "set".', $className, $method->name));
                    }

                    foreach ($annotation->getGroups() as $group) {
                        $attributeMetadata->addGroup($group);
                    }
                } elseif ($annotation instanceof MaxDepth) {
                    if (!$accessorOrMutator) {
                        throw new MappingException(sprintf('MaxDepth on "%s::%s()" cannot be added. MaxDepth can only be added on methods beginning with "get", "is", "has" or "set".', $className, $method->name));
                    }

                    $attributeMetadata->setMaxDepth($annotation->getMaxDepth());
                } elseif ($annotation instanceof SerializedName) {
                    if (!$accessorOrMutator) {
                        throw new MappingException(sprintf('SerializedName on "%s::%s()" cannot be added. SerializedName can only be added on methods beginning with "get", "is", "has" or "set".', $className, $method->name));
                    }

                    $attributeMetadata->setSerializedName($annotation->getSerializedName());
                }

                $loaded = true;
            }
        }

        return $loaded;
    }
}
