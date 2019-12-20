<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class CollectionPhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Model','ResourceModel',$data->getModelName()]);
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection',
            $data->getInterfaceInstance(),
            $data->getInstance(),
            $data->getResourceModelName() => $data->getResourceModelInstance()
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        $class = $this->phpBuilder->class('Collection')
            ->extend('AbstractCollection')
            ->addStmt($this->phpBuilder->property('_idFieldName')->makeProtected()
                ->setDefault($this->phpBuilder->classConstFetch($data->getResourceModelName(), 'ENTITY_ID'))
            )
            ->addStmt($this->phpBuilder->property('_eventPrefix')->makeProtected()
                ->setDefault($data->getModuleName()->getSlug([$data->getModelName(), 'collection']))
            )
            ->addStmt($this->phpBuilder->property('_eventObject')->makeProtected()
                ->setDefault($data->getModelVarName().'_collection')
            )
            ->addStmt($this->phpBuilder->method('_construct')->makeProtected()
                ->addStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), '_init', [
                    $this->phpBuilder->classConstFetch($data->getModelName(), 'class'),
                    $this->phpBuilder->classConstFetch($data->getResourceModelName(), 'class')
                ]))
            );
        return $class;
    }
}