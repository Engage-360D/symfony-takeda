<?php

namespace Engage360d\Bundle\TakedaBundle;

use FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface;
use Elastica\Document;

function rip_tags($string) {
    // ----- remove HTML TAGs -----
    $string = preg_replace ('/<[^>]*>/', ' ', $string);

    // ----- remove control characters -----
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space

    // ----- remove multiple spaces -----
    $string = trim(preg_replace('/ {2,}/', ' ', $string));

    return $string;
}

class PageToElasticaTransformer implements ModelToElasticaTransformerInterface
{
    public function transform($object, array $fields)
    {
        $content = "";
        $pageBlocks = $object->getPageBlocks();

        foreach ($pageBlocks as $pageBlock) {
            $json = json_decode($pageBlock->getJson());
            switch ($pageBlock->getType()) {
                case 'sonata.block.service.text':
                case 'engage360d_takeda.block.good_to_know':
                    $content .= " " . rip_tags($json->content);
                    break;
            }
        }

        $doc = new Document(
            $object->getId(),
            [
                'title' => $object->getTitle(),
                'content' => $content
            ]
        );

        return $doc;
    }
}
