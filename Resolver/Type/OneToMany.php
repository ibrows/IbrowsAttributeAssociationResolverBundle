<?php

namespace Ibrows\AttributeAssociationResolver\Resolver\Type;

use Ibrows\AttributeAssociationResolver\Reader\AssociationMappingInfoInterface;
use Ibrows\AttributeAssociationResolver\Result\ResultBag;
use Symfony\Component\Console\Output\OutputInterface;

class OneToMany extends AbstractResolver
{
    /** @var ResultBag */
    protected $resultBag;

    /**
     * @param ResultBag $resultBag
     * @param AssociationMappingInfoInterface $mappingInfo
     * @param string $propertyName
     * @param mixed $entity
     * @param OutputInterface $output
     * @return ResolverInterface
     */
    public function resolveAssociation(
        ResultBag $resultBag,
        AssociationMappingInfoInterface $mappingInfo,
        $propertyName,
        $entity,
        OutputInterface $output
    )
    {
        $annotation = $mappingInfo->getAnnotation();
        $metaData = $mappingInfo->getMetaData();

        $this->resultBag = $resultBag;
        $this->resolve($resultBag, $entity, $propertyName, $annotation, $metaData);
    }

    /**
     * @param ResultBag $resultBag
     * @param $entity
     * @param $propertyName
     * @param \Ibrows\AttributeAssociationResolver\Attribute\OneToMany $annotation
     * @param $meta
     * @throws \Exception
     */
    protected function resolve(ResultBag $resultBag, $entity, $propertyName, \Ibrows\AttributeAssociationResolver\Attribute\OneToMany $annotation, $meta)
    {
        $valueFieldName = $annotation->getValueFieldName();
        $targetFieldName = $annotation->getTargetFieldName();

        $sourceCollectionFieldName = $propertyName;
        $sourceCollectionFieldAddMethod = $annotation->getCollectionAddFunctionName() ? $annotation->getCollectionAddFunctionName() : 'add'. ucfirst($sourceCollectionFieldName);
        $sourceCollectionFieldRemoveMethod = $annotation->getCollectionRemoveFunctionName() ? $annotation->getCollectionRemoveFunctionName() : 'remove'. ucfirst($sourceCollectionFieldName);
        $sourceCollectionFieldGetMethod = $annotation->getValueGetterName() ? $annotation->getValueGetterName() : 'get'. ucfirst($sourceCollectionFieldName);

        $sourceValueGetMethod = 'get'. ucfirst($valueFieldName);

        if ($annotation->getValueGetterName() != null) {
            $sourceValueGetMethod = $annotation->getValueGetterName();
        }

        $this->checkIfMethodsExists(array($sourceCollectionFieldGetMethod), $entity);
        $this->checkIfMethodsExists(array($sourceCollectionFieldAddMethod), $entity);
        $this->checkIfMethodsExists(array($sourceCollectionFieldRemoveMethod), $entity);
        $this->checkIfMethodsExists(array($sourceValueGetMethod), $entity);

        $valueFieldValues = $entity->$sourceValueGetMethod();

        if (!is_array($valueFieldValues)) {
            throw new \Exception("Expecting array as returnobject of ".$sourceValueGetMethod."()");
        }

        $em = $this->entityManager;

        $targetRepo = $em->getRepository($meta['targetEntity']);

        $targetsEntities = array();
        $delta = false;
        foreach ($valueFieldValues as $value) {
            $targets = $targetRepo->findBy(array(
                $targetFieldName => $value
            ));

            foreach ($targets as $target) {
                $targetsEntities[] = $target;
                $setEntityMethod = 'set'.ucfirst($meta['mappedBy']);
                $getEntityMethod = 'get'.ucfirst($meta['mappedBy']);
                $this->checkIfMethodsExists(array($setEntityMethod, $getEntityMethod), $target);
                if ($target->$getEntityMethod() != $entity) {
                    $target->$setEntityMethod($entity);
                    $em->persist($target);
                    $delta = true;
                }
            }
        }

        if (!$this->syncCollection($resultBag, $entity, $sourceCollectionFieldAddMethod, $sourceCollectionFieldRemoveMethod, $entity->$sourceCollectionFieldGetMethod(), $targetsEntities)) {
            $resultBag->addSkipped($entity);
        } else {
            $delta = true;
            $resultBag->addChanged($entity);
        }

        if ($delta) {
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * @param ResultBag $resultBag
     * @param $entity
     * @param $addMethod
     * @param $removeMethod
     * @param $collection
     * @param $newcollection
     * @return bool
     */
    protected function syncCollection(ResultBag $resultBag, $entity, $addMethod, $removeMethod, $collection, $newcollection)
    {
        $_collection = array();
        if (!is_array($collection)) {
            foreach ($collection as $entry) {
                $_collection[] = $entry;
            }
        }
        $collection = $_collection;
        $delta = false;

        foreach ($collection as $entry) {
            if (!in_array($entry, $newcollection)) {
                $entity->$removeMethod($entry);
                $delta = true;
            }
        }

        foreach ($newcollection as $entry) {
            if (!in_array($entry, $collection)) {
                $entity->$addMethod($entry);
                $delta = true;
            }
        }

        return $delta;
    }
}
